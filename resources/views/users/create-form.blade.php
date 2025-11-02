@extends('layouts.main', [
    'title' => 'Create User',
])

@section('content')
    <div class="app-ly-max-width">
        <form action="{{ route('users.create') }}" method="post">
            @csrf

            <div class="app-cmp-form-detail">
                <label for="app-inp-name">Name</label>
                <input type="text" id="app-inp-name" name="name" value="{{ old('name') }}" required />

                <label for="app-inp-email">Email</label>
                <input type="email" id="app-inp-email" name="email" value="{{ old('email') }}" required />

                <label for="app-inp-password">Password</label>
                <input type="password" id="app-inp-password" name="password" required />

                <label for="app-inp-role">Role</label>
                <select id="app-inp-role" name="role">
                    <option value="USER" {{ old('role') == 'USER' ? 'selected' : '' }}>USER</option>
                    <option value="ADMIN" {{ old('role') == 'ADMIN' ? 'selected' : '' }}>ADMIN</option>
                </select>
            </div>

            <div class="app-cmp-form-actions">
                <button type="submit">Create</button>
                <a href="{{ session()->get('bookmarks.users.list', route('users.list')) }}"><button type="button">Cancel</button></a>
            </div>
        </form>
    </div>
@endsection
