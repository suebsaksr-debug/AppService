@extends('layouts.admin')
@section('title', 'Products')

@section('content')
<div class="section-head">
  <h1>Products</h1>
  <a class="btn" href="{{ route('admin.products.create') }}">Add product</a>
</div>
<div class="table-scroll">
  <table>
    <thead><tr><th>Name</th><th>Category</th><th>Price</th><th>Store status</th><th>Actions</th></tr></thead>
    <tbody>
      @forelse ($products as $product)
        <tr>
          <td>{{ $product->name }}</td>
          <td>{{ $product->category }}</td>
          <td>${{ number_format((float) $product->price, 2) }}</td>
          <td>{{ $product->is_active ? 'Active' : 'Hidden' }}</td>
          <td>
            <a class="row-btn" href="{{ route('admin.products.edit', $product) }}">Edit</a>
            <form method="post" action="{{ route('admin.products.destroy', $product) }}" style="display:inline" onsubmit="return confirm('Delete this product?')">
              @csrf @method('delete')
              <button class="row-btn danger" type="submit">Delete</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="5">No products yet. Add a product to make it visible in the store.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
{{ $products->links() }}
<p class="links"><a href="{{ route('admin.orders.index') }}">Manage customer orders</a></p>
@endsection
