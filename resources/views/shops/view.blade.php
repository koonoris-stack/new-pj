@extends('shops.main', [
    'title' => $shop->name,
])

@section('content')
  <nav>
        <form action="{{ route('shops.delete', [
            'shop' => $shop->code,
        ]) }}" method="post"
            id="app-form-delete">
            @csrf
        </form>

        <ul class="app-cmp-links">
            @can('update', $shop)
            <li>
                <a href="{{ route('shops.update-form', [
                        'shop' => $shop->code,
                    ]) }}">Update</a>
            </li>
            @endcan

            @can('delete', $shop)
            <li class="app-cl-warn">
                <button type="submit" form="app-form-delete" class="app-cl-link">Delete</button>
            </li>
            @endcan
@php
session()->put('bookmarks.shops.view-products', url()->full());
@endphp
            <li>
                <a
                    href="{{ route('shops.view-products', [
                        'shop' => $shop->code,
                    ]) }}">View Products</a>
            </li>
            <li> <a href="{{
session()->get('bookmarks.shops.view', route('shops.list', [
    'shop' => $shop->code,
]))}}">&lt; Back</a></li>
           
            
        </ul>
    </nav>
    <dl class="app-cmp-data-detail">
        <dt>Code</dt>
        <dd>
            <span class="app-cl-code">{{ $shop->code }}</span>
        </dd>

        <dt>Name</dt>
        <dd>
            {{ $shop->name }}
        </dd>

        <dt>Owner</dt>
        <dd>
            {{ $shop->owner }}
        </dd>

        <dt>Location</dt>
        <dd>
            <span class="app-cl-number">{{ $shop->latitude }}, {{ $shop->longitude }}</span>
        </dd>

        <dt>Address</dt>
        <dd>
            <pre style="margin: 0px;">{{ $shop->address }}</pre>
        </dd>
    </dl>
@endsection