@extends('layouts.app')

@section('title', 'Create Workspace')

@section('content')
<div class="flex items-center gap-2 mb-4">
    <a href="{{ route('workspaces.index') }}" class="btn btn-secondary">← Back</a>
    <h1>Create New Workspace</h1>
</div>

<div class="card max-w-2xl">
    <form action="{{ route('workspaces.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name" class="form-label">Workspace Name</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="e.g. Marketing Team, Personal Projects" value="{{ old('name') }}" required>
            @error('name') <div class="text-danger text-sm">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description (Optional)</label>
            <textarea name="description" id="description" rows="3" class="form-control" placeholder="What is this workspace for?">{{ old('description') }}</textarea>
            @error('description') <div class="text-danger text-sm">{{ $message }}</div> @enderror
        </div>

        <div class="flex justify-end mt-6">
            <button type="submit" class="btn btn-primary">Create Workspace</button>
        </div>
    </form>
</div>
@endsection
