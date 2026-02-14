@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h1>Categories</h1>
    <a href="{{ route('categories.create') }}" class="btn btn-primary">+ New Category</a>
</div>

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
