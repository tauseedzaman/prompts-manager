@extends('layouts.app')

@section('title', $collection->name)

@section('content')
<div class="flex items-center gap-2 mb-4">
    <a href="{{ route('collections.index') }}" class="btn btn-secondary">← Collections</a>
    <h1>{{ $collection->name }}</h1>
</div>

<div class="card mb-4">
    <p>{{ $collection->description }}</p>
</div>

<h2>Prompts in this Collection</h2>

@if($collection->prompts->count() > 0)
<div class="grid grid-cols-2 gap-4 mt-4">
    @foreach($collection->prompts as $prompt)
    <div class="card">
        <h3 class="text-lg font-bold mb-2">
            <a href="{{ route('prompts.show', $prompt) }}">{{ $prompt->title }}</a>
        </h3>
        <p class="text-muted text-sm mb-2">{{ Str::limit($prompt->prompt_text, 100) }}</p>
    </div>
    @endforeach
</div>
@else
<p class="text-muted">No prompts in this collection yet.</p>
@endif

@endsection
