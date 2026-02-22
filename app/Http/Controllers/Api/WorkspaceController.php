<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkspaceController extends Controller
{
    public function index(Request $request)
    {
        $workspaces = $request->user()->workspaces()->with('owner')->get();
        $owned = $request->user()->ownedWorkspaces()->with('owner')->get();
        
        return response()->json([
            'workspaces' => $workspaces->concat($owned)->unique('id')
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $workspace = Workspace::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . Str::random(5),
            'owner_id' => $request->user()->id,
            'description' => $validated['description'],
        ]);

        // Add owner as admin member
        $workspace->members()->attach($request->user()->id, ['role' => 'admin']);

        return response()->json($workspace, 201);
    }

    public function show(Workspace $workspace)
    {
        $this->authorizeAccess($workspace);
        return response()->json($workspace->load(['members', 'prompts', 'categories']));
    }

    public function update(Request $request, Workspace $workspace)
    {
        $this->authorizeAdmin($workspace);
        
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(5);
        }

        $workspace->update($validated);
        return response()->json($workspace);
    }

    public function destroy(Workspace $workspace)
    {
        if ($workspace->owner_id !== auth()->id()) {
            abort(403, 'Only owners can delete workspaces.');
        }

        $workspace->delete();
        return response()->json(['message' => 'Workspace deleted']);
    }

    protected function authorizeAccess(Workspace $workspace)
    {
        if ($workspace->owner_id === auth()->id()) return;
        if ($workspace->members()->where('user_id', auth()->id())->exists()) return;
        
        abort(403);
    }

    protected function authorizeAdmin(Workspace $workspace)
    {
        if ($workspace->owner_id === auth()->id()) return;
        
        $member = $workspace->members()->where('user_id', auth()->id())->first();
        if ($member && $member->pivot->role === 'admin') return;
        
        abort(403);
    }
}
