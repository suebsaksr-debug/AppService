@extends('layouts.admin')
@section('title', 'Orders')

@section('content')
<h1>Customer orders</h1>
<div class="table-scroll">
  <table>
    <thead><tr><th>Order</th><th>Customer</th><th>Items</th><th>Total</th><th>Status</th><th>Update</th></tr></thead>
    <tbody>
      @forelse ($orders as $order)
        <tr>
          <td>
            <strong>{{ $order->order_number }}</strong><br>
            <small>{{ $order->created_at->format('Y-m-d H:i') }}</small><br>
            <small>{{ $order->email }} · {{ $order->phone }}</small><br>
            <small>{{ $order->address }}</small>
          </td>
          <td>{{ $order->customer_name }}</td>
          <td>
            @foreach ($order->items as $item)
              <div>{{ $item->product_name }} × {{ $item->quantity }} (${{ number_format((float) $item->line_total, 2) }})</div>
            @endforeach
          </td>
          <td>${{ number_format((float) $order->total, 2) }}</td>
          <td>{{ ucfirst($order->status) }}</td>
          <td>
            <form method="post" action="{{ route('admin.orders.update', $order) }}">
              @csrf @method('patch')
              <select name="status" aria-label="Status for {{ $order->order_number }}">
                @foreach ($statuses as $status)
                  <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
                @endforeach
              </select>
              <button class="row-btn" type="submit">Save</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="6">No customer orders yet.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
{{ $orders->links() }}
@endsection
