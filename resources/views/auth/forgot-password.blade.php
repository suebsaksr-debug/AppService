<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reset password | Feane</title>
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
  <h2 id="title">Reset your password</h2>
  <div id="msg" class="msg" role="alert" hidden></div>
  <form id="ask" novalidate>
    <label for="email">Email</label><input id="email" type="email" autocomplete="email" required>
    <button class="btn" type="submit" style="margin-top:20px">Send reset link</button>
  </form>
  <form id="set" novalidate hidden>
    <label for="pw">New password (8+ characters)</label><input id="pw" type="password" autocomplete="new-password" minlength="8" required>
    <label for="pw2">Confirm new password</label><input id="pw2" type="password" autocomplete="new-password" required>
    <button class="btn" type="submit" style="margin-top:20px">Save new password</button>
  </form>
  <p class="links"><a href="{{ route('login') }}">Back to log in</a></p>
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
function nextPage() { var n = new URLSearchParams(location.search).get('next'); return /^[a-z-]+\.html$/.test(n || '') ? n : 'index.html'; }

var token = new URLSearchParams(location.search).get('token');
if (token) {
  if (F.resetTokenValid(token)) { $('ask').hidden = true; $('set').hidden = false; $('title').textContent = 'Choose a new password'; }
  else show('error', 'This reset link is invalid or has expired. Request a new one.');
}
$('ask').addEventListener('submit', function (e) {
  e.preventDefault();
  try {
    var t = F.requestReset($('email').value);
    if (t) {
      var m = $('msg'); m.className = 'msg info'; m.hidden = false; m.textContent = 'Demo mode: a real site would email this link. ';
      m.appendChild(el('a', { href: 'forgot-password.html?token=' + t, text: 'Open reset link' }));
    } else show('ok', 'If that email has an account, a reset link is on its way.');
  } catch (err) { show('error', err.message); }
});
$('set').addEventListener('submit', async function (e) {
  e.preventDefault();
  if ($('pw').value !== $('pw2').value) { show('error', 'The two passwords do not match.'); return; }
  try { await F.finishReset(token, $('pw').value); $('set').hidden = true; show('ok', 'Password updated. You can log in now.'); }
  catch (err) { show('error', err.message); }
});
})();
</script>
</body>
</html>
