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
        <form action="{{ route('products.add-shops-form', [
'product' => $product->code,
]) }}" method="get" class="app-cmp-search-form">

            <div class="app-cmp-form-detail">
                <label for="app-criteria-term">Search</label>
                <input type="text" id="app-criteria-term" name="term" value="{{ $criteria['term'] ?? '' }}" />

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
            <li><a href="{{ session()->get('bookmarks.products.add-shops-form', route('products.view-shops', [
    'product' => $product->code,
])) }}">&lt; Back</a></li>
        </ul>
        {{ $shops->withQueryString()->links() }}



           <form action="{{ route('products.add-shop', [
'product' => $product->code,
]) }}" id="app-form-add-shop" method="post">
@csrf
</form>
    </nav>

 

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
                    form="app-form-add-shop"
                    name="shop" value="{{ $shop->code }}">
                    Add
                </button>
            </td>

        </tr>
        @endforeach
    </tbody>
</table>
@endsection