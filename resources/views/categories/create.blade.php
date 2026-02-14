@extends('layouts.app')

@section('title', 'Create Category')

@section('content')
<div class="flex items-center gap-2 mb-4">
    <a href="{{ route('categories.index') }}" class="btn btn-secondary">← Back</a>
    <h1>Create Category</h1>
</div>

<div class="card">
    <form action="{{ route('categories.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
            @error('name') <div class="text-danger text-sm">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug') }}" required>
            @error('slug') <div class="text-danger text-sm">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="color" class="form-label">Color (Hex or name)</label>
            <input type="text" name="color" id="color" class="form-control" value="{{ old('color', '#3b82f6') }}">
        </div>

        <div class="form-group">
            <label for="sort_order" class="form-label">Sort Order</label>
            <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
        </div>

        <div class="form-group">
            <label class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                Active
            </label>
        </div>

        <div class="flex justify-end gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Save Category</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('name').addEventListener('input', function() {
        const name = this.value;
        const slug = name.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        document.getElementById('slug').value = slug;
    });
</script>
@endsection
