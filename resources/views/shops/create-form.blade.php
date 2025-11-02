@extends('shops.main', [
    'title' => 'Create',
])

@section('content')
    <form action="{{ route('shops.create') }}" method="post">
        @csrf

        <div class="app-cmp-form-detail">
            <label for="app-inp-code">Code</label>
           <input type="text" name="code" value="{{ old('code') }}" required />

            <label for="app-inp-name">Name</label>
            <input type="text" id="app-inp-name" name="name" value="{{ old('name') }}" required />

            <label for="app-inp-name">Owner</label>
            <input type="text" id="app-inp-name" name="owner" value="{{ old('owner') }}" required />

            <label for="app-inp-name">latitude</label>
            <input type="text" id="app-inp-name" name="latitude" value="{{ old('latitude') }}" required />

            <label for="app-inp-name">longitude</label>
            <input type="text" id="app-inp-name" name="longitude" value="{{ old('longitude') }}" required />

            <label for="app-inp-name">Address</label>
            <textarea name="description" cols="80" rows="10"
required>{{ old('description') }}</textarea>
        </div>

        <div class="app-cmp-form-actions">
            <a href="{{ session()->get('bookmarks.shops.create-form', route('shops.list')) }}">
            <button type="submit">Create</button></a>
            <a href="{{ session()->get(
                'bookmarks.shops.create-form',
                route('shops.list'),
            ) }}">
                <button type="button">Cancel</button>
            </a>
        </div>
    </form>
@endsection