@extends('categories.main', [
    'title' => $category->code,
])

@section('content')
    @can('update', $category)
    <form action="{{ route('categories.update', [
        'category' => $category->code,
    ]) }}" method="post">
        @csrf

        <div class="app-cmp-form-detail">
            <label for="app-inp-code">Code</label>
            <input type="text" name="code" value="{{ old('code', $category->code) }}" required />

            <label for="app-inp-name">Name</label>
            <input type="text" id="app-inp-name" name="name" value="{{ old('name', $category->name) }}" required />



           <label for="app-inp-name">Description</label>
            <textarea name="description" cols="80" rows="10"
required>{{ old('description', $category->description) }}</textarea>
        </div>

        <div class="app-cmp-form-actions">
            <button type="submit">Update</button>

            <a href="{{ route('categories.view', [
                'category' => $category->code,
            ]) }}">
                <button type="button">Cancel</button>
            </a>

            
        </div>
    </form>
    @endcan
@endsection