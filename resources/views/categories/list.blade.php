@extends('categories.main', [
    'title' => 'List',
    'mainClasses' => ['app-ly-max-width'],
])

@section('content')
    <table class="app-cmp-data-list">
        <colgroup>
            <col style="width: 5ch;" />
        </colgroup>

         <search>
        <form action="{{ route('categories.list') }}" method="get" class="app-cmp-search-form">
            <div class="app-cmp-form-detail">
                <label for="app-criteria-term">Search</label>
                <input type="text" id="app-criteria-term" name="term" value="{{ $criteria['term'] }}" />

                
            </div>

            <div class="app-cmp-form-actions">
                <button type="submit" class="primary">Search</button>
                <a href="{{ route('categories.list') }}">
                    <button type="button" class="accent">X</button>
                </a>
            </div>
        </form>
    </search>
    <nav>
            <ul class="app-cmp-links">
                @php 
                    session()->put('bookmarks.categories.create-form', url()->full());
                @endphp
                @can('create', \App\Models\Category::class)
                <li>
                    <a href="{{ route('categories.create-form') }}">New Category</a>
                </li>
                @endcan
            </ul>
            {{ $categories->withQueryString()->links() }}
        </nav>

        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>No. of products</th>
                
            </tr>
        </thead>

        <tbody>
            @php
                session()->put('bookmarks.categories.view', url()->full());
            @endphp
            @foreach ($categories as $category)
                <tr>
                    <td>
                        <a href="{{ route('categories.view', [
                            'category' => $category->code,
                        ]) }}"
                            class="app-cl-code">
                            {{ $category->code }}
                        </a>
                    </td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->products_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection