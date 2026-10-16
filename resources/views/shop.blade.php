@extends('template.frontend.master')
@section('title', 'Shop | Feane')

@section('content')
<section class="wrap block">
  <div class="section-head">
    <h1>Shop all products</h1>
    <form action="{{ route('shop') }}" method="get" class="toolbar">
      <select name="category" aria-label="Filter by category">
        <option value="">All categories</option>
        @foreach ($categories as $category)
          <option value="{{ $category }}" @selected($selectedCategory === $category)>{{ $category }}</option>
        @endforeach
      </select>
      <input type="search" name="q" value="{{ request('q') }}" placeholder="Search products" aria-label="Search products">
      <button class="btn sm" type="submit">Filter</button>
    </form>
  </div>
  <div class="grid">
    @forelse ($products as $product)
      <article class="card">
        <img class="product-photo" src="{{ asset($product->image ?: 'images/categories/' . strtolower($product->category) . '.jpg') }}" alt="{{ $product->name }}" loading="lazy">
        <small class="muted">{{ $product->category }}</small>
        <h3>{{ $product->name }}</h3>
        <p>{{ $product->description }}</p>
        <div class="price">${{ number_format((float) $product->price, 2) }}</div>
        <button class="btn sm" type="button" data-cart-add="{{ $product->id }}">Add to cart</button>
      </article>
    @empty
      <p class="empty">No products match your search.</p>
    @endforelse
  </div>
  <div class="block">{{ $products->links() }}</div>
  <a class="btn" href="{{ route('checkout') }}">Go to cart / checkout</a>
</section>
@endsection
