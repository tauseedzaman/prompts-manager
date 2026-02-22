<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ownedWorkspaces = auth()->user()->ownedWorkspaces()->withCount(['members', 'prompts'])->get();
        $joinedWorkspaces = auth()->user()->workspaces()->withCount(['members', 'prompts'])->get();
        
        return view('workspaces.index', compact('ownedWorkspaces', 'joinedWorkspaces'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('workspaces.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $workspace = Workspace::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . Str::random(5),
            'owner_id' => auth()->id(),
            'description' => $validated['description'],
        ]);

        $workspace->members()->attach(auth()->id(), ['role' => 'admin']);

        return redirect()->route('workspaces.show', $workspace)->with('success', 'Workspace created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Workspace $workspace)
    {
        $this->authorizeAccess($workspace);
        $workspace->load(['members', 'prompts.category', 'categories']);
        
        return view('workspaces.show', compact('workspace'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Workspace $workspace)
    {
        $this->authorizeAdmin($workspace);
        return view('workspaces.edit', compact('workspace'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Workspace $workspace)
    {
        $this->authorizeAdmin($workspace);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $workspace->update($validated);

        return redirect()->route('workspaces.show', $workspace)->with('success', 'Workspace updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workspace $workspace)
    {
        if ($workspace->owner_id !== auth()->id()) {
            abort(403, 'Only owners can delete workspaces.');
        }

        $workspace->delete();

        return redirect()->route('workspaces.index')->with('success', 'Workspace deleted successfully.');
    }

    /**
     * Add a member to the workspace.
     */
    public function addMember(Request $request, Workspace $workspace)
    {
        $this->authorizeAdmin($workspace);

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'required|in:admin,editor,viewer',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($workspace->members()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'User is already a member of this workspace.');
        }

        $workspace->members()->attach($user->id, ['role' => $validated['role']]);

        return back()->with('success', "{$user->name} added to workspace.");
    }

    /**
     * Remove a member from the workspace.
     */
    public function removeMember(Workspace $workspace, User $user)
    {
        $this->authorizeAdmin($workspace);

        if ($user->id === $workspace->owner_id) {
            return back()->with('error', 'Cannot remove the workspace owner.');
        }

        $workspace->members()->detach($user->id);

        return back()->with('success', 'Member removed from workspace.');
    }

    protected function authorizeAccess(Workspace $workspace)
    {
        if ($workspace->owner_id === auth()->id()) return;
        if ($workspace->members()->where('user_id', auth()->id())->exists()) return;
        
        abort(403, 'You do not have access to this workspace.');
    }

    protected function authorizeAdmin(Workspace $workspace)
    {
        if ($workspace->owner_id === auth()->id()) return;
        
        $member = $workspace->members()->where('user_id', auth()->id())->first();
        if ($member && $member->pivot->role === 'admin') return;
        
        abort(403, 'Administrator access required.');
    }
}
