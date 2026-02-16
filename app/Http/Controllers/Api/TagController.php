<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tags = $request->user()->tags()->withCount('prompts')->latest()->get();
        return response()->json($tags);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tag = $request->user()->tags()->create($validated);
        return response()->json($tag, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $tag = $request->user()->tags()->with('prompts')->findOrFail($id);
        return response()->json($tag);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tag = $request->user()->tags()->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
        ]);

        $tag->update($validated);
        return response()->json($tag);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $tag = $request->user()->tags()->findOrFail($id);
        $tag->delete();

        return response()->json(['message' => 'Tag deleted successfully'], 200);
    }
}
