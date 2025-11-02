@extends('layouts.main', [
    'title' => 'Users: Self' ,
])

@section('content')
@section('header')
    <nav>
        <ul class="app-cmp-links">
            <li>
                <a href="{{ route('users.self.update-form') }}">Update</a>
            </li>
            <li>
                <a href="{{ session()->get('bookmarks.users.selves.view', route('users.list')) }}">&lt; Back</a>
            </li>
        </ul>
    </nav>
@endsection

@section('content')
    <div class="app-ly-max-width">
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
