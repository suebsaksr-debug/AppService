<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Create account | Feane</title>
  <!-- IMAGE PLACEHOLDER: favicon. Add images/favicon.png, then uncomment:
  <link rel="icon" href="images/favicon.png"> -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;700&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/shop.css">
</head>
<body>
<div id="site-header"></div>
<main id="main">

<section class="wrap block"><div class="panel form-card">
  <h2>Create an account</h2>
  <div id="msg" class="msg" role="alert" hidden></div>
  <form id="f" action="{{ route('register') }}" method="post" novalidate>
    @csrf
    <label for="name">Name</label><input id="name" name="name" autocomplete="name" required>
    <label for="email">Email</label><input id="email" name="email" type="email" autocomplete="email" required>
    <label for="pw">Password (8+ characters)</label><input id="pw" name="password" type="password" autocomplete="new-password" minlength="8" required>
    <label for="pw2">Confirm password</label><input id="pw2" name="password_confirmation" type="password" autocomplete="new-password" required>
    <button class="btn" type="submit" style="margin-top:20px">Create account</button>
  </form>
  <p class="links"><span>Already registered?</span><a href="{{ route('login') }}">Log in</a></p>
</div></section>
</main>
<div id="site-footer"></div>
<script src="js/store.js"></script>
<script src="js/shell.js"></script>
<script>
(function () {
'use strict';
var F = window.Feane, ui = F.ui, el = ui.el;
function $(id) { return document.getElementById(id); }
function show(kind, text) { var m = $('msg'); m.className = 'msg ' + kind; m.textContent = text; m.hidden = false; }
$('f').addEventListener('submit', async function (e) {
  e.preventDefault();
  if ($('pw').value !== $('pw2').value) { show('error', 'The two passwords do not match.'); return; }
  try {
    var result = await F.register($('name').value, $('email').value, $('pw').value, $('f').action);
    location.href = result.redirect || '/login';
  }
  catch (err) { show('error', err.message); }
});
})();
</script>
</body>
</html>
