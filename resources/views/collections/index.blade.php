@extends('layouts.app')

@section('title', 'Collections')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h1>Collections</h1>
    <a href="{{ route('collections.create') }}" class="btn btn-primary">+ New Collection</a>
</div>

<div class="grid grid-cols-2 gap-4">
    @foreach($collections as $collection)
    <div class="card">
        <div class="flex justify-between items-center mb-2">
            <h2 class="text-xl font-bold">
                <a href="{{ route('collections.show', $collection) }}">{{ $collection->name }}</a>
            </h2>
            <div class="flex gap-2">
                 <a href="{{ route('collections.edit', $collection) }}" class="text-muted text-sm hover:text-primary">Edit</a>
                 <form action="{{ route('collections.destroy', $collection) }}" method="POST" onsubmit="return confirm('Delete collection?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-danger text-sm hover:underline" style="background:none; border:none; cursor:pointer;">Delete</button>
                </form>
            </div>
        </div>
        <p class="text-muted mb-4">{{ $collection->description }}</p>
        <div class="flex items-center justify-between text-sm text-muted">
            <span>{{ $collection->prompts->count() }} prompts</span>
            <a href="{{ route('collections.show', $collection) }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">View</a>
        </div>
    </div>
    @endforeach
</div>
@endsection
