<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Collection;

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
            'slug' => 'required|string|max:255|unique:collections',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

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
            'slug' => 'required|string|max:255|unique:collections,slug,' . $collection->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

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
