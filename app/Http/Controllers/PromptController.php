<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Prompt;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Collection;

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
        $collections = Collection::where('user_id', auth()->id())->get();
        return view('prompts.create', compact('categories', 'tags', 'collections'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:prompts',
            'category_id' => 'required|exists:categories,id',
            'prompt_text' => 'required|string',
            'description' => 'nullable|string',
            'language' => 'nullable|string',
            'tone' => 'nullable|string',
            'is_template' => 'boolean',
            'variables_schema' => 'nullable|string', // JSON string or array
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'collections' => 'array',
            'collections.*' => 'exists:collections,id',
        ]);

        if ($request->is_template) {
           // Handle variables_schema logic if needed (validation of JSON)
        }

        $validated['user_id'] = auth()->id();
        $prompt = Prompt::create($validated);

        if ($request->has('tags')) {
            $prompt->tags()->sync($request->tags);
        }
        
        if ($request->has('collections')) {
             $prompt->collections()->sync($request->collections);
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
        $collections = Collection::where('user_id', auth()->id())->get();
        return view('prompts.edit', compact('prompt', 'categories', 'tags', 'collections'));
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
            'slug' => 'required|string|max:255|unique:prompts,slug,' . $prompt->id,
            'category_id' => 'required|exists:categories,id',
            'prompt_text' => 'required|string',
            'description' => 'nullable|string',
            'language' => 'nullable|string',
            'tone' => 'nullable|string',
            'is_template' => 'boolean',
            'variables_schema' => 'nullable|string',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
             'collections' => 'array',
            'collections.*' => 'exists:collections,id',
        ]);

        $prompt->update($validated);

         if ($request->has('tags')) {
            $prompt->tags()->sync($request->tags);
        }
        
         if ($request->has('collections')) {
             $prompt->collections()->sync($request->collections);
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
}
