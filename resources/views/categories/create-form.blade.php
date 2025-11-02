@extends('categories.main', [
    'title' => 'Create',
])

@section('content')
    @can('create', \App\Models\Category::class)
    <form action="{{ route('categories.create') }}" method="post">
        @csrf

        <div class="app-cmp-form-detail">
            <label for="app-inp-code">Code</label>
            <input type="text" id="app-inp-code" name="code" value="{{ old('code') }}" required />

            <label for="app-inp-name">Name</label>
            <input type="text" id="app-inp-name" name="name" value="{{ old('name') }}" required />


            <label for="app-inp-name">Description</label>
            <textarea id="app-inp-name" name="description" required>{{ old('description') }}</textarea>
        </div>

        <div class="app-cmp-form-actions">
            <a href="{{ session()->get('bookmarks.categories.create-form', route('categories.list')) }}">
            <button type="submit">Create</button></a>
            <a href="{{ session()->get(
                'bookmarks.categories.create-form',
                route('categories.list'),
            ) }}">
                <button type="button">Cancel</button>
            </a>
        </div>
    </form>
    @endcan
@endsection