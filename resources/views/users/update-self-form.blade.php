@extends('layouts.main', [
    'title' => 'Users: Self Update ' . $user->email,
])

@section('content')
    <div class="app-ly-max-width">
    <form action="{{ route('users.self.update') }}" method="post">
            @csrf

            <div class="app-cmp-form-detail">

                <label for="app-inp-email">Email</label>
                <div>{{ $user->email }}</div>

                <label for="app-inp-role">Role</label>
                <div>{{ $user->role }}</div>

                <label for="app-inp-name">Name</label>
                <input type="text" id="app-inp-name" name="name" value="{{ old('name', $user->name) }}" required />

                <label for="app-inp-password">Password</label>
                <input type="password" id="app-inp-password" name="password" placeholder="Leave blank if you don't need to update" />

                <label for="app-inp-password-confirm">Confirm Password</label>
                <input type="password" id="app-inp-password-confirm" name="password_confirmation" placeholder="Repeat password" />

            </div>

            <div class="app-cmp-form-actions">
                <button type="submit">Update</button>
                <a href="{{ route('users.self.view') }}"><button type="button" class="app-button-secondary">Cancel</button></a>
            </div>
        </form>
    </div>
@endsection
