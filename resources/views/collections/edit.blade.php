@extends('layouts.app')

@section('title', 'Edit Collection: ' . $collection->name)

@section('content')
<div class="flex items-center gap-2 mb-4">
    <a href="{{ route('collections.index') }}" class="btn btn-secondary">← Back</a>
    <h1>Edit Collection</h1>
</div>

<div class="card">
    <form action="{{ route('collections.update', $collection) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $collection->name) }}" required>
            @error('name') <div class="text-danger text-sm">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $collection->slug) }}" required>
            @error('slug') <div class="text-danger text-sm">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" rows="3" class="form-control">{{ old('description', $collection->description) }}</textarea>
        </div>

        <div class="flex justify-end gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Update Collection</button>
        </div>
    </form>
</div>
@endsection
