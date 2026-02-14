@extends('layouts.app')

@section('title', 'Edit Tag: ' . $tag->name)

@section('content')
<div class="flex items-center gap-2 mb-4">
    <a href="{{ route('tags.index') }}" class="btn btn-secondary">← Back</a>
    <h1>Edit Tag</h1>
</div>

<div class="card">
    <form action="{{ route('tags.update', $tag) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $tag->name) }}" required>
            @error('name') <div class="text-danger text-sm">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $tag->slug) }}" required>
            @error('slug') <div class="text-danger text-sm">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="color" class="form-label">Color (Hex)</label>
            <input type="text" name="color" id="color" class="form-control" value="{{ old('color', $tag->color) }}">
        </div>

        <div class="flex justify-end gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Update Tag</button>
        </div>
    </form>
</div>
@endsection
