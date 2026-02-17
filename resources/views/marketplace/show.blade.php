@extends('layouts.app')

@section('title', $prompt->title . ' - Marketplace')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div class="flex items-center gap-2">
        <a href="{{ route('marketplace.index') }}" class="btn btn-secondary">← Back to Marketplace</a>
    </div>
    <div class="flex gap-2">
        @auth
            <form action="{{ route('prompts.fork', $prompt) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-code-branch mr-1"></i> Fork
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="btn btn-primary btn-sm" title="Login to fork this prompt">
                <i class="fas fa-code-branch mr-1"></i> Fork
            </a>
        @endauth
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <div class="md:col-span-2">
        <div class="card">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h1 class="mb-1">{{ $prompt->title }}</h1>
                    <div class="flex gap-2 items-center text-sm text-muted">
                        <span class="badge badge-blue">{{ $prompt->category->name }}</span>
                        @if($prompt->language) <span>{{ strtoupper($prompt->language) }}</span> @endif
                        @if($prompt->tone) <span>{{ ucfirst($prompt->tone) }}</span> @endif
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-yellow-500 text-lg font-bold mb-1">
                        <i class="fas fa-star mr-1"></i> {{ $prompt->average_rating }}
                    </div>
                    <div class="text-xs text-muted">{{ $prompt->ratings->count() }} ratings</div>
                </div>
            </div>

            @if($prompt->description)
                <div class="mb-6 text-muted">
                    {{ $prompt->description }}
                </div>
            @endif

            <div class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-xl border border-gray-200 dark:border-gray-700 relative group">
                <button onclick="copyToClipboard(document.getElementById('marketplace-prompt-text').innerText)" class="absolute top-4 right-4 p-2 bg-white dark:bg-gray-800 rounded-md shadow-sm border border-gray-200 dark:border-gray-700 opacity-0 group-hover:opacity-100 transition-opacity">
                    <i class="fas fa-copy"></i>
                </button>
                <pre id="marketplace-prompt-text" class="whitespace-pre-wrap font-sans text-main">{{ $prompt->prompt_text }}</pre>
            </div>

            <div class="mt-8">
                <h3 class="text-lg font-bold mb-4">Ratings & Reviews</h3>
                
                @auth
                    @if(Auth::id() !== $prompt->user_id)
                        <form action="{{ route('marketplace.rate', $prompt) }}" method="POST" class="mb-8 p-4 bg-blue-50 dark:bg-blue-900/10 rounded-lg">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-bold mb-2">Your Rating</label>
                                <div class="flex gap-4">
                                    @for($i = 1; $i <= 5; $i++)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="rating" value="{{ $i }}" class="hidden peer" required>
                                            <i class="fas fa-star text-2xl text-gray-300 peer-checked:text-yellow-500 hover:text-yellow-400"></i>
                                        </label>
                                    @endfor
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-bold mb-2">Review (Optional)</label>
                                <textarea name="comment" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">Submit Review</button>
                        </form>
                    @endif
                @else
                    <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800/50 border border-dashed border-gray-300 dark:border-gray-700 rounded-xl text-center">
                        <p class="text-muted mb-4">You must be logged in to rate and review prompts.</p>
                        <a href="{{ route('login') }}" class="btn btn-secondary btn-sm">Log In to Review</a>
                    </div>
                @endauth

                <div class="space-y-4">
                    @forelse($prompt->ratings as $rating)
                        <div class="border-b border-gray-100 dark:border-gray-800 pb-4">
                            <div class="flex justify-between mb-1">
                                <div class="font-bold text-sm">{{ $rating->user->name }}</div>
                                <div class="text-yellow-500 text-xs">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa{{ $i <= $rating->rating ? 's' : 'r' }} fa-star"></i>
                                    @endfor
                                </div>
                            </div>
                            <div class="text-sm text-muted italic">"{{ $rating->comment ?? 'No comment provided.' }}"</div>
                            <div class="text-[10px] text-muted mt-1">{{ $rating->created_at->diffForHumans() }}</div>
                        </div>
                    @empty
                        <p class="text-muted text-sm italic">No ratings yet. Be the first to rate!</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="md:col-span-1">
        <div class="card">
            <h3 class="text-sm font-bold uppercase tracking-wider mb-4">Prompt Info</h3>
            <div class="space-y-4">
                <div>
                    <span class="text-xs text-muted block">Created By</span>
                    <a href="{{ route('users.show', $prompt->user->username ?? $prompt->user->id) }}" class="flex items-center gap-2 mt-1 group">
                        <img class="w-8 h-8 rounded-full object-cover group-hover:ring-2 ring-indigo-500 transition-all" src="{{ $prompt->user->avatar_url }}" alt="{{ $prompt->user->name }}">
                        <span class="font-medium group-hover:text-indigo-600 transition-colors">{{ $prompt->user->name }}</span>
                    </a>
                </div>
                <div>
                    <span class="text-xs text-muted block">Created On</span>
                    <span class="font-medium">{{ $prompt->created_at->format('M d, Y') }}</span>
                </div>
                <div>
                    <span class="text-xs text-muted block">Forks</span>
                    <span class="font-medium"><i class="fas fa-code-branch mr-1"></i> {{ $prompt->forks->count() }}</span>
                </div>
                <div>
                    <span class="text-xs text-muted block">Usage Type</span>
                    <span class="badge badge-gray">{{ $prompt->usage_type ?? 'Standard' }}</span>
                </div>
            </div>
        </div>

        @if($prompt->tags->count() > 0)
        <div class="card mt-6">
            <h3 class="text-sm font-bold uppercase tracking-wider mb-4">Tags</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($prompt->tags as $tag)
                    <span class="badge badge-gray">#{{ $tag->name }}</span>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
