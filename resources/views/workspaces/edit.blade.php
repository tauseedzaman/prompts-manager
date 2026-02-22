@extends('layouts.app')

@section('title', 'Edit Workspace')

@section('content')
<div class="flex items-center gap-2 mb-4">
    <a href="{{ route('workspaces.show', $workspace) }}" class="btn btn-secondary">← Back</a>
    <h1>Workspace Settings</h1>
</div>

<div class="card max-w-2xl">
    <form action="{{ route('workspaces.update', $workspace) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name" class="form-label">Workspace Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $workspace->name) }}" required>
            @error('name') <div class="text-danger text-sm">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" rows="3" class="form-control">{{ old('description', $workspace->description) }}</textarea>
            @error('description') <div class="text-danger text-sm">{{ $message }}</div> @enderror
        </div>

        <div class="flex justify-between mt-6">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <form action="{{ route('workspaces.destroy', $workspace) }}" method="POST" onsubmit="return confirm('Delete workspace? This cannot be undone!')">
                @csrf
                @method('DELETE')
                @if($workspace->owner_id === auth()->id())
                <button type="submit" class="text-red-500 hover:text-red-400 text-sm font-bold">
                    <i class="fas fa-trash mr-2"></i> Delete Workspace
                </button>
                @endif
            </form>
        </div>
    </form>
</div>
@endsection
