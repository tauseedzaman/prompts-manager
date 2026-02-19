@extends('layouts.app')

@section('title', 'Prompt Marketplace')

@section('content')
<div class="mb-8 flex justify-between items-end">
    <div>
        <h1 class="mb-2">Prompt Marketplace</h1>
        <p class="text-muted">Discover and copy high-quality prompts from the community.</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <!-- Sidebar Filters -->
    <div class="md:col-span-1 space-y-6">
        <div class="card">
            <h3 class="text-sm font-bold uppercase tracking-wider mb-4">Categories</h3>
            <div class="space-y-1">
                <a href="{{ route('marketplace.index') }}" class="block px-3 py-2 rounded-md text-sm {{ !request('category_id') ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/20' : 'hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    All Categories
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('marketplace.index', ['category_id' => $category->id]) }}" class="block px-3 py-2 rounded-md text-sm {{ request('category_id') == $category->id ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/20' : 'hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Feed -->
    <div class="md:col-span-3">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($prompts as $prompt)
                <div class="card p-0 flex flex-col hover:shadow-lg transition-shadow border-t-4" style="border-top-color: {{ $prompt->category->color ?? '#3b82f6' }}">
                    <div class="p-5 flex-grow">
                        <div class="flex justify-between items-start mb-2">
                            <span class="badge badge-gray text-[10px] uppercase font-bold">{{ $prompt->category->name }}</span>
                            <div class="flex items-center text-yellow-500 text-sm">
                                <i class="fas fa-star mr-1"></i>
                                <span>{{ $prompt->average_rating }}</span>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold mb-2 line-clamp-1">{{ $prompt->title }}</h3>
                        <p class="text-muted text-sm mb-4 line-clamp-2">{{ $prompt->description ?? 'No description provided.' }}</p>
                        
                        <a href="{{ route('users.show', $prompt->user->username ?? $prompt->user->id) }}" class="flex items-center gap-2 text-xs text-muted hover:text-indigo-600 transition-colors">
                            <img class="w-6 h-6 rounded-full object-cover" src="{{ $prompt->user->avatar_url }}" alt="{{ $prompt->user->name }}">
                            <span>{{ $prompt->user->name }}</span>
                        </a>
                    </div>
                    <div class="px-5 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <div class="flex gap-4 text-xs text-muted">
                            <span><i class="fas fa-code-branch mr-1"></i> {{ $prompt->forks_count ?? $prompt->forks()->count() }}</span>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="copyToClipboard('{{ route('marketplace.show', $prompt) }}')" class="btn btn-secondary btn-sm" title="Copy link to this prompt">
                                <i class="fas fa-link mr-1 text-[10px]"></i> Link
                            </button>
                            <a href="{{ route('marketplace.show', $prompt) }}" class="btn btn-secondary btn-sm">View</a>
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
                </div>
            @empty
                <div class="md:col-span-2 card py-12 text-center">
                    <i class="fas fa-globe text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium">No prompts found</h3>
                    <p class="text-muted">No public prompts have been shared yet.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $prompts->links() }}
        </div>
    </div>
</div>
@endsection
