@extends('layouts.main', [
    'title' => 'Update User',
])

@section('content')
    <div class="app-ly-max-width">
        <form action="{{ route('users.update', ['user' => $user->id]) }}" method="post">
            @csrf

            <div class="app-cmp-form-detail">
                <label for="app-inp-email">Email:{{ $user->email }}</label><br>
                <label for="app-inp-name">Name</label>
                <input type="text" id="app-inp-name" name="name" value="{{ old('name', $user->name) }}" required />

                
               

                <label for="app-inp-password">Password</label>
                <input type="password" id="app-inp-password" name="password" placeholder="Leave blank if you don't need to update" />

                <label for="app-inp-password-confirm">Confirm Password</label>
                <input type="password" id="app-inp-password-confirm" name="password_confirmation" placeholder="Repeat password" />

                @can('update', $user)
                    @if (auth()->user()->isAdministrator() && auth()->user()->id !== $user->id)
                        <label for="app-inp-role">Role</label>
                        <select id="app-inp-role" name="role">
                            <option value="USER" {{ old('role', $user->role) === 'USER' ? 'selected' : '' }}>USER</option>
                            <option value="ADMIN" {{ old('role', $user->role) === 'ADMIN' ? 'selected' : '' }}>ADMIN</option>
                        </select>
                    @else
                        <label>Role</label>
                        <div>{{ $user->role }}</div>
                    @endif
                @endcan
            </div>

            <div class="app-cmp-form-actions">
                <button type="submit">Update</button>
                <a href="{{ route('users.view', ['user' => $user->id]) }}"><button type="button">Cancel</button></a>
            </div>
        </form>
    </div>
@endsection
