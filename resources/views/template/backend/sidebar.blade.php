<aside class="sidebar">
  <a class="brand" href="{{ route('admin.dashboard') }}">feane<span>.</span> <small style="font:600 12px 'Source Sans 3';color:#9ca3af">ADMIN</small></a>
  <div>
    <div class="nav-label">Workspace</div>
    <nav class="nav">
      <a class="active" href="{{ route('admin.dashboard') }}"><span class="ico">▦</span> Dashboard</a>
      <a href="{{ route('admin.products.index') }}"><span class="ico">▣</span> Products</a>
      <a href="{{ route('admin.orders.index') }}"><span class="ico">▤</span> Orders</a>
      <a href="{{ route('home') }}"><span class="ico">↗</span> View store</a>
    </nav>
  </div>
  <div class="sidebar-note">
    {{ auth()->user()->name }}<br>
    <form method="post" action="{{ route('logout') }}">
      @csrf
      <button class="btn btn-secondary" type="submit">Log out</button>
    </form>
  </div>
</aside>
