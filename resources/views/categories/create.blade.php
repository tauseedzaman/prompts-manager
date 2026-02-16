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
            <label for="color" class="form-label">Color</label>
            <div class="flex items-center gap-3">
                <input type="color" name="color" id="color" class="h-10 w-20 rounded border border-gray-300 dark:border-gray-600 cursor-pointer" value="{{ old('color', '#3b82f6') }}">
                <input type="text" id="color-hex" class="form-control flex-1" value="{{ old('color', '#3b82f6') }}" readonly>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Choose a color to represent this category</p>
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

@push('scripts')
<script>
    // Sync color picker with hex input
    document.addEventListener('DOMContentLoaded', function() {
        const colorPicker = document.getElementById('color');
        const colorHex = document.getElementById('color-hex');
        
        if (colorPicker && colorHex) {
            colorPicker.addEventListener('input', function() {
                colorHex.value = this.value.toUpperCase();
            });
        }
    });
</script>
@endpush
@endsection
