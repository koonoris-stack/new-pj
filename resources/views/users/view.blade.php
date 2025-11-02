@extends('layouts.main', [
    'title' => 'View User: ' . $user->email,
])
@section('header')
    <nav>
        <form action="{{ route('users.delete', [
            'user' => $user->id,
        ]) }}" method="post" id="app-form-delete">
            @csrf
        </form>

        <ul class="app-cmp-links">
            @can('update', $user)
            <li>
                <a href="{{ route('users.update-form', ['user' => $user->id]) }}">Update</a>
            </li>
            @endcan

            @can('delete', $user)
            <li class="app-cl-warn">
                <button type="submit" form="app-form-delete" class="app-cl-link">Delete</button>
            </li>
            @endcan

            <li>
                <a href="{{ session()->get('bookmarks.users.view', route('users.list')) }}">&lt; Back</a>
            </li>
        </ul>
    </nav>
@endsection

@section('content')
    <div class="app-ly-max-width">
        <h2>{{ $user->name }}</h2>

        <dl class="app-cmp-data-detail">
            <dt>Name</dt>
            <dd>{{ $user->name }}</dd>

            <dt>Email</dt>
            <dd>{{ $user->email }}</dd>

            <dt>Role</dt>
            <dd>{{ $user->role }}</dd>
        </dl>
    </div>
@endsection
