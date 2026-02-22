@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h1>Categories</h1>
    <a href="{{ route('categories.create') }}" class="btn btn-primary">+ New Category</a>
</div>

@if(count($suggestions) > 0)
<div class="card mb-6 bg-slate-50 dark:bg-white/5 border-dashed">
    <h3 class="text-sm font-bold uppercase text-muted mb-3">Suggested Categories</h3>
    <div class="flex flex-wrap gap-3">
        @foreach($suggestions as $suggestion)
        <form action="{{ route('categories.store-suggestion') }}" method="POST">
            @csrf
            <input type="hidden" name="name" value="{{ $suggestion['name'] }}">
            <input type="hidden" name="color" value="{{ $suggestion['color'] }}">
            <input type="hidden" name="icon" value="{{ $suggestion['icon'] }}">
            <button type="submit" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all hover:scale-105" style="background-color: {{ $suggestion['color'] }}20; color: {{ $suggestion['color'] }}; border: 1px solid {{ $suggestion['color'] }}40;">
                <i class="{{ $suggestion['icon'] }}"></i>
                {{ $suggestion['name'] }}
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
                <th style="padding: 0.75rem;">Color</th>
                <th style="padding: 0.75rem;">Sort Order</th>
                <th style="padding: 0.75rem;">Active</th>
                <th style="padding: 0.75rem; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
            <tr style="border-bottom: 1px solid var(--border-color);">
                <td style="padding: 0.75rem;">
                    <a href="{{ route('categories.show', $category) }}" class="font-medium hover:underline">{{ $category->name }}</a>
                </td>
                <td style="padding: 0.75rem;">
                    <span style="display:inline-block; width: 16px; height: 16px; border-radius: 4px; background-color: {{ $category->color ?? '#cbd5e1' }}; vertical-align: middle;"></span>
                    {{ $category->color }}
                </td>
                <td style="padding: 0.75rem;">{{ $category->sort_order }}</td>
                <td style="padding: 0.75rem;">
                    @if($category->is_active)
                        <span class="badge badge-blue">Active</span>
                    @else
                        <span class="badge badge-gray">Inactive</span>
                    @endif
                </td>
                <td style="padding: 0.75rem; text-align: right;">
                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Edit</a>
                    <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display: inline-block; margin-left: 0.5rem;" onsubmit="return confirm('Delete this category?');">
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
