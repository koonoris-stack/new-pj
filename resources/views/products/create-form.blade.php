@extends('products.main', [
'title' => 'Create',
])

@section('content')
<form action="{{ route('products.create') }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="app-cmp-form-detail">
        <label for="app-inp-code">Code</label>
        <input type="text" id="app-inp-code" name="code" value="{{ old('code') }}" required />

        <label for="app-inp-name">Name</label>
        <input type="text" id="app-inp-name" name="name" value="{{ old('name') }}" required />

        <label for="app-inp-category">Category</label>
        <select id="app-inp-category" name="category_id" required>
            <option value="">-- Please select category --</option>
            @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected($category->id == old('category_id'))>
                [{{ $category->code }}] {{ $category->name }}
            </option>
            @endforeach
        </select>

        <label for="app-inp-price">Price</label>
        <input type="number" id="app-inp-price" name="price" value="{{ old('price') }}" step="any" required />

        <label for="app-inp-image">Image (upload)</label>
        <input type="file" id="app-inp-image" name="image_file" accept="image/*" />
        <small class="helper">OR you may leave empty and later use Image URL</small>

        <label for="app-inp-description">Description</label>
        <textarea id="app-inp-description" name="description" cols="80" rows="10" required>{{ old('description') }}</textarea>
    </div>

    <div class="app-cmp-form-actions">
        <button type="submit" class="primary-btn">Create</button>

        <a href="{{ session()->get('bookmarks.products.create-form', route('products.list')) }}" class="secondary-btn">
            Cancel
        </a>
    </div>

</form>
@endsection