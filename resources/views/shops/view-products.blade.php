@extends('shops.main', [
    'title' => $shop->code . ' Product',
    'mainClasses' => ['app-ly-max-width'],
])

@section('content')
    <search>
        <form action="{{ route('shops.view-products', [
            'shop' => $shop->code,
        ]) }}" method="get" class="app-cmp-search-form">
            <div class="app-cmp-form-detail">
                <label for="app-criteria-term">Search</label>
                <input type="text" id="app-criteria-term" name="term" value="{{ $criteria['term'] }}" />

                 <label for="app-criteria-min-price">Min Price</label>
                <input type="number" id="app-criteria-min-price" name="minPrice" value="{{ $criteria['minPrice'] }}"
                    step="any" />

                <label for="app-criteria-max-price">Max Price</label>
                <input type="number" id="app-criteria-max-price" name="maxPrice" value="{{ $criteria['maxPrice'] }}"
                    step="any" />

            </div>

            

            <div class="app-cmp-form-actions">
                <button type="submit" class="primary">Search</button>
                <a href="{{ route('shops.view-products', [
                    'shop' => $shop->code,
                ]) }}">
                    <button type="button" class="accent">X</button>
                </a>
            </div>
        </form>
    </search>

 <nav class="app-cmp-links-bar">
<ul class="app-cmp-links">
{{-- Add back link to shops-view --}}
<li><a href="{{ session()->get('bookmarks.shops.view-products', route('shops.view', [
    'shop' => $shop->code,
])) }}">&lt; Back</a></li>


</li>
@can('update', $shop)
<li><a href="{{ route('shops.add-products-form', [
    'shop' => $shop->code,
]) }}">&lt; add product</a></li>
@endcan
</ul>
{{ $products->withQueryString()->links() }}



@can('update', $shop)
<form action="{{ route('shops.remove-product', [
    'shop' => $shop->code,

]) }}" id="app-form-remove-product" method="post">
@csrf
</form>
@endcan
</nav>






    <table class="app-cmp-data-list">
        <colgroup>
            <col style="width: 5ch;" />
        </colgroup>

        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>No. of Shops</th>
                <th></th>
            </tr>
        </thead>

        <tbody>
            @php
session()->put('bookmarks.products.view', url()->full());
@endphp
@php
    session()->put('bookmarks.shops.add-products-form', url()->full());
@endphp
@php
session()->put('bookmarks.categories.view', url()->full());
@endphp
            @foreach ($products as $product)
                <tr>
                    <td>
                        <a href="{{ route('products.view', [
                            'product' => $product->code,
                        ]) }}"
                            class="app-cl-code">
                            {{ $product->code }}
                        </a>
                    </td>
                    <td>{{ $product->name }}</td>
                    <td><a href="{{ route('categories.view', [
                        'category' => $product->category->code,
                    ]) }}">{{ $product->category->name }}</a></td>
                    <td class="app-cl-number">{{ number_format($product->price, 2) }}</td>
                    <td class="app-cl-number">{{ $product->shops_count }}</td>
                     @can('update', $shop)
                     <td>
<button type="submit"
form="app-form-remove-product"
name="product" value="{{ $product->code }}">
Remove
</button>
</td>
@endcan
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection



