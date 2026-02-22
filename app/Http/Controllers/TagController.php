<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use Illuminate\Support\Str;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = Tag::where('user_id', auth()->id())->get();

        $allSuggestions = [
            ['name' => 'chatgpt', 'color' => '#10b981'],
            ['name' => 'openai', 'color' => '#3b82f6'],
            ['name' => 'claude', 'color' => '#8b5cf6'],
            ['name' => 'gemini', 'color' => '#f59e0b'],
            ['name' => 'marketing', 'color' => '#ec4899'],
            ['name' => 'coding', 'color' => '#06b6d4'],
            ['name' => 'content', 'color' => '#64748b'],
            ['name' => 'research', 'color' => '#475569'],
        ];

        $existingNames = $tags->pluck('name')->map(fn($n) => strtolower($n))->toArray();
        $suggestions = array_filter($allSuggestions, fn($s) => !in_array(strtolower($s['name']), $existingNames));

        return view('tags.index', compact('tags', 'suggestions'));
    }

    /**
     * Store a suggested resource in storage.
     */
    public function storeSuggestion(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['name']);
        
        // Ensure not duplicate
        if (Tag::where('user_id', auth()->id())->where('name', $validated['name'])->exists()) {
            return redirect()->back()->with('error', 'Tag already exists.');
        }

        Tag::create($validated);

        return redirect()->route('tags.index')->with('success', 'Tag added from suggestions.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tags.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required', 
                'string', 
                'max:255',
                \Illuminate\Validation\Rule::unique('tags')->where('user_id', auth()->id())
            ],
            'color' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['name']);
        
        Tag::create($validated);

        return redirect()->route('tags.index')->with('success', 'Tag created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag)
    {
        if ($tag->user_id !== auth()->id()) {
            abort(403);
        }
        return view('tags.show', compact('tag'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag)
    {
        if ($tag->user_id !== auth()->id()) {
            abort(403);
        }
        return view('tags.edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            'name' => [
                'required', 
                'string', 
                'max:255',
                \Illuminate\Validation\Rule::unique('tags')
                    ->where('user_id', auth()->id())
                    ->ignore($tag->id)
            ],
            'color' => 'nullable|string',
        ]);

        if ($tag->user_id !== auth()->id()) {
            abort(403);
        }
        
        $validated['slug'] = Str::slug($validated['name']);
        $tag->update($validated);

        return redirect()->route('tags.index')->with('success', 'Tag updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        if ($tag->user_id !== auth()->id()) {
            abort(403);
        }
        $tag->delete();
        return redirect()->route('tags.index')->with('success', 'Tag deleted successfully.');
    }
}
