<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/common.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/product.css') }}" />

    <title>{{ $title }}</title>
</head>

<body>
    <!-- top banner with big white title -->
    <div class="top-banner">
        <div class="top-banner-inner">
            <span class="top-banner-title">Chikuru book</span>

            {{-- center search form --}}
            <div class="top-banner-search">
                <form action="{{ route('search') }}" method="get" class="top-search-form">
                    <input type="text" name="term" placeholder="ค้นหาสินค้า" value="{{ request('term') ?? '' }}" />
                </form>
            </div>

            <div class="top-banner-actions">
                @auth
                    <!-- User name as a button/link to user's self page (first) -->
                    <a href="{{ route('users.self.view') }}" class="top-user-link">{{ \Auth::user()->name }}</a>

                    <!-- Logout form (after user) -->
                    <form action="{{ route('logout') }}" method="post" style="display:inline;margin-left:8px;">
                        @csrf
                        <button type="submit" class="top-login-btn">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="top-login-link">Login</a>
                @endauth
            </div>
        </div>
    </div>

    {{-- Minimal header: brand removed, search removed, keep login --}}
    <header class="chikuru-header" id="app-cmp-main-header">
        <div class="chikuru-header-inner">
            {{-- brand & search removed to keep header minimal --}}

        </div>
    </header>

    <main id="app-cmp-main-content" @class($mainClasses ?? [])>
        @php
        // Keep a bookmark for the users self-view. Do not overwrite while on self routes.
        if (!Route::is('users.self.*') && !Route::is('users.selves.*')) {
        session()->put('bookmarks.users.selves.view', url()->full());
        }
        @endphp
        <header>
            <h1><span @class($titleClesses ?? [])>{{ $title }}</span></h1>
            <div class="app-cmp-notifications">
                @session('status')
                <div role="status">
                    {{ $value }}
                </div>
                @endsession
            </div>
            <div class="app-cmp-notifications">
                {{-- status message --}}

                @error('alert')
                <div role="alert">
                    {{ $message }}
                </div>
                @enderror
            </div>
            @yield('header')
        </header>

        @yield('content')
    </main>

    <footer id="app-cmp-main-footer">
        &#xA9; Copyright Pachara's Database.
    </footer>
</body>

</html>