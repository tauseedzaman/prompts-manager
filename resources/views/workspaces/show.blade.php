@extends('layouts.app')

@section('title', $workspace->name)

@section('content')
<div class="mb-6 flex justify-between items-start">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('workspaces.index') }}" class="text-muted hover:text-indigo-500"><i class="fas fa-chevron-left"></i></a>
            <h1 class="mb-0">{{ $workspace->name }}</h1>
        </div>
        <p class="text-muted">{{ $workspace->description ?? 'No description provided.' }}</p>
    </div>
    <div class="flex gap-2">
        @if($workspace->owner_id === auth()->id() || auth()->user()->workspaces()->where('workspace_id', $workspace->id)->wherePivot('role', 'admin')->exists())
            <a href="{{ route('workspaces.edit', $workspace) }}" class="btn btn-secondary">
                <i class="fas fa-cog mr-2"></i> Settings
            </a>
            <button onclick="document.getElementById('add-member-modal').classList.remove('hidden')" class="btn btn-primary">
                <i class="fas fa-user-plus mr-2"></i> Add Member
            </button>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Content: Prompts -->
    <div class="lg:col-span-2">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold">Workspace Prompts</h2>
            <a href="{{ route('prompts.create', ['workspace_id' => $workspace->id]) }}" class="btn btn-primary btn-sm">
                + New Shared Prompt
            </a>
        </div>

        @if($workspace->prompts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($workspace->prompts as $prompt)
                <div class="card flex flex-col h-full border-l-4" style="border-left-color: {{ $prompt->category->color ?? '#3b82f6' }}">
                    <div class="flex justify-between items-start mb-2">
                        <span class="badge badge-gray text-[10px] uppercase font-bold">{{ $prompt->category->name }}</span>
                    </div>
                    <h3 class="text-md font-bold mb-2">
                        <a href="{{ route('prompts.show', $prompt) }}" class="hover:text-indigo-500">{{ $prompt->title }}</a>
                    </h3>
                    <p class="text-muted text-xs mb-4 flex-grow line-clamp-2">{{ $prompt->description ?? Str::limit($prompt->prompt_text, 100) }}</p>
                    <div class="flex justify-between items-center mt-4 pt-3 border-t border-white/5">
                        <button onclick="copyToClipboard(`{{ addslashes($prompt->prompt_text) }}`)" class="text-xs text-indigo-400 hover:text-indigo-300">
                             Copy Text
                        </button>
                        <span class="text-[10px] text-muted">Used {{ $prompt->usage_count }} times</span>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="card text-center py-12">
                <i class="fas fa-file-alt text-4xl text-gray-700 mb-4"></i>
                <p class="text-muted mb-4">No prompts shared in this workspace yet.</p>
                <a href="{{ route('prompts.create', ['workspace_id' => $workspace->id]) }}" class="btn btn-secondary btn-sm">Add the first prompt</a>
            </div>
        @endif
    </div>

    <!-- Sidebar: Members -->
    <div>
        <div class="card">
            <h3 class="text-sm font-bold uppercase text-muted mb-4 tracking-wider">Workspace Members</h3>
            <div class="space-y-4">
                <!-- Owner -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="{{ $workspace->owner->avatar_url }}" class="w-8 h-8 rounded-full border border-white/10">
                        <div>
                            <div class="text-xs font-bold">{{ $workspace->owner->name }} (You)</div>
                            <div class="text-[10px] text-muted">Owner</div>
                        </div>
                    </div>
                </div>

                <!-- Members -->
                @foreach($workspace->members as $member)
                    @if($member->id !== $workspace->owner_id)
                    <div class="flex items-center justify-between group">
                        <div class="flex items-center gap-3">
                            <img src="{{ $member->avatar_url }}" class="w-8 h-8 rounded-full">
                            <div>
                                <div class="text-xs font-bold">{{ $member->name }}</div>
                                <div class="text-[10px] text-muted">{{ ucfirst($member->pivot->role) }}</div>
                            </div>
                        </div>
                        @if($workspace->owner_id === auth()->id() || (auth()->user()->workspaces()->where('workspace_id', $workspace->id)->wherePivot('role', 'admin')->exists() && $member->pivot->role !== 'admin'))
                        <form action="{{ route('workspaces.members.destroy', [$workspace, $member]) }}" method="POST" onsubmit="return confirm('Remove this member?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-muted hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Add Member Modal (Quick & Simple Tailwind Overlay) -->
<div id="add-member-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="card w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Add Member</h3>
            <button onclick="document.getElementById('add-member-modal').classList.add('hidden')" class="text-muted">✕</button>
        </div>
        <form action="{{ route('workspaces.members.store', $workspace) }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="colleague@example.com" required>
            </div>
            <div class="form-group">
                <label class="form-label">Role</label>
                <select name="role" class="form-control">
                    <option value="viewer">Viewer (Can only copy)</option>
                    <option value="editor">Editor (Can add/edit prompts)</option>
                    <option value="admin">Admin (Manage members)</option>
                </select>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="document.getElementById('add-member-modal').classList.add('hidden')" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Member</button>
            </div>
        </form>
    </div>
</div>
@endsection
