@extends('layouts.main', [
    'title' => 'Users',
])

@section('content')
    <div class="app-ly-max-width">
        @can('viewAny', App\Models\User::class)
            @php
                session()->put('bookmarks.users.list', url()->full());
            @endphp

            <div class="app-cmp-links-bar">
                <div>
                    <ul class="app-cmp-links">
                        <li><a href="{{ route('users.create-form') }}">New User</a></li>
                    </ul>
                </div>
            </div>
        @endcan

        <form class="app-cmp-search-form" action="{{ route('users.list') }}" method="get">
            <div>
                <label for="app-inp-term">Search</label>
                <input id="app-inp-term" type="text" name="term" value="{{ $criteria['term'] ?? '' }}" />
            </div>

            <div>
                <button type="submit">Search</button>
                <a href="{{ route('users.list') }}"><button type="button" class="app-button-secondary">X</button></a>
            </div>
        </form>

        <table class="app-cmp-data-list">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Name</th>
                    <th>Role</th>
                    
                </tr>
                  @php
        session()->put('bookmarks.users.view', url()->full());
        @endphp
            </thead>
            <tbody>
              
                @foreach ($users as $user)
                    <tr>
                        <td><a href="{{ route('users.view', ['user' => $user->id]) }}">{{ $user->email }}</a></td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->role }}</td>
                        
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $users->links() }}
    </div>
@endsection
