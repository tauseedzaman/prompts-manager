@extends('layouts.app')

@section('title', 'Edit Category: ' . $category->name)

@section('content')
<div class="flex items-center gap-2 mb-4">
    <a href="{{ route('categories.index') }}" class="btn btn-secondary">← Back</a>
    <h1>Edit Category</h1>
</div>

<div class="card">
    <form action="{{ route('categories.update', $category) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $category->name) }}" required>
            @error('name') <div class="text-danger text-sm">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $category->slug) }}" required>
            @error('slug') <div class="text-danger text-sm">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="color" class="form-label">Color (Hex or name)</label>
            <input type="text" name="color" id="color" class="form-control" value="{{ old('color', $category->color) }}">
        </div>

        <div class="form-group">
            <label for="sort_order" class="form-label">Sort Order</label>
            <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', $category->sort_order) }}">
        </div>

        <div class="form-group">
            <label class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                Active
            </label>
        </div>

        <div class="flex justify-end gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Update Category</button>
        </div>
    </form>
</div>
@endsection
