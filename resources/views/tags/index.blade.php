@extends('layouts.app')

@section('title', 'Tags')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h1>Tags</h1>
    <a href="{{ route('tags.create') }}" class="btn btn-primary">+ New Tag</a>
</div>

@if(count($suggestions) > 0)
<div class="card mb-6 bg-slate-50 dark:bg-white/5 border-dashed">
    <h3 class="text-sm font-bold uppercase text-muted mb-3">Suggested Tags</h3>
    <div class="flex flex-wrap gap-3">
        @foreach($suggestions as $suggestion)
        <form action="{{ route('tags.store-suggestion') }}" method="POST">
            @csrf
            <input type="hidden" name="name" value="{{ $suggestion['name'] }}">
            <input type="hidden" name="color" value="{{ $suggestion['color'] }}">
            <button type="submit" class="px-3 py-1 rounded-full text-xs font-semibold transition-all hover:scale-110" style="background-color: {{ $suggestion['color'] }}; color: #fff;">
                #{{ $suggestion['name'] }}
            </button>
        </form>
        @endforeach
    </div>
</div>
@endif

<div class="card">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                <th style="padding: 0.75rem;">Name</th>
                <th style="padding: 0.75rem;">Slug</th>
                <th style="padding: 0.75rem;">Color</th>
                <th style="padding: 0.75rem; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tags as $tag)
            <tr style="border-bottom: 1px solid var(--border-color);">
                <td style="padding: 0.75rem;">
                    <a href="{{ route('tags.edit', $tag) }}" class="font-medium hover:underline">{{ $tag->name }}</a>
                </td>
                <td style="padding: 0.75rem;" class="text-muted text-sm">{{ $tag->slug }}</td>
                <td style="padding: 0.75rem;">
                    @if($tag->color)
                    <span style="display:inline-block; padding: 0.1rem 0.5rem; border-radius: 99px; background-color: {{ $tag->color }}; color: #fff; font-size: 0.75rem;">Example</span>
                    @else
                    <span class="text-muted">-</span>
                    @endif
                </td>
                <td style="padding: 0.75rem; text-align: right;">
                    <a href="{{ route('tags.edit', $tag) }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Edit</a>
                    <form action="{{ route('tags.destroy', $tag) }}" method="POST" style="display: inline-block; margin-left: 0.5rem;" onsubmit="return confirm('Delete this tag?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
