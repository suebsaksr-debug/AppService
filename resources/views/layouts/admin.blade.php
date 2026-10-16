<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Admin') | Feane</title>
  @include('template.frontend.css')
</head>
<body>
  <header class="site">
    <div class="wrap bar">
      <a class="logo" href="{{ route('admin.products.index') }}">Feane Admin</a>
      <nav class="acct" aria-label="Admin navigation">
        <a href="{{ route('admin.products.index') }}">Products</a>
        <a href="{{ route('admin.orders.index') }}">Orders</a>
        <a href="{{ route('home') }}">View store</a>
        <span>{{ auth()->user()->name }}</span>
        <form method="post" action="{{ route('logout') }}">@csrf<button class="row-btn" type="submit">Log out</button></form>
      </nav>
    </div>
  </header>
  <main id="main" class="wrap block">
    @if (session('success'))<p class="msg ok" role="status">{{ session('success') }}</p>@endif
    @yield('content')
  </main>
</body>
</html>
