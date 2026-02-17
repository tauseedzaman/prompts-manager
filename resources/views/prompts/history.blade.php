@extends('layouts.app')

@section('title', 'History - ' . $prompt->title)

@section('content')
<div class="flex justify-between items-center mb-4">
    <div class="flex items-center gap-2">
        <a href="{{ route('prompts.show', $prompt) }}" class="btn btn-secondary">← Back to Prompt</a>
    </div>
</div>

<div class="mb-6">
    <h1 class="mb-1">Version History</h1>
    <p class="text-muted">Track all changes made to "{{ $prompt->title }}" over time.</p>
</div>

<div class="space-y-6">
    @forelse($versions as $version)
        <div class="card p-0 overflow-hidden">
            <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <i class="fas fa-history"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-main">{{ $version->created_at->format('M d, Y \a\t H:i') }}</div>
                        <div class="text-xs text-muted">Edited by {{ $version->user->name }}</div>
                    </div>
                </div>
                <button 
                    onclick="copyToClipboard(`{{ addslashes($version->prompt_text) }}`)"
                    class="btn btn-primary btn-sm"
                >
                    <i class="fas fa-copy mr-1"></i> Copy This Version
                </button>
            </div>
            <div class="px-6 py-4">
                <div class="mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-muted block mb-1">Title at this version</span>
                    <div class="text-main font-medium">{{ $version->title }}</div>
                </div>
                
                @if($version->description)
                <div class="mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-muted block mb-1">Description</span>
                    <div class="text-muted text-sm">{{ $version->description }}</div>
                </div>
                @endif

                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-muted block mb-1">Prompt Text</span>
                    <div class="bg-gray-100 dark:bg-gray-900/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                        <pre class="text-sm whitespace-pre-wrap font-sans text-main">{{ $version->prompt_text }}</pre>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card text-center py-12">
            <div class="mb-4">
                <i class="fas fa-history text-4xl text-gray-300"></i>
            </div>
            <h3 class="text-lg font-medium">No history yet</h3>
            <p class="text-muted">Changes will appear here once you update this prompt.</p>
        </div>
    @endforelse

    <div class="mt-4">
        {{ $versions->links() }}
    </div>
</div>
@endsection
