@extends('layouts.app')

@section('title', 'My Workspaces')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1>Workspaces</h1>
    <a href="{{ route('workspaces.create') }}" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i> Create Workspace
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <!-- Owned Workspaces -->
    <div>
        <h2 class="text-sm font-bold uppercase text-muted mb-4 tracking-wider">Owned by Me</h2>
        @if($ownedWorkspaces->count() > 0)
            <div class="space-y-4">
                @foreach($ownedWorkspaces as $workspace)
                <div class="card hover:border-indigo-500/50 transition-colors">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-bold">
                            <a href="{{ route('workspaces.show', $workspace) }}" class="hover:text-indigo-500">{{ $workspace->name }}</a>
                        </h3>
                        <span class="badge badge-blue">Owner</span>
                    </div>
                    <p class="text-muted text-sm mb-4 line-clamp-2">{{ $workspace->description ?? 'No description.' }}</p>
                    <div class="flex gap-4 text-xs text-muted border-t border-white/5 pt-3">
                        <span><i class="fas fa-users mr-1"></i> {{ $workspace->members_count }} Members</span>
                        <span><i class="fas fa-file-alt mr-1"></i> {{ $workspace->prompts_count }} Prompts</span>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="card text-center py-8">
                <p class="text-muted text-sm">You haven't created any workspaces yet.</p>
            </div>
        @endif
    </div>

    <!-- Joined Workspaces -->
    <div>
        <h2 class="text-sm font-bold uppercase text-muted mb-4 tracking-wider">Shared with Me</h2>
        @if($joinedWorkspaces->count() > 0)
            <div class="space-y-4">
                @foreach($joinedWorkspaces as $workspace)
                <div class="card hover:border-indigo-500/50 transition-colors">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-bold">
                            <a href="{{ route('workspaces.show', $workspace) }}" class="hover:text-indigo-500">{{ $workspace->name }}</a>
                        </h3>
                        <span class="badge badge-gray">{{ ucfirst($workspace->pivot->role) }}</span>
                    </div>
                    <p class="text-muted text-sm mb-4 line-clamp-2">{{ $workspace->description ?? 'No description.' }}</p>
                    
                    <div class="flex justify-between items-center border-t border-white/5 pt-3">
                        <div class="flex gap-4 text-xs text-muted">
                            <span><i class="fas fa-users mr-1"></i> {{ $workspace->members_count }} Members</span>
                            <span><i class="fas fa-file-alt mr-1"></i> {{ $workspace->prompts_count }} Prompts</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-muted">
                           <img src="{{ $workspace->owner->avatar_url }}" class="w-4 h-4 rounded-full">
                           <span>by {{ $workspace->owner->name }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="card text-center py-8">
                <p class="text-muted text-sm">No shared workspaces found.</p>
            </div>
        @endif
    </div>
</div>
@endsection
