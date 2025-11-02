@extends('products.main', [
'title' => $product->code,
])
@section('header')
@endsection
@section('content')
<form action="{{ route('products.update', [
        'product' => $product->code,
    ]) }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="app-cmp-form-detail">
        <label for="app-inp-code">Code</label>
        <input type="text" name="code" value="{{ old('code', $product->code) }}"
            required />

        <label for="app-inp-name">Name</label>
        <input type="text" id="app-inp-name" name="name" value="{{ old('name', $product->name) }}" required />

        <label for="app-inp-category">Category</label>
        <select name="category_id" required>
            @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected($category->id == old('category_id', $product->category->id))>
                [{{ $category->code }}] {{ $category->name }}
            </option>
            @endforeach
        </select>

        <label for="app-inp-price">Price</label>
        <input type="number" id="app-inp-price" name="price" value="{{ old('price', $product->price) }}" step="any" required />

        <label for="app-inp-image">Image (upload)</label>
        @php
            // preview: ถ้ามี product->image หรือ public/images/{code}.* ให้แสดง
            $preview = $product->image ?? null;
            foreach (['jpg','png','gif','webp'] as $ext) {
                if (!$preview && file_exists(public_path("images/{$product->code}.{$ext}"))) {
                    $preview = asset("images/{$product->code}.{$ext}");
                }
            }
        @endphp
        @if(!empty($preview))
            <div style="margin-bottom:8px;">
                <img src="{{ $preview }}" alt="preview" class="product-thumb" />
            </div>
        @endif
        <input type="file" id="app-inp-image" name="image_file" accept="image/*" />
        <small class="helper">Upload an image to replace current one, or leave empty to keep it.</small>

        <label for="app-inp-description">Description</label>
        <textarea id="app-inp-description" name="description" cols="80" rows="10" required>{{ old('description', $product->description) }}</textarea>
    </div>

    <div class="app-cmp-form-actions">
        <button type="submit">Update</button>

        <a href="{{ route('products.view', [
'product' => $product->code,
]) }}">
            <button type="button">Cancel</button>
        </a>
    </div>
</form>
@endsection