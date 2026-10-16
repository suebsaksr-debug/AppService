<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Log in | Feane</title>
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
  <h2>Log in</h2>
  <div id="msg" class="msg" role="alert" hidden></div>
  <form id="f" action="{{ route('login') }}" method="post" novalidate>
    @csrf
    <label for="email">Email</label><input id="email" name="email" type="email" autocomplete="email" required>
    <label for="pw">Password</label><input id="pw" name="password" type="password" autocomplete="current-password" required>
    <button class="btn" type="submit" style="margin-top:20px">Log in</button>
  </form>
  <p class="links"><a href="{{ route('password.request') }}">Forgot your password?</a><a href="{{ route('register') }}">Create an account</a></p>
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
  try {
    var result = await F.login($('email').value, $('pw').value, $('f').action);
    location.href = result.redirect || '/';
  }
  catch (err) { show('error', err.message); }
});
})();
</script>
</body>
</html>
