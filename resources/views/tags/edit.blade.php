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
            <label for="color" class="form-label">Color</label>
            <div class="flex items-center gap-3">
                <input type="color" name="color" id="color" class="h-10 w-20 rounded border border-gray-300 dark:border-gray-600 cursor-pointer" value="{{ old('color', $tag->color ?? '#6b7280') }}">
                <input type="text" id="color-hex" class="form-control flex-1" value="{{ old('color', $tag->color ?? '#6b7280') }}" readonly>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Choose a color to represent this tag</p>
        </div>

        <div class="flex justify-end gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Update Tag</button>
        </div>
    </form>
</div>
@endsection

<script>
    // Sync color picker with hex input
    const colorPicker = document.getElementById('color');
    const colorHex = document.getElementById('color-hex');
    
    colorPicker.addEventListener('input', function() {
        colorHex.value = this.value.toUpperCase();
    });
</script>
