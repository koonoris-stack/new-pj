@extends('shops.main', [
    'title' => $shop->code,
])

@section('content')
    <form action="{{ route('shops.update', [
        'shop' => $shop->code,
    ]) }}" method="post">
        @csrf

        <div class="app-cmp-form-detail">
            <label for="app-inp-code">Code</label>
            <input type="text" name="code" value="{{ old('code', $shop->code) }}" required />
            <label for="app-inp-name">Name</label>
            <input type="text" id="app-inp-name" name="name" value="{{ old('name', $shop->name) }}" required />

       <label for="app-inp-name">Owner</label>
            <input type="text" id="app-inp-name" name="owner" value="{{ old('owner', $shop->owner) }}" required />

            <label for="app-inp-name">latitude</label>
            <input type="text" id="app-inp-name" name="latitude" value="{{ old('latitude', $shop->latitude) }}" required />

            <label for="app-inp-name">longitude</label>
            <input type="text" id="app-inp-name" name="longitude" value="{{ old('longitude', $shop->longitude) }}" required />


           <label for="app-inp-name">Address</label>
           <textarea id="app-inp-name" name="address" cols="80" rows="10" required>{{ old('address', $shop->address) }}</textarea>
        </div>

        <div class="app-cmp-form-actions">
            <button type="submit">Update</button>
            <a href="{{ route('shops.view', [
                'shop' => $shop->code,
            ]) }}">
                <button type="button">Cancel</button>
            </a>
        </div>
    </form>
@endsection