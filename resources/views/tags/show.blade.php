@extends('layouts.app')

@section('title', $tag->name)

@section('content')
<div class="flex items-center gap-2 mb-4">
    <a href="{{ route('tags.index') }}" class="btn btn-secondary">← Tags</a>
    <h1>{{ $tag->name }}</h1>
</div>

<div class="card">
    <p>Showing prompts for tag: <strong>{{ $tag->name }}</strong></p>
    <a href="{{ route('prompts.index', ['tag_id' => $tag->id]) }}" class="btn btn-primary mt-4">View Prompts</a>
</div>
@endsection
