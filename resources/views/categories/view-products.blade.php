@extends('categories.main', [
    'title' => $category->code . ' Product',
    'mainClasses' => ['app-ly-max-width'],
])

@section('header')
    <search>
        <form action="{{ route('categories.view-products', [
            'category' => $category->code,
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
                <a href="{{ route('categories.view-products', [
                    'category' => $category->code,
                ]) }}">
                    <button type="button" class="accent">X</button>
                </a>
            </div>
        </form>
    </search>

  <nav class="app-cmp-links-bar">
<ul class="app-cmp-links">
{{-- Add back link to categories-view --}}
<li><a href="{{ session()->get('bookmarks.categories.view-products', route('categories.view', [
    'category' => $category->code,
])) }}">Back</a></li>

@can('update', $category)
<li>
    @php
    session()->put('bookmarks.categories.add-products-form', url()->full());
    @endphp
    <a href="{{ route('categories.add-products-form', [
'category' => $category->code,
]) }}">&lt; add product</a>
</li>
@endcan
</ul>
{{ $products->withQueryString()->links() }}
</nav>
@endsection

@section('content')
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
            </tr>
        </thead>

        <tbody>
            @php
session()->put('bookmarks.products.view', url()->full());
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
                    <td>{{ $product->category->name }}</td>
                    <td class="app-cl-number">{{ number_format($product->price, 2) }}</td>
                    <td class="app-cl-number">{{ $product->shops_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection