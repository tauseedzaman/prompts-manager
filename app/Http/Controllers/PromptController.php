<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Prompt;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Str;

class PromptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Prompt::where('user_id', auth()->id())->with(['category', 'tags']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('prompt_text', 'like', "%{$search}%");
            });
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('tag_id')) {
            $query->whereHas('tags', function($q) use ($request) {
                $q->where('tags.id', $request->tag_id);
            });
        }

        $prompts = $query->latest()->paginate(10);
        $categories = Category::all();

        return view('prompts.index', compact('prompts', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('user_id', auth()->id())->where('is_active', true)->get();
        $tags = Tag::where('user_id', auth()->id())->get();
        return view('prompts.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'prompt_text' => 'required|string',
            'description' => 'nullable|string',
            'language' => 'nullable|string',
            'tone' => 'nullable|string',
            'is_template' => 'boolean',
            'variables_schema' => 'nullable|string', // JSON string or array
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'variables_schema' => 'nullable|string', // JSON string or array
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']);
        
        // Ensure slug is unique for this user
        $baseSlug = $validated['slug'];
        $count = 1;
        while (Prompt::where('user_id', auth()->id())->where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $baseSlug . '-' . $count++;
        }

        $prompt = Prompt::create($validated);

        if ($request->has('tags')) {
            $prompt->tags()->sync($request->tags);
        }
        


        return redirect()->route('prompts.index')->with('success', 'Prompt created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Prompt $prompt)
    {
        if ($prompt->user_id !== auth()->id()) {
            abort(403);
        }
         return view('prompts.show', compact('prompt'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Prompt $prompt)
    {
        if ($prompt->user_id !== auth()->id()) {
            abort(403);
        }
        $categories = Category::where('user_id', auth()->id())->get();
        $tags = Tag::where('user_id', auth()->id())->get();
        return view('prompts.edit', compact('prompt', 'categories', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Prompt $prompt)
    {
        if ($prompt->user_id !== auth()->id()) {
            abort(403);
        }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'prompt_text' => 'required|string',
            'description' => 'nullable|string',
            'language' => 'nullable|string',
            'tone' => 'nullable|string',
            'is_template' => 'boolean',
            'variables_schema' => 'nullable|string',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
        ]);

        if ($prompt->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']);
            $baseSlug = $validated['slug'];
            $count = 1;
            while (Prompt::where('user_id', auth()->id())->where('slug', $validated['slug'])->where('id', '!=', $prompt->id)->exists()) {
                $validated['slug'] = $baseSlug . '-' . $count++;
            }
        }

        $prompt->update($validated);

         if ($request->has('tags')) {
            $prompt->tags()->sync($request->tags);
        }
        


        return redirect()->route('prompts.index')->with('success', 'Prompt updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prompt $prompt)
    {
        if ($prompt->user_id !== auth()->id()) {
            abort(403);
        }
        $prompt->delete();
        return redirect()->route('prompts.index')->with('success', 'Prompt deleted successfully.');
    }
    
    public function copy(Prompt $prompt)
    {
        // Log valid usage here via PromptRun if implemented
        return response()->json([
            'text' => $prompt->prompt_text,
            'message' => 'Copied to clipboard!'
        ]);
    }

    /**
     * Display a listing of favorited prompts.
     */
    public function favorites()
    {
        $prompts = \App\Models\Prompt::where('user_id', auth()->id())
            ->where('is_favorite', true)
            ->with(['category', 'tags'])
            ->latest()
            ->paginate(12);

        return view('prompts.favorites', compact('prompts'));
    }

    public function toggleFavorite(Prompt $prompt)
    {
        if ($prompt->user_id !== auth()->id()) {
            abort(403);
        }

        $prompt->is_favorite = !$prompt->is_favorite;
        $prompt->save();

        return response()->json([
            'is_favorite' => $prompt->is_favorite,
            'message' => $prompt->is_favorite ? 'Added to favorites!' : 'Removed from favorites!'
        ]);
    }
    public function export()
    {
        $prompts = Prompt::where('user_id', auth()->id())->with(['category', 'tags'])->get();
        
        $data = $prompts->map(function($prompt) {
            return [
                'title' => $prompt->title,
                'prompt_text' => $prompt->prompt_text,
                'description' => $prompt->description,
                'category' => $prompt->category ? $prompt->category->name : 'Uncategorized',
                'tags' => $prompt->tags->pluck('name')->toArray(),
                'language' => $prompt->language,
                'tone' => $prompt->tone,
                'usage_type' => $prompt->usage_type,
                'is_template' => $prompt->is_template,
                'variables_schema' => $prompt->variables_schema,
                'example_input' => $prompt->example_input,
                'example_output' => $prompt->example_output,
            ];
        });

        $filename = 'prompts_export_' . date('Y-m-d_H-i-s') . '.json';
        
        return response()->streamDownload(function() use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT);
        }, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function importPage()
    {
        return view('prompts.import');
    }

    public function downloadSample()
    {
        $sample = [
            [
                'title' => 'Sample Prompt Title',
                'prompt_text' => 'Write a creative story about a robot who learns to paint.',
                'description' => 'A creative writing prompt for AI.',
                'category' => 'Creative Writing',
                'tags' => ['creative', 'storytelling', 'robots'],
                'language' => 'English',
                'tone' => 'Creative',
                'usage_type' => 'Full Prompt',
                'is_template' => false,
            ],
            [
                'title' => 'Code Refactoring Template',
                'prompt_text' => 'Refactor the following {{language}} code for better performance: {{code}}',
                'description' => 'A template for code optimization.',
                'category' => 'Coding',
                'tags' => ['coding', 'optimization', 'refactoring'],
                'language' => 'English',
                'tone' => 'Professional',
                'usage_type' => 'Snippet',
                'is_template' => true,
                'variables_schema' => [
                    ['name' => 'language', 'type' => 'text', 'placeholder' => 'Python'],
                    ['name' => 'code', 'type' => 'textarea', 'placeholder' => 'Paste your code here']
                ],
            ]
        ];

        return response()->streamDownload(function() use ($sample) {
            echo json_encode($sample, JSON_PRETTY_PRINT);
        }, 'prompts_sample.json', [
            'Content-Type' => 'application/json',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:json|max:2048',
        ]);

        $fileContent = file_get_contents($request->file('file')->getRealPath());
        $data = json_decode($fileContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->back()->with('error', 'Invalid JSON file format.');
        }

        if (!is_array($data)) {
            return redirect()->back()->with('error', 'The JSON file must contain an array of prompts.');
        }

        $importCount = 0;
        try {
            foreach ($data as $index => $item) {
                // Basic Validation
                if (empty($item['title']) || empty($item['prompt_text'])) {
                    continue; // Skip invalid entries
                }

                // Find or create category
                $categoryName = $item['category'] ?? 'Uncategorized';
                $category = Category::firstOrCreate(
                    ['name' => $categoryName, 'user_id' => auth()->id()],
                    ['is_active' => true, 'color' => '#3b82f6', 'slug' => Str::slug($categoryName)]
                );

                // Create prompt data
                $promptData = [
                    'user_id' => auth()->id(),
                    'category_id' => $category->id,
                    'title' => $item['title'],
                    'slug' => Str::slug($item['title']),
                    'prompt_text' => $item['prompt_text'],
                    'description' => $item['description'] ?? null,
                    'language' => $item['language'] ?? 'English',
                    'tone' => $item['tone'] ?? 'Neutral',
                    'usage_type' => $item['usage_type'] ?? 'Full Prompt',
                    'is_template' => isset($item['is_template']) ? (bool)$item['is_template'] : false,
                    'variables_schema' => $item['variables_schema'] ?? null,
                    'example_input' => $item['example_input'] ?? null,
                    'example_output' => $item['example_output'] ?? null,
                    'status' => 'published',
                ];

                // Ensure unique slug for this user
                $baseSlug = $promptData['slug'];
                $count = 1;
                while (Prompt::where('user_id', auth()->id())->where('slug', $promptData['slug'])->exists()) {
                    $promptData['slug'] = $baseSlug . '-' . $count++;
                }

                $prompt = Prompt::create($promptData);

                // Handle tags
                if (!empty($item['tags']) && is_array($item['tags'])) {
                    $tagIds = [];
                    foreach ($item['tags'] as $tagName) {
                        $tag = Tag::firstOrCreate(
                            ['name' => $tagName, 'user_id' => auth()->id()],
                            ['slug' => Str::slug($tagName)]
                        );
                        $tagIds[] = $tag->id;
                    }
                    $prompt->tags()->sync($tagIds);
                }

                $importCount++;
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred during import: ' . $e->getMessage());
        }

        return redirect()->route('prompts.index')->with('success', "Successfully imported {$importCount} prompts.");
    }
}
