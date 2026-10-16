@extends('template.frontend.master')
@section('title', 'Checkout | Feane')

@section('content')
<section class="wrap block">
  <h1>Cart and checkout</h1>
  @if (session('success'))
    <p class="msg ok" role="status">{{ session('success') }}</p>
  @endif
  @if ($errors->any())
    <div class="msg error" role="alert">
      <p>Please review the order information:</p>
      <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
  @endif
  <div class="cart-layout">
    <div class="panel">
      <h2>Your items</h2>
      <div id="checkout-items"></div>
      <p id="cart-empty" class="empty" hidden>Your cart is empty. <a href="{{ route('shop') }}">Browse products</a>.</p>
    </div>
    <div class="panel">
      <h2>Shipping details</h2>
      <p>Submit your order and the store will contact you to confirm it and arrange payment.</p>
      <div id="checkout-total"></div>
      <form id="checkout-form" method="post" action="{{ route('orders.store') }}">
        @csrf
        <label for="customer_name">Name</label>
        <input id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required maxlength="150">
        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required maxlength="255">
        <label for="phone">Phone</label>
        <input id="phone" name="phone" value="{{ old('phone') }}" required maxlength="40">
        <label for="address">Shipping address</label>
        <textarea id="address" name="address" required maxlength="2000">{{ old('address') }}</textarea>
        <div id="order-items-fields"></div>
        <button class="btn" type="submit">Place order</button>
      </form>
    </div>
  </div>
</section>
<script id="storefront-products" type="application/json">@json($products)</script>
@endsection
