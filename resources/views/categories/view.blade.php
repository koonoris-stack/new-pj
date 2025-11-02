@extends('categories.main', [
'title' => $category->name,
])

@section('content')
<nav>
    <form action="{{ route('categories.delete', [
            'category' => $category->code,
        ]) }}" method="post"
        id="app-form-delete">
        @csrf
    </form>

    <ul class="app-cmp-links">
            @can('update', $category)
            <li>
                <a
                    href="{{ route('categories.update-form', [
                        'category' => $category->code,
                    ]) }}">Update</a>
            </li>
            @endcan
            
            @can('delete', $category)
            <li class="app-cl-warn">
                <button type="submit" form="app-form-delete" class="app-cl-link">Delete</button>
            </li>
            @endcan

             <li>
@php
session()->put('bookmarks.categories.view-products', url()->full());
@endphp
            <a
            href="{{ route('categories.view-products', [
                 'category' => $category->code,
               ]) }}">View Products</a>
            </li>
            <li> <a href="{{ session()->get('bookmarks.categories.view', route('categories.list')) }}">Back</a></li>
        </ul>
</nav>
<dl class="app-cmp-data-detail">
    <dt>Code</dt>
    <dd>
        <span class="app-cl-code">{{ $category->code }}</span>
    </dd>

    <dt>Name</dt>
    <dd>
        {{ $category->name }}
    </dd>



    <dt>Description</dt>
    <dd>
        <pre style="margin: 0px;">{{ $category->description }}</pre>
    </dd>


</dl>
@endsection