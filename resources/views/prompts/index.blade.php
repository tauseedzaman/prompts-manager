@extends('layouts.app')

@section('title', 'Prompts Dashboard')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h1>All Prompts</h1>
    <div class="flex gap-2">
        <a href="{{ route('prompts.export') }}" class="btn btn-secondary">
            <i class="fas fa-download"></i> Export
        </a>
        <a href="{{ route('prompts.import-page') }}" class="btn btn-secondary">
            <i class="fas fa-upload"></i> Import
        </a>
        <a href="{{ route('prompts.create') }}" class="btn btn-primary">
            + New Prompt
        </a>
    </div>
</div>

<div class="flex gap-4">
    <!-- Sidebar Filters (Optional, could be moved to main layout sidebar but keeping here for context specific) -->
    <div class="w-64 flex-shrink-0">
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
                        <span class="inline-block w-2.5 h-2.5 rounded-full mr-2" style="background-color: {{ $category->color ?? '#cbd5e1' }};"></span>
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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($prompts as $prompt)
                <div class="card flex flex-col h-full">
                    <div class="flex justify-between items-start mb-2">
                        <span class="badge badge-blue">
                            {{ $prompt->category->name }}
                        </span>
                        <button onclick="toggleFavorite('{{ $prompt->id }}', this)" class="favorite-btn {{ $prompt->is_favorite ? 'is-active' : '' }}" title="{{ $prompt->is_favorite ? 'Remove from favorites' : 'Add to favorites' }}">
                            <i class="{{ $prompt->is_favorite ? 'fas' : 'far' }} fa-heart"></i>
                        </button>
                    </div>
                    
                    <h2 class="text-xl font-semibold mb-2">
                        <a href="{{ route('prompts.show', $prompt) }}">{{ $prompt->title }}</a>
                    </h2>
                    
                    <p class="text-muted text-sm mb-4 flex-grow">
                        {{ Str::limit($prompt->description ?? $prompt->prompt_text, 100) }}
                    </p>

                    <div class="flex gap-2 mt-auto">
                         @foreach($prompt->tags as $tag)
                            <span class="badge badge-gray text-[10px]">#{{ $tag->name }}</span>
                         @endforeach
                    </div>
                    
                    <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-100 dark:border-white/5">
                        <button onclick="copyToClipboard(`{{ addslashes($prompt->prompt_text) }}`)" class="btn btn-secondary btn-sm">
                            Copy
                        </button>
                         <a href="{{ route('prompts.edit', $prompt) }}" class="text-muted text-sm hover:text-blue-500">Edit</a>
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

@push('scripts')
<script>
    function toggleFavorite(promptId, btn) {
        fetch(`/prompts/${promptId}/toggle-favorite`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            const icon = btn.querySelector('i');
            if (data.is_favorite) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                btn.classList.add('is-active');
                btn.title = 'Remove from favorites';
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                btn.classList.remove('is-active');
                btn.title = 'Add to favorites';
            }
            
            // Optional: SweetAlert toast
            Swal.fire({
                icon: 'success',
                title: data.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
        })
        .catch(err => {
            console.error('Error toggling favorite:', err);
            Swal.fire({
                icon: 'error',
                title: 'Something went wrong',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
        });
    }
</script>
@endpush
