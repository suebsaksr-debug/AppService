@extends('template.frontend.master')
@section('title', 'Online store | Feane')

@section('content')
<!-- HERO -->
<section class="wrap hero">
  <div>
    <h1>Well-made things for everyday life</h1>
    <p>Clothing, home goods and tech chosen for quality and built to last. Free shipping on orders over $50.</p>
    <div class="actions">
      <a class="btn" href="{{ route('shop') }}">Shop all products</a>
      <a class="btn ghost" href="#featured">See featured items</a>
    </div>
  </div>
  <img src="{{ asset('images/hero.jpg') }}" alt="A selection of stylish everyday clothing" width="1200" height="900">
</section>

<!-- Shop categories -->
<section class="wrap block">
  <div class="section-head"><h2>Shop by category</h2></div>
  <div class="cat-grid">
    @foreach ($categories as $category)
      <a class="tile" href="{{ route('shop', ['category' => $category]) }}">
        <img src="{{ asset('images/categories/' . strtolower($category) . '.jpg') }}" alt="{{ $category }}" loading="lazy">
        <span>{{ $category }}</span>
      </a>
    @endforeach
  </div>
</section>

<!-- Featured products from the database -->
<section class="wrap block" id="featured">
  <div class="section-head"><h2>Featured</h2><a href="{{ route('shop') }}">View all products</a></div>
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
      <p>No products are available yet.</p>
    @endforelse
  </div>
</section>

<!-- VALUE STRIP -->
<section class="band">
  <div class="wrap">
    <div><h3>Free shipping over $50</h3><p>Under $50 is a flat $5.95.</p></div>
    <!-- Edit these three promises to match your real policies. -->
    <div><h3>30-day returns</h3><p>Not right? Send it back within 30 days.</p></div>
    <div><h3>Real people to help</h3><p>Support is open Monday to Friday, 9am to 6pm.</p></div>
  </div>
</section>

<!-- PROMO BANNER -->
<section class="wrap block">
  <div class="promo">
    <div>
      <h2>New season, new wardrobe</h2>
      <p class="muted">Fresh layers, easy basics and the bags to carry them.</p>
      <a class="btn" href="{{ route('shop', ['category' => 'Clothing']) }}">Shop clothing</a>
    </div>
    <img src="{{ asset('images/promo.jpg') }}" alt="A fresh seasonal wardrobe" width="1280" height="720">
  </div>
</section>
@endsection
