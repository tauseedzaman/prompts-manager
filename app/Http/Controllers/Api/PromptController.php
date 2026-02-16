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
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Filter by collection
        if ($request->has('collection_id')) {
            $query->whereHas('collections', function ($q) use ($request) {
                $q->where('collections.id', $request->input('collection_id'));
            });
        }

        // Filter by tag
        if ($request->has('tag_id')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->input('tag_id'));
            });
        }

        $prompts = $query->with(['category', 'collections', 'tags'])
                         ->latest()
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
            'content' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'collection_ids' => 'nullable|array',
            'collection_ids.*' => 'exists:collections,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
        ]);

        $prompt = $request->user()->prompts()->create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category_id' => $validated['category_id'] ?? null,
        ]);

        // Attach collections
        if (!empty($validated['collection_ids'])) {
            $prompt->collections()->attach($validated['collection_ids']);
        }

        // Attach tags
        if (!empty($validated['tag_ids'])) {
            $prompt->tags()->attach($validated['tag_ids']);
        }

        return response()->json($prompt->load(['category', 'collections', 'tags']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $prompt = $request->user()->prompts()->with(['category', 'collections', 'tags'])->findOrFail($id);
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
            'content' => 'sometimes|required|string',
            'category_id' => 'nullable|exists:categories,id',
            'collection_ids' => 'nullable|array',
            'collection_ids.*' => 'exists:collections,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
        ]);

        $prompt->update([
            'title' => $validated['title'] ?? $prompt->title,
            'content' => $validated['content'] ?? $prompt->content,
            'category_id' => $validated['category_id'] ?? $prompt->category_id,
        ]);

        // Sync collections
        if (isset($validated['collection_ids'])) {
            $prompt->collections()->sync($validated['collection_ids']);
        }

        // Sync tags
        if (isset($validated['tag_ids'])) {
            $prompt->tags()->sync($validated['tag_ids']);
        }

        return response()->json($prompt->load(['category', 'collections', 'tags']));
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
}
