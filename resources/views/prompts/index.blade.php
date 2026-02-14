@extends('layouts.app')

@section('title', 'Prompts Dashboard')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h1>All Prompts</h1>
    <div class="flex gap-2">
        <a href="{{ route('prompts.create') }}" class="btn btn-primary">
            + New Prompt
        </a>
    </div>
</div>

<div class="flex gap-4">
    <!-- Sidebar Filters (Optional, could be moved to main layout sidebar but keeping here for context specific) -->
    <div style="width: 250px; flex-shrink: 0;">
        <div class="card">
            <h3 class="text-sm font-bold uppercase text-muted mb-2">Categories</h3>
            <ul>
                <li>
                    <a href="{{ route('prompts.index') }}" class="nav-link {{ !request('category_id') ? 'active' : '' }}">
                        All Categories
                    </a>
                </li>
                @foreach($categories as $category)
                <li>
                    <a href="{{ route('prompts.index', ['category_id' => $category->id]) }}" class="nav-link {{ request('category_id') == $category->id ? 'active' : '' }}">
                        <span style="display:inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: {{ $category->color ?? '#cbd5e1' }}; margin-right: 8px;"></span>
                        {{ $category->name }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Prompts Grid -->
    <div class="w-full">
        @if($prompts->count() > 0)
            <div class="grid grid-cols-2 gap-4" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem;">
                @foreach($prompts as $prompt)
                <div class="card" style="display: flex; flex-direction: column; height: 100%;">
                    <div class="flex justify-between items-start mb-2">
                        <span class="badge badge-blue">
                            {{ $prompt->category->name }}
                        </span>
                        @if($prompt->is_favorite)
                            <span style="color: #fbbf24;">★</span>
                        @endif
                    </div>
                    
                    <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">
                        <a href="{{ route('prompts.show', $prompt) }}">{{ $prompt->title }}</a>
                    </h2>
                    
                    <p class="text-muted text-sm mb-4" style="flex-grow: 1;">
                        {{ Str::limit($prompt->description ?? $prompt->prompt_text, 100) }}
                    </p>

                    <div class="flex gap-2 mt-auto">
                         @foreach($prompt->tags as $tag)
                            <span class="badge badge-gray" style="font-size: 0.7rem;">#{{ $tag->name }}</span>
                         @endforeach
                    </div>
                    
                    <div class="flex justify-between items-center mt-4 pt-4" style="border-top: 1px solid var(--border-color);">
                        <button onclick="copyToClipboard(`{{ addslashes($prompt->prompt_text) }}`)" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                            Copy
                        </button>
                         <a href="{{ route('prompts.edit', $prompt) }}" class="text-muted text-sm hover:text-primary">Edit</a>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="mt-4">
                {{ $prompts->links() }}
            </div>
        @else
            <div class="card text-center" style="padding: 3rem;">
                <p class="text-muted mb-4">No prompts found.</p>
                <a href="{{ route('prompts.create') }}" class="btn btn-primary">Create your first prompt</a>
            </div>
        @endif
    </div>
</div>
@endsection
