<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prompt;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PromptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->user()->prompts();

        // Search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('prompt_text', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Filter by tag
        if ($request->has('tag_id')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->input('tag_id'));
            });
        }

        // Filter by favorites
        if ($request->has('is_favorite')) {
            $query->where('is_favorite', $request->boolean('is_favorite'));
        }

        // Sorting
        if ($request->input('sort') === 'most_used') {
            $query->mostUsed();
        } else {
            $query->latest();
        }

        $prompts = $query->with(['category', 'tags'])
                         ->paginate($request->input('per_page', 15));

        return response()->json($prompts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'prompt_text' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'category_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'language' => 'nullable|string|max:10',
            'tone' => 'nullable|string|max:50',
            'usage_type' => 'nullable|string|max:50',
            'is_template' => 'nullable|boolean',
            'variables_schema' => 'nullable|array',
            'example_input' => 'nullable|array',
            'example_output' => 'nullable|string',
            'source' => 'nullable|string|max:255',
            'visibility' => 'nullable|string|in:public,private',
            'is_favorite' => 'nullable|boolean',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
            'tag_names' => 'nullable|array',
            'tag_names.*' => 'string|max:50',
        ]);

        $categoryId = $validated['category_id'] ?? null;
        
        // Handle auto-creation of category
        if (!$categoryId && !empty($validated['category_name'])) {
            $category = $request->user()->categories()->firstOrCreate(
                ['name' => $validated['category_name']],
                ['slug' => \Illuminate\Support\Str::slug($validated['category_name'])]
            );
            $categoryId = $category->id;
        }

        $slug = \Illuminate\Support\Str::slug($validated['title']);
        $baseSlug = $slug;
        $count = 1;
        while ($request->user()->prompts()->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count++;
        }

        // Extract variables
        $variables = $this->extractVariables($validated['prompt_text']);

        $prompt = $request->user()->prompts()->create([
            'title' => $validated['title'],
            'slug' => $slug,
            'prompt_text' => $validated['prompt_text'],
            'category_id' => $categoryId,
            'description' => $validated['description'] ?? null,
            'language' => $validated['language'] ?? 'en',
            'tone' => $validated['tone'] ?? null,
            'usage_type' => $validated['usage_type'] ?? null,
            'is_template' => $validated['is_template'] ?? !empty($variables),
            'variables_schema' => $validated['variables_schema'] ?? $variables,
            'example_input' => $validated['example_input'] ?? null,
            'example_output' => $validated['example_output'] ?? null,
            'source' => $validated['source'] ?? null,
            'visibility' => $validated['visibility'] ?? 'private',
            'is_favorite' => $validated['is_favorite'] ?? false,
        ]);

        // Attach tags by ID
        if (!empty($validated['tag_ids'])) {
            $prompt->tags()->attach($validated['tag_ids']);
        }

        // Attach tags by Name (auto-create)
        if (!empty($validated['tag_names'])) {
            $tagIds = [];
            foreach ($validated['tag_names'] as $tagName) {
                $tag = $request->user()->tags()->firstOrCreate(
                    ['name' => $tagName],
                    ['slug' => \Illuminate\Support\Str::slug($tagName)]
                );
                $tagIds[] = $tag->id;
            }
            $prompt->tags()->syncWithoutDetaching($tagIds);
        }

        return response()->json($prompt->load(['category', 'tags']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $prompt = $request->user()->prompts()->with(['category', 'tags'])->findOrFail($id);
        return response()->json($prompt);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $prompt = $request->user()->prompts()->findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'prompt_text' => 'sometimes|required|string',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'language' => 'nullable|string|max:10',
            'tone' => 'nullable|string|max:50',
            'usage_type' => 'nullable|string|max:50',
            'is_template' => 'nullable|boolean',
            'variables_schema' => 'nullable|array',
            'example_input' => 'nullable|array',
            'example_output' => 'nullable|string',
            'source' => 'nullable|string|max:255',
            'visibility' => 'nullable|string|in:public,private',
            'is_favorite' => 'nullable|boolean',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
        ]);

        $prompt->update(array_merge(
            $request->only([
                'title', 'prompt_text', 'category_id', 'description', 
                'language', 'tone', 'usage_type', 'is_template', 
                'variables_schema', 'example_input', 'example_output', 
                'source', 'visibility', 'is_favorite'
            ]),
            ['prompt_text' => $validated['prompt_text'] ?? $prompt->prompt_text]
        ));



        // Sync tags
        if (isset($validated['tag_ids'])) {
            $prompt->tags()->sync($validated['tag_ids']);
        }

        return response()->json($prompt->load(['category', 'tags']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $prompt = $request->user()->prompts()->findOrFail($id);
        $prompt->delete();

        return response()->json(['message' => 'Prompt deleted successfully'], 200);
    }

    public function incrementUsage(Request $request, string $id)
    {
        $prompt = Prompt::findOrFail($id);
        
        // Allowed if owner or if it's public
        if ($prompt->user_id !== $request->user()->id && $prompt->visibility !== 'public') {
            abort(403);
        }

        $prompt->increment('usage_count');

        return response()->json([
            'message' => 'Usage incremented',
            'usage_count' => $prompt->usage_count
        ]);
    }

    private function extractVariables($text)
    {
        preg_match_all('/\{\{(.+?)\}\}/', $text, $matches);
        return array_values(array_unique($matches[1] ?? []));
    }
}
