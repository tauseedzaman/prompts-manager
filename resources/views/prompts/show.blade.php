@extends('layouts.app')

@section('title', $prompt->title)

@section('content')
<div x-data="{
    promptText: @js($prompt->prompt_text),
    variables: @js($prompt->variables_schema ?? []),
    values: {},
    get rendered() {
        let text = this.promptText;
        this.variables.forEach(v => {
            const val = this.values[v] || '{{' + v + '}}';
            text = text.replaceAll('{{' + v + '}}', val);
        });
        return text;
    },
    copyRendered() {
        copyToClipboard(this.rendered);
    }
}" x-init="variables.forEach(v => values[v] = '')">

    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-2">
            <a href="{{ route('prompts.index') }}" class="btn btn-secondary">← Back</a>
        </div>
        <div class="flex gap-2">
            @if(Auth::id() == $prompt->user_id)
                <a href="{{ route('prompts.history', $prompt) }}" class="btn btn-secondary">History</a>
                <a href="{{ route('prompts.edit', $prompt) }}" class="btn btn-secondary">Edit</a>
                <form action="{{ route('prompts.destroy', $prompt) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            @endif
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
            <div class="flex gap-2 items-center">
                @if(Auth::id() == $prompt->user_id)
                    <span class="badge {{ $prompt->visibility == 'public' ? 'badge-blue' : 'badge-gray' }} flex items-center gap-1">
                        <i class="fas fa-{{ $prompt->visibility == 'public' ? 'globe' : 'lock' }} text-[10px]"></i>
                        {{ ucfirst($prompt->visibility) }}
                    </span>
                @endif
                @if($prompt->visibility == 'public')
                    <div class="flex items-center text-yellow-500 text-sm font-bold ml-2">
                        <i class="fas fa-star mr-1"></i> {{ $prompt->average_rating }}
                    </div>
                @endif
                <button @click="copyRendered()" class="btn btn-secondary flex items-center gap-2">
                    <i class="fas fa-copy"></i> Copy
                </button>
                <button @click="Swal.fire('Playground', 'Playground feature coming soon!', 'info')" class="btn btn-primary flex items-center gap-2">
                    <i class="fas fa-play"></i> Run Preview
                </button>
            </div>
        </div>

        @if($prompt->description)
            <div class="mb-4 text-muted">
                {{ $prompt->description }}
            </div>
        @endif

        <div style="background-color: #f8fafc; padding: 1.5rem; border-radius: 0.5rem; border: 1px solid var(--border-color); position: relative;">
            <pre id="prompt-text" x-text="rendered" style="white-space: pre-wrap; font-family: 'Inter', sans-serif; font-size: 1rem; color: var(--text-main); font-weight: 500;"></pre>
        </div>

        @if($prompt->tags->count() > 0)
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach($prompt->tags as $tag)
                    <span class="badge badge-gray">#{{ $tag->name }}</span>
                @endforeach
            </div>
        @endif
    </div>

    <template x-if="variables.length > 0">
        <div class="card mt-6">
            <h3 class="text-sm font-bold uppercase tracking-wider mb-6 flex items-center gap-2">
                <i class="fas fa-sliders-h text-blue-500"></i>
                Fill variables
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <template x-for="variable in variables" :key="variable">
                    <div class="form-group mb-0">
                        <label :for="'v-' + variable" class="form-label font-semibold text-xs mb-2 block text-gray-500 uppercase tracking-tight" x-text="variable"></label>
                        <input type="text" :id="'v-' + variable" x-model="values[variable]" class="form-control focus:ring-2 focus:ring-blue-500/20 transition-all" :placeholder="'Enter ' + variable + '...'">
                    </div>
                </template>
            </div>
        </div>
    </template>
</div>

@endsection
