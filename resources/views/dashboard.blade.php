@extends('template.frontend.master')
@section('title', 'Dashboard')

@section('content')
<section class="wrap block">
  <h1>Welcome, {{ auth()->user()->name }}</h1>
  <p>Your account is ready.</p>
  @if (auth()->user()->is_admin)
    <a class="btn" href="{{ route('admin.products.index') }}">Open Admin dashboard</a>
  @else
    <a class="btn" href="{{ route('shop') }}">Continue shopping</a>
  @endif
</section>
@endsection
