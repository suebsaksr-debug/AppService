@extends('layouts.admin')
@section('title', $product->exists ? 'Edit product' : 'Add product')

@section('content')
<h1>{{ $product->exists ? 'Edit product' : 'Add product' }}</h1>
@if ($errors->any())
  <div class="msg error" role="alert"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif
<form class="panel form-card" method="post" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}">
  @csrf
  @if ($product->exists) @method('put') @endif
  <label for="name">Product name</label>
  <input id="name" name="name" value="{{ old('name', $product->name) }}" required maxlength="255">
  <label for="category">Category</label>
  <select id="category" name="category" required>
    @foreach ($categories as $category)
      <option value="{{ $category }}" @selected(old('category', $product->category) === $category)>{{ $category }}</option>
    @endforeach
  </select>
  <label for="price">Price</label>
  <input id="price" name="price" type="number" min="0" max="99999999.99" step="0.01" value="{{ old('price', $product->price) }}" required>
  <label for="description">Description</label>
  <textarea id="description" name="description" maxlength="5000">{{ old('description', $product->description) }}</textarea>
  <label for="image">Image path (for example images/categories/clothing.jpg)</label>
  <input id="image" name="image" value="{{ old('image', $product->image) }}" maxlength="2048" placeholder="images/categories/clothing.jpg or https://...">
  <label for="is_active">Visibility</label>
  <select id="is_active" name="is_active" required>
    <option value="1" @selected((string) old('is_active', $product->exists ? (int) $product->is_active : 1) === '1')>Visible in store</option>
    <option value="0" @selected((string) old('is_active', $product->exists ? (int) $product->is_active : 1) === '0')>Hidden</option>
  </select>
  <div class="links">
    <button class="btn" type="submit">Save product</button>
    <a href="{{ route('admin.products.index') }}">Cancel</a>
  </div>
</form>
@endsection
