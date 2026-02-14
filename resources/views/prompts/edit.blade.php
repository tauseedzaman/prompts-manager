@extends('layouts.app')

@section('title', 'Edit Prompt: ' . $prompt->title)

@section('content')
<div class="flex items-center gap-2 mb-4">
    <a href="{{ route('prompts.show', $prompt) }}" class="btn btn-secondary">← Back</a>
    <h1>Edit Prompt</h1>
</div>

<div class="card">
    <form action="{{ route('prompts.update', $prompt) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-2 gap-4">
            <div class="form-group">
                <label for="title" class="form-label">Title</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $prompt->title) }}" required>
                @error('title') <div class="text-danger text-sm">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="slug" class="form-label">Slug</label>
                <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $prompt->slug) }}" required>
                @error('slug') <div class="text-danger text-sm">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="category_id" class="form-label">Category</label>
            <select name="category_id" id="category_id" class="form-control" required>
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $prompt->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id') <div class="text-danger text-sm">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="prompt_text" class="form-label">Prompt Text</label>
            <textarea name="prompt_text" id="prompt_text" rows="10" class="form-control" required>{{ old('prompt_text', $prompt->prompt_text) }}</textarea>
            @error('prompt_text') <div class="text-danger text-sm">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description / Notes</label>
            <textarea name="description" id="description" rows="3" class="form-control">{{ old('description', $prompt->description) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="form-group">
                <label for="language" class="form-label">Language</label>
                <input type="text" name="language" id="language" class="form-control" value="{{ old('language', $prompt->language) }}">
            </div>

            <div class="form-group">
                <label for="tone" class="form-label">Tone</label>
                <input type="text" name="tone" id="tone" class="form-control" value="{{ old('tone', $prompt->tone) }}">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Tags</label>
            <div class="flex flex-wrap gap-2">
                @foreach($tags as $tag)
                    <label class="flex items-center gap-2 cursor-pointer border rounded px-2 py-1 hover:bg-gray-50">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', $prompt->tags->pluck('id')->toArray())) ? 'checked' : '' }}>
                        <span style="color: {{ $tag->color ?? 'inherit' }}">{{ $tag->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Update Prompt</button>
        </div>
    </form>
</div>
@endsection
