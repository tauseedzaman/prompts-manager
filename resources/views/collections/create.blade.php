@extends('layouts.app')

@section('title', 'Create Collection')

@section('content')
<div class="flex items-center gap-2 mb-4">
    <a href="{{ route('collections.index') }}" class="btn btn-secondary">← Back</a>
    <h1>Create Collection</h1>
</div>

<div class="card">
    <form action="{{ route('collections.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
            @error('name') <div class="text-danger text-sm">{{ $message }}</div> @enderror
        </div>

@endsection
