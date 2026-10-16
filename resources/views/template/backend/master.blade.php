<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin dashboard !!!</title>
  @include('template.backend.css')
</head>
<body>
<div class="layout">
  @include('template.backend.sidebar')
  <main id="dashboard">
    <header class="topbar">
      <div>
        <div class="eyebrow">Store management</div>
        <h1>Dashboard</h1>
        <p class="top-desc">Welcome, {{ auth()->user()->name }}. Manage your product catalog.</p>
      </div>
      <div class="admin-chip">● &nbsp; Admin workspace</div>
    </header>

    <section class="stats" aria-label="Product summary">
      <div class="stat">
        <div class="stat-top">Total products <span class="stat-icon">▣</span></div>
        <strong>{{ number_format($totalProducts) }}</strong>
      </div>
      <div class="stat">
        <div class="stat-top">Visible in store <span class="stat-icon">▤</span></div>
        <strong>{{ number_format($activeProducts) }}</strong>
      </div>
      <div class="stat">
        <div class="stat-top">Combined product prices <span class="stat-icon">$</span></div>
        <strong>${{ number_format((float) $catalogValue, 2) }}</strong>
      </div>
    </section>

    <section class="panel" id="products">
      <div class="panel-head">
        <h2>Recent products</h2>
        <div class="tools">
          <a class="btn btn-secondary" href="{{ route('admin.products.index') }}">Manage all products</a>
          <a class="btn btn-primary" href="{{ route('admin.products.create') }}">＋ Add product</a>
        </div>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr><th>Product</th><th>Category</th><th>Price</th><th>Status</th><th>Actions</th></tr>
          </thead>
          <tbody>
            @forelse ($products as $product)
              <tr>
                <td>
                  <div class="product-cell">
                    <div class="thumb">
                      @if ($product->image)
                        <img src="{{ asset($product->image) }}" alt="">
                      @else
                        <span>{{ strtoupper(substr($product->name, 0, 1)) }}</span>
                      @endif
                    </div>
                    <div><div class="p-name">{{ $product->name }}</div></div>
                  </div>
                </td>
                <td>{{ $product->category }}</td>
                <td><strong>${{ number_format((float) $product->price, 2) }}</strong></td>
                <td>
                  <span class="pill {{ $product->is_active ? 'in-stock' : 'out-stock' }}">
                    {{ $product->is_active ? 'Visible' : 'Hidden' }}
                  </span>
                </td>
                <td>
                  <div class="actions">
                    <a class="icon-btn" href="{{ route('admin.products.edit', $product) }}" aria-label="Edit {{ $product->name }}" title="Edit">✎</a>
                    <form method="post" action="{{ route('admin.products.destroy', $product) }}" data-confirm="Delete this product?">
                      @csrf
                      @method('delete')
                      <button class="icon-btn" type="submit" aria-label="Delete {{ $product->name }}" title="Delete">🗑</button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="empty">No products yet. Add a product to get started.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="table-foot">
        <span>Showing {{ $products->count() }} of {{ number_format($totalProducts) }} products</span>
        <a href="{{ route('admin.products.index') }}">Open product management</a>
      </div>
    </section>
  </main>
</div>
@include('template.backend.js')
</body>
</html>
