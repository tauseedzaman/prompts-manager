@extends('layouts.app')

@section('title', 'My Favorites')

@section('content')
<div class="flex flex-col gap-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">My Favorites</h1>
            <p class="text-muted mt-1 text-sm">Quick access to your most-valued AI prompts.</p>
        </div>
        <a href="{{ route('prompts.index') }}" class="btn btn-secondary">
            <i class="fas fa-search mr-2"></i> Explore All
        </a>
    </div>

    @if($prompts->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($prompts as $prompt)
                <div class="card flex flex-col h-full group" id="card-{{ $prompt->id }}">
                    <div class="flex justify-between items-start mb-4">
                        <span class="badge badge-blue">
                             {{ $prompt->category->name ?? 'Uncategorized' }}
                        </span>
                        <button onclick="toggleFavorite('{{ $prompt->id }}', this)" 
                                class="favorite-btn is-active" 
                                title="Remove from favorites">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>

                    <h3 class="text-xl font-bold mb-3 group-hover:text-blue-500 transition-colors">
                        <a href="{{ route('prompts.show', $prompt) }}">{{ $prompt->title }}</a>
                    </h3>
                    
                    <p class="text-muted text-sm mb-6 flex-grow leading-relaxed">
                        {{ Str::limit($prompt->description ?? $prompt->prompt_text, 120) }}
                    </p>
                    
                    @if($prompt->tags->count() > 0)
                        <div class="flex flex-wrap gap-2 mb-6">
                            @foreach($prompt->tags as $tag)
                                <span class="badge badge-gray text-[10px]">#{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex justify-between items-center pt-5 border-t border-gray-100 dark:border-white/5 mt-auto">
                        <button onclick="copyToClipboard(`{{ addslashes($prompt->prompt_text) }}`)" class="btn btn-secondary btn-sm px-4">
                             <i class="far fa-copy mr-1.5"></i> Copy
                        </button>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('prompts.edit', $prompt) }}" class="p-2 text-muted hover:text-blue-500 transition-colors">
                                <i class="far fa-edit"></i>
                            </a>
                            <a href="{{ route('prompts.show', $prompt) }}" class="p-2 text-muted hover:text-blue-500 transition-colors">
                                <i class="fas fa-external-link-alt text-xs"></i>
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
        <div class="flex flex-col items-center justify-center py-24 text-center bg-gray-50 dark:bg-white/5 rounded-[3rem] border border-gray-100 dark:border-white/5">
            <div class="w-24 h-24 bg-blue-600/10 rounded-3xl flex items-center justify-center mb-8 rotate-3">
                <i class="far fa-heart text-blue-500 text-4xl"></i>
            </div>
            <h2 class="text-2xl font-bold">No favorites yet</h2>
            <p class="text-muted mt-3 max-w-sm mx-auto">Items you mark with a heart will appear here for high-speed access.</p>
            <a href="{{ route('prompts.index') }}" class="btn btn-primary mt-10 px-8 py-3">
                 Start Exploring
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function toggleFavorite(id, btn) {
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
            const icon = btn.querySelector('i');
            
            if (data.is_favorite) {
                btn.classList.add('is-active');
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
                btn.classList.remove('is-active');
                icon.classList.replace('fas', 'far');
                
                // Remove card from DOM with animation if we're on the favorites page
                if (window.location.pathname.includes('/prompts/favorites')) {
                    const card = document.getElementById(`card-${id}`);
                    if (card) {
                        card.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(20px) scale(0.95)';
                        card.style.filter = 'blur(10px)';
                        
                        setTimeout(() => {
                            card.remove();
                            // Check if last card removed to show empty state
                            if (document.querySelectorAll('.card[id^="card-"]').length === 0) {
                                window.location.reload();
                            }
                        }, 400);
                    }
                }

                Swal.fire({
                    icon: 'info',
                    title: 'Removed from favorites',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                });
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
