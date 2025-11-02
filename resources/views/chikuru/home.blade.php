@extends('layouts.main', [
    'title' => '',
])

@section('content')
<header class="chikuru-header">
    <div class="chikuru-header-inner">
        {{-- login removed here (kept in layout) --}}
    </div>
</header>

<section class="chikuru-section">
    {{-- เพิ่มแถบเน้นหัวข้อให้เด่นขึ้น --}}
    <div class="section-title-band">
        <h2 class="section-title">New item</h2>
    </div>

    <div class="products-grid chikuru-grid">
        @foreach ($products as $product)
        @php
            // find image (support uploaded files)
            $img = $product->image ?? null;
            foreach (['jpg','jpeg','png','gif','webp'] as $ext) {
                if (!$img && file_exists(public_path("images/{$product->code}.{$ext}"))) {
                    $img = asset("images/{$product->code}.{$ext}");
                }
            }
        @endphp

        <div class="product-card chikuru-card">
            <a href="{{ route('products.view', ['product' => $product->code]) }}" class="product-thumb-wrap">
                @if($img)
                <img src="{{ $img }}" alt="{{ $product->name }}" class="product-thumb" />
                @else
                <div class="product-thumb product-thumb--placeholder"></div>
                @endif
            </a>

            <div class="product-body">
                <div class="product-title">{{ $product->name }}</div>

                <div class="product-meta-small">จัดส่ง 3-5 วันทำการ</div>

                <div class="product-actions">
                    <a class="by-order" href="{{ route('login') }}">By Order</a>
                </div>

                <div class="product-price-wrap">
                    <div class="product-price">฿{{ number_format($product->price, 2) }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="products-pagination chikuru-pagination">
        {{ $products->withQueryString()->links() }}
    </div>
</section>
@endsection
