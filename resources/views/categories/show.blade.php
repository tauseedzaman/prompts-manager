@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="flex items-center gap-2 mb-4">
    <a href="{{ route('categories.index') }}" class="btn btn-secondary">← Categories</a>
    <h1>{{ $category->name }}</h1>
</div>

<div class="card">
    <p>Showing prompts for category: <strong>{{ $category->name }}</strong></p>
    <a href="{{ route('prompts.index', ['category_id' => $category->id]) }}" class="btn btn-primary mt-4">View Prompts</a>
</div>
@endsection
