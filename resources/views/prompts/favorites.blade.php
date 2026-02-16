@extends('layouts.app')

@section('title', 'My Favorites')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight">My Favorites</h1>
            <p class="text-gray-400 mt-1">Quick access to your most-used AI prompts.</p>
        </div>
    </div>

    @if($prompts->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($prompts as $prompt)
                <div class="prompt-card group">
                    <div class="prompt-card-header">
                        <div class="flex items-center gap-2">
                            @if($prompt->category)
                                <span class="category-badge" style="background-color: {{ $prompt->category->color }}20; color: {{ $prompt->category->color }}">
                                    {{ $prompt->category->name }}
                                </span>
                            @endif
                        </div>
                        <button onclick="toggleFavorite('{{ $prompt->id }}')" 
                                id="fav-btn-{{ $prompt->id }}"
                                class="favorite-btn {{ $prompt->is_favorite ? 'active' : '' }}"
                                title="{{ $prompt->is_favorite ? 'Remove from favorites' : 'Add to favorites' }}">
                            <i class="{{ $prompt->is_favorite ? 'fas' : 'far' }} fa-heart"></i>
                        </button>
                    </div>

                    <div class="prompt-card-body">
                        <h3 class="prompt-title">{{ $prompt->title }}</h3>
                        <p class="prompt-preview">{{ Str::limit($prompt->prompt_text, 120) }}</p>
                        
                        @if($prompt->tags->count() > 0)
                            <div class="flex flex-wrap gap-1.5 mt-4">
                                @foreach($prompt->tags as $tag)
                                    <span class="tag-pill">#{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="prompt-card-footer">
                        <button onclick="copyToClipboard('{{ addslashes($prompt->prompt_text) }}')" class="action-btn" title="Copy to clipboard">
                            <i class="far fa-copy"></i>
                        </button>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('prompts.edit', $prompt) }}" class="action-btn" title="Edit prompt">
                                <i class="far fa-edit"></i>
                            </a>
                            <a href="{{ route('prompts.show', $prompt) }}" class="action-btn" title="View details">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-8">
            {{ $prompts->links() }}
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-20 text-center bg-white/5 rounded-[2rem] border border-white/5">
            <div class="w-20 h-20 bg-blue-600/10 rounded-2xl flex items-center justify-center mb-6">
                <i class="far fa-heart text-blue-500 text-3xl"></i>
            </div>
            <h2 class="text-xl font-semibold text-white">No favorites yet</h2>
            <p class="text-gray-400 mt-2 max-w-sm">Items you mark with a heart will appear here for quick access.</p>
            <a href="{{ route('prompts.index') }}" class="btn btn-primary mt-8">Explore Prompts</a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function toggleFavorite(id) {
        fetch(`/prompts/${id}/toggle-favorite`, {
            method: 'POST',
            headers: {
                'X-CSR-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const btn = document.getElementById(`fav-btn-${id}`);
            const icon = btn.querySelector('i');
            
            if (data.is_favorite) {
                btn.classList.add('active');
                icon.classList.replace('far', 'fas');
                Swal.fire({
                    icon: 'success',
                    title: 'Added to favorites!',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                });
            } else {
                btn.classList.remove('active');
                icon.classList.replace('fas', 'far');
                Swal.fire({
                    icon: 'info',
                    title: 'Removed from favorites',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                });
                
                // Optional: Remove card from DOM if we're on the favorites page
                if (window.location.pathname.includes('/prompts/favorites')) {
                    const card = btn.closest('.prompt-card');
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        card.remove();
                        // Check if last card removed to show empty state
                        if (document.querySelectorAll('.prompt-card').length === 0) {
                            window.location.reload();
                        }
                    }, 300);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
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
