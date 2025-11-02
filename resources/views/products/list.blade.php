@extends('products.main', [
'title' => 'List',
'mainClasses' => ['app-ly-max-width'],
])

@section('header')
<div class="app-cmp-links-bar">
    <nav>
        @php
        session()->put('bookmarks.products.create-form', url()->full());
        @endphp
        <ul class="app-cmp-links">

            @can('create', \App\Models\Product::class)

            <li>

                <a href="{{ route('products.create-form') }}">New Product</a>

            </li>

            @endcan

        </ul>
    </nav>

    {{ $products->withQueryString()->links() }}
</div>
@endsection

@section('content')
@php
    session()->put('bookmarks.products.view', url()->full());
@endphp

<div class="products-grid">
    @foreach ($products as $product)
    @php
        // หา image ตามลำดับ:
        // 1) product->image (URL stored)
        // 2) public/images/{code}.{ext} (ตรวจหลายนามสกุล)
        // 3) ถ้า code === 'pd001' ให้ตรวจหา public/images/cat1.{ext} แล้วใช้ (และตั้ง badge = 'cat1')
        $img = null;
        $badge = null;
        $exts = ['jpg','jpeg','png','gif','webp'];

        if (!empty($product->image)) {
            $img = $product->image;
        } else {
            foreach ($exts as $ext) {
                if (file_exists(public_path("images/{$product->code}.{$ext}"))) {
                    $img = asset("images/{$product->code}.{$ext}");
                    break;
                }
            }

            if (!$img && $product->code === 'pd001') {
                foreach ($exts as $ext) {
                    if (file_exists(public_path("images/cat1.{$ext}"))) {
                        $img = asset("images/cat1.{$ext}");
                        $badge = 'cat1';
                        break;
                    }
                }
            }
        }
    @endphp

    <div class="product-card">
        <a href="{{ route('products.view', ['product' => $product->code]) }}" class="product-card-link">
            @if($img)
                <div class="product-thumb-wrap">
                    <img src="{{ $img }}" alt="{{ $product->name }}" class="product-thumb" />
                    @if($badge)
                        <div class="image-badge">{{ $badge }}</div>
                    @endif
                </div>
            @else
                <div class="product-thumb product-thumb--placeholder"></div>
            @endif
        </a>

        <div class="product-meta">
            <div class="product-code">{{ $product->code }}</div>
            <div class="product-name" title="{{ $product->name }}">{{ $product->name }}</div>
            <div class="product-price">฿{{ number_format($product->price, 2) }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="products-pagination">
    {{ $products->withQueryString()->links() }}
</div>
@endsection