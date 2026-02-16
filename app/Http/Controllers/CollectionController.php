<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Collection;
use Illuminate\Support\Str;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $collections = Collection::where('user_id', auth()->id())->get();
        return view('collections.index', compact('collections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('collections.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        // Ensure slug is unique for this user
        $baseSlug = $validated['slug'];
        $count = 1;
        while (Collection::where('user_id', auth()->id())->where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $baseSlug . '-' . $count++;
        }

        // Assuming user_id is nullable or handled by auth() later.
        // $validated['user_id'] = auth()->id();

        $validated['user_id'] = auth()->id();
        Collection::create($validated);

        return redirect()->route('collections.index')->with('success', 'Collection created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Collection $collection)
    {
        if ($collection->user_id !== auth()->id()) {
            abort(403);
        }
        return view('collections.show', compact('collection'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Collection $collection)
    {
        if ($collection->user_id !== auth()->id()) {
            abort(403);
        }
        return view('collections.edit', compact('collection'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Collection $collection)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        if ($collection->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
            $baseSlug = $validated['slug'];
            $count = 1;
            while (Collection::where('user_id', auth()->id())->where('slug', $validated['slug'])->where('id', '!=', $collection->id)->exists()) {
                $validated['slug'] = $baseSlug . '-' . $count++;
            }
        }

        if ($collection->user_id !== auth()->id()) {
            abort(403);
        }
        $collection->update($validated);

        return redirect()->route('collections.index')->with('success', 'Collection updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Collection $collection)
    {
        if ($collection->user_id !== auth()->id()) {
            abort(403);
        }
        $collection->delete();
        return redirect()->route('collections.index')->with('success', 'Collection deleted successfully.');
    }
}
