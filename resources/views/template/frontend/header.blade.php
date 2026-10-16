<div id="site-header"></div>
<script>window.FeaneAuth = @json(auth()->user() ? ['name' => auth()->user()->name, 'is_admin' => auth()->user()->is_admin] : null);</script>
@auth
  <div class="wrap admin-shortcut"><a href="{{ route('admin.dashboard') }}">Back to dashboard</a></div>
@endauth
<main id="main">
