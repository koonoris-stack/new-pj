@extends('products.main', [
'title' => $product->code . ' Shops',
'mainClasses' => ['app-ly-max-width'],
])

@section('content')
<table class="app-cmp-data-list">
    <colgroup>
        <col style="width: 5ch;" />
    </colgroup>

    <search>
        <form action="{{ route('products.view-shops', [
'product' => $product->code,
]) }}" method="get" class="app-cmp-search-form">

            <div class="app-cmp-form-detail">
                <label for="app-criteria-term">Search</label>
                <input type="text" id="app-criteria-term" name="term" value="{{ $criteria['term'] }}" />

            </div>

            <div class="app-cmp-form-actions">
                <button type="submit" class="primary">Search</button>
                <a href="{{ route('products.view-shops', [
'product' => $product->code,
]) }}">
                    <button type="button" class="accent">X</button>
                </a>
            </div>
        </form>
    </search>

    <nav class="app-cmp-links-bar">
        <ul class="app-cmp-links">
            {{-- Add back link to product-view --}}
            <li> <a href="{{
session()->get('bookmarks.products.view-shops', route('products.view', [
    'product' => $product->code,
]  ))
}}">&lt; Back</a></li>

            @can('update', $product)
            <li><a href="{{ route('products.add-shops-form', [
'product' => $product->code,
]) }}">&lt; add shop</a></li>
            @endcan

        </ul>

        {{ $shops->withQueryString()->links() }}
    </nav>

    @can('update', $product)
    <form action="{{ route('products.remove-shop', [
'product' => $product->code,
]) }}" id="app-form-remove-shop" method="post">
        @csrf

    </form>
 @endcan
    <thead>
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Owner</th>
            <th>No. of products</th>
            <th></th>
        </tr>
    </thead>

    <tbody>
        @php
        session()->put('bookmarks.products.add-shops-form', url()->full());
        @endphp
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
            <td>{{ $shop->products_count }}</td>
            <td>
                <button type="submit"
                    form="app-form-remove-shop"
                    name="shop" value="{{ $shop->code }}">
                    Remove
                </button>
            </td>

        </tr>
        @endforeach
    </tbody>
</table>
@endsection