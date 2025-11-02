@extends('shops.main', [
    'title' => 'List',
    'mainClasses' => ['app-ly-max-width'],
])

@section('content')
    <table class="app-cmp-data-list">
        <colgroup>
            <col style="width: 5ch;" />
        </colgroup>

         <search>
        <form action="{{ route('shops.list') }}" method="get" class="app-cmp-search-form">
            <div class="app-cmp-form-detail">
                <label for="app-criteria-term">Search</label>
                <input type="text" id="app-criteria-term" name="term" value="{{ $criteria['term'] }}" />

                
            </div>

            <div class="app-cmp-form-actions">
                <button type="submit" class="primary">Search</button>
                <a href="{{ route('shops.list') }}">
                    <button type="button" class="accent">X</button>
                </a>
            </div>
        </form>
    </search>
    <nav>
            <ul class="app-cmp-links">
                @php 
                    session()->put('bookmarks.shops.create-form', url()->full());
                @endphp
                @can('create', \App\Models\Shop::class)
                <li>
                    <a href="{{ route('shops.create-form') }}">New Shop</a>
                </li>
                @endcan
            </ul>
            {{ $shops->withQueryString()->links() }}
        </nav>

        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Owner</th>
            </tr>
        </thead>

        <tbody>
            @php
                session()->put('bookmarks.shops.view', url()->full());
            @endphp
            @foreach ($shops as $shop)
                <tr>
                    <td>
                        <a href="{{ route('shops.view', [
                            'shop' => $shop->code,
                        ]) }}"
                            class="app-cl-code">
                            {{ $shop->code }}
                        </a>
                    </td>
                    <td>{{ $shop->name }}</td>
                    <td>{{ $shop->owner }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection