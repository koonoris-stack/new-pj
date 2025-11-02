@extends('products.main', [
'title' => $product->code,
])

@section('header')
<nav>
    <form action="{{ route('products.delete', [
            'product' => $product->code,
        ]) }}" method="post"
        id="app-form-delete">
        @csrf
    </form>

    <ul class="app-cmp-links">
        @can('update', $product)
        <li>
            <a
                href="{{ route('products.update-form', [
                        'product' => $product->code,
                    ]) }}">Update</a>
        </li>
        @endcan
        
        @can('delete', $product)
        <li class="app-cl-warn">
            <button type="submit" form="app-form-delete" class="app-cl-link">Delete</button>
        </li>
        @endcan
        
        @php
        session()->put('bookmarks.products.view-shops', url()->full());
        @endphp
        <li>
            <a
                href="{{ route('products.view-shops', [
                 'product' => $product->code,
               ]) }}">View Shops</a>
        </li>

        <li>
            <a href="{{
session()->get('bookmarks.products.view', route('products.list'))
}}">&lt; Back</a>
        </li>
    </ul>




</nav>
@endsection

@section('content')
<div class="product-detail">
	<div class="product-gallery">
		@php
			$mainImg = $product->image ?? null;
			foreach (['jpg','jpeg','png','gif','webp'] as $ext) {
				if (!$mainImg && file_exists(public_path("images/{$product->code}.{$ext}"))) {
					$mainImg = asset("images/{$product->code}.{$ext}");
				}
			}
		@endphp

		<div class="product-gallery-main">
			@if($mainImg)
				<img src="{{ $mainImg }}" alt="{{ $product->name }}">
			@else
				<div class="product-thumb--placeholder" style="height:360px"></div>
			@endif
		</div>

		{{-- thumbs (optional): reuse same image for now --}}
		<div class="product-gallery-thumbs">
			@if($mainImg)
				<img src="{{ $mainImg }}" alt="thumb" />
			@endif
			{{-- ...additional thumbs could go here --}}
		</div>
	</div>

	<div class="product-info">
		<h2 class="pd-name">{{ $product->name }}</h2>

		<div class="pd-meta">
			<div class="pd-code"><strong>Code</strong> : {{ $product->code }}</div>
			@if(!empty($product->category))
				<div class="pd-category"><strong>Category</strong> : {{ $product->category->name }} ({{ $product->category->code }})</div>
			@endif
		</div>

		<div class="pd-price-row">
			<span class="price-badge">฿{{ number_format($product->price, 2) }}</span>
			@if(isset($product->old_price))
				<small class="old-price">฿{{ number_format($product->old_price,2) }}</small>
			@endif
		</div>

		<div class="pd-actions">
			@can('update', $product)
				<a href="{{ route('products.update-form', ['product' => $product->code]) }}" class="btn btn-primary">Update</a>
			@endcan
			@can('delete', $product)
				<form action="{{ route('products.delete', ['product' => $product->code]) }}" method="post" class="inline-form" onsubmit="return confirm('Delete this product?')">
					@csrf
					<button type="submit" class="btn btn-danger">Delete</button>
				</form>
			@endcan
			<a href="{{ route('products.list') }}" class="btn btn-ghost">&lt; Back</a>
		</div>

		<div class="pd-description">
			<h3>รายละเอียดสินค้า</h3>
			<p>{!! nl2br(e($product->description)) !!}</p>
		</div>
	</div>
</div>
@endsection