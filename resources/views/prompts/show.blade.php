@extends('layouts.app')

@section('title', $prompt->title)

@section('content')
<div class="flex justify-between items-center mb-4">
    <div class="flex items-center gap-2">
        <a href="{{ route('prompts.index') }}" class="btn btn-secondary">← Back</a>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('prompts.history', $prompt) }}" class="btn btn-secondary">History</a>
        <a href="{{ route('prompts.edit', $prompt) }}" class="btn btn-secondary">Edit</a>
        <form action="{{ route('prompts.destroy', $prompt) }}" method="POST" onsubmit="return confirm('Are you sure?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="flex justify-between items-start mb-4">
        <div>
            <h1 style="margin-bottom: 0.25rem;">{{ $prompt->title }}</h1>
            <div class="flex gap-2 items-center text-sm text-muted">
                <span class="badge badge-blue">{{ $prompt->category->name }}</span>
                @if($prompt->language) <span>{{ strtoupper($prompt->language) }}</span> @endif
                @if($prompt->tone) <span>{{ ucfirst($prompt->tone) }}</span> @endif
            </div>
        </div>
        <button onclick="copyToClipboard(document.getElementById('prompt-text').innerText)" class="btn btn-primary">
            Copy Prompt
        </button>
    </div>

    @if($prompt->description)
        <div class="mb-4 text-muted">
            {{ $prompt->description }}
        </div>
    @endif

    <div style="background-color: #f8fafc; padding: 1.5rem; border-radius: 0.5rem; border: 1px solid var(--border-color); position: relative;">
        <pre id="prompt-text" style="white-space: pre-wrap; font-family: 'Inter', sans-serif; font-size: 1rem; color: var(--text-main);">{{ $prompt->prompt_text }}</pre>
    </div>

    @if($prompt->tags->count() > 0)
        <div class="mt-4 flex gap-2">
            @foreach($prompt->tags as $tag)
                <span class="badge badge-gray">#{{ $tag->name }}</span>
            @endforeach
        </div>
    @endif
</div>

@if($prompt->is_template && $prompt->variables_schema)
<div class="card">
    <h2>Template Variables</h2>
    <!-- Implementation for template rendering would go here -->
    <p class="text-muted">This prompt has variables. (Rendering logic to be implemented)</p>
</div>
@endif

@endsection
