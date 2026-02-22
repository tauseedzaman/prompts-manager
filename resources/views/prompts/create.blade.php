@extends('layouts.app')

@section('title', 'Create New Prompt')

@section('content')
<div class="flex items-center gap-2 mb-4">
    <a href="{{ route('prompts.index') }}" class="btn btn-secondary">← Back</a>
    <h1>Create New Prompt</h1>
</div>

<div class="card">
    <form action="{{ route('prompts.store') }}" method="POST">
        @csrf
        @if(request('workspace_id'))
            <input type="hidden" name="workspace_id" value="{{ request('workspace_id') }}">
        @endif

        <div class="form-group">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
            @error('title') <div class="text-danger text-sm">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="category_id" class="form-label">Category</label>
            <select name="category_id" id="category_id" class="form-control" required>
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id') <div class="text-danger text-sm">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="prompt_text" class="form-label">Prompt Text</label>
            <textarea name="prompt_text" id="prompt_text" rows="10" class="form-control" required>{{ old('prompt_text') }}</textarea>
            {{-- <div class="form-text">Support for {{ $variables }} coming soon.</div> --}}
            @error('prompt_text') <div class="text-danger text-sm">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description / Notes</label>
            <textarea name="description" id="description" rows="3" class="form-control">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="form-group">
                <label for="language" class="form-label">Language</label>
                <input type="text" name="language" id="language" class="form-control" value="{{ old('language', 'en') }}">
            </div>

            <div class="form-group">
                <label for="tone" class="form-label">Tone</label>
                <input type="text" name="tone" id="tone" class="form-control" value="{{ old('tone') }}">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Tags</label>
            <div class="flex flex-wrap gap-2">
                @foreach($tags as $tag)
                    <label class="flex items-center gap-2 cursor-pointer border rounded px-2 py-1 hover:bg-gray-50">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}>
                        <span style="color: {{ $tag->color ?? 'inherit' }}">{{ $tag->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Visibility</label>
            <div class="flex gap-4 mt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="visibility" value="private" {{ old('visibility', 'private') == 'private' ? 'checked' : '' }}>
                    <span>Private (Only you)</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="visibility" value="public" {{ old('visibility') == 'public' ? 'checked' : '' }}>
                    <span>Public (Share in Marketplace)</span>
                </label>
            </div>
            @error('visibility') <div class="text-danger text-sm">{{ $message }}</div> @enderror
        </div>

        <div class="flex justify-end gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Create Prompt</button>
        </div>
    </form>
</div>

@endsection
