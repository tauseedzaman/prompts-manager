<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $collections = $request->user()->collections()->withCount('prompts')->latest()->get();
        return response()->json($collections);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $collection = $request->user()->collections()->create($validated);
        return response()->json($collection, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $collection = $request->user()->collections()->with('prompts')->findOrFail($id);
        return response()->json($collection);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $collection = $request->user()->collections()->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $collection->update($validated);
        return response()->json($collection);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $collection = $request->user()->collections()->findOrFail($id);
        $collection->delete();

        return response()->json(['message' => 'Collection deleted successfully'], 200);
    }
}
