@extends('layouts.app')

@section('title', $user->name . ' (@' . $user->username . ')')

@section('content')
<div class="mb-8">
    <div class="card p-8">
        <div class="flex flex-col md:flex-row gap-8 items-center md:items-start text-center md:text-left">
            <div class="shrink-0">
                <img class="h-32 w-32 object-cover rounded-full border-4 border-indigo-100 dark:border-indigo-900 shadow-lg" src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
            </div>
            <div class="flex-grow">
                <div class="flex flex-col md:flex-row justify-between items-center md:items-start gap-4">
                    <div>
                        <h1 class="mb-1">{{ $user->name }}</h1>
                        <p class="text-indigo-600 dark:text-indigo-400 font-medium">@<span>{{ $user->username ?? strtolower(str_replace(' ', '', $user->name)) }}</span></p>
                    </div>
                    <div class="flex gap-4">
                        @auth
                            @if(auth()->id() !== $user->id)
                                <form action="{{ route('users.follow', $user) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn {{ auth()->user()->isFollowing($user) ? 'btn-secondary' : 'btn-primary' }}">
                                        {{ auth()->user()->isFollowing($user) ? 'Unfollow' : 'Follow' }}
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('profile.edit') }}" class="btn btn-secondary">Edit Profile</a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary">Follow</a>
                        @endauth
                    </div>
                </div>
                
                @if($user->bio)
                    <p class="mt-4 text-muted max-w-2xl mx-auto md:mx-0">{{ $user->bio }}</p>
                @endif

                <div class="flex gap-6 mt-6 justify-center md:justify-start">
                    <div class="text-center">
                        <div class="text-xl font-bold">{{ $stats['prompts_count'] }}</div>
                        <div class="text-xs text-muted font-bold uppercase tracking-wider">Prompts</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xl font-bold">{{ $stats['following_count'] }}</div>
                        <div class="text-xs text-muted font-bold uppercase tracking-wider">Following</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xl font-bold">{{ $stats['followers_count'] }}</div>
                        <div class="text-xs text-muted font-bold uppercase tracking-wider">Followers</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mb-6">
    <h2 class="text-xl font-bold">Public Prompts</h2>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
            </div>
            <div class="px-5 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center text-xs">
                <div class="flex gap-3 text-muted">
                    <span><i class="fas fa-code-branch mr-1"></i> {{ $prompt->forks_count ?? $prompt->forks()->count() }}</span>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('marketplace.show', $prompt) }}" class="btn btn-secondary btn-sm">View</a>
                    @auth
                        <form action="{{ route('prompts.fork', $prompt) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm">Fork</button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    @empty
        <div class="md:col-span-3 card py-12 text-center">
            <i class="fas fa-file-alt text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium">No public prompts</h3>
            <p class="text-muted">This user hasn't shared any prompts yet.</p>
        </div>
    @endforelse
</div>

<div class="mt-8">
    {{ $prompts->links() }}
</div>
@endsection
