/* Shared storefront shell: header, footer, product card, toast. Loaded after store.js on every page. */
(function () {
  'use strict';
  var F = window.Feane;

  function el(tag, attrs, kids) {
    var e = document.createElement(tag);
    Object.keys(attrs || {}).forEach(function (k) {
      if (k === 'text') e.textContent = attrs[k];
      else if (k === 'class') e.className = attrs[k];
      else e.setAttribute(k, attrs[k]);
    });
    (kids || []).forEach(function (c) { e.appendChild(c); });
    return e;
  }
  function slug(s) { return String(s).toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, ''); }
  function params() { return new URLSearchParams(location.search); }

  /* Product image. If the product has an image path it renders an <img>;
     otherwise it renders a placeholder box that names the file to add. */
  function media(p, size, suffix) {
    if (p.image && !suffix) return el('img', { src: p.image, alt: p.name, loading: 'lazy' });
    if (!suffix && p.category) return el('img', {
      src: 'images/categories/' + slug(p.category) + '.jpg', alt: p.name, loading: 'lazy'
    });
    return el('div', { class: 'ph', role: 'img', 'aria-label': 'Image placeholder for ' + p.name,
      'data-ph': 'images/products/' + slug(p.name) + (suffix || '') + '.jpg', 'data-size': size || '800 × 800' });
  }

  function toast(msg) {
    var t = document.getElementById('toast');
    if (!t) { t = el('div', { id: 'toast', class: 'toast', role: 'status' }); document.body.appendChild(t); }
    t.textContent = msg; t.classList.add('show');
    clearTimeout(toast._t); toast._t = setTimeout(function () { t.classList.remove('show'); }, 2200);
  }
  function badge() { var b = document.getElementById('cartCount'); if (b) b.textContent = F.cart.count(); }

  function card(p) {
    var href = 'product.html?id=' + encodeURIComponent(p.id);
    var add = el('button', { type: 'button', class: 'btn sm', text: 'Add to cart' });
    add.addEventListener('click', function () { F.cart.add(p.id, 1); badge(); toast('Added ' + p.name + ' to your cart.'); });
    return el('article', { class: 'card' }, [
      el('a', { href: href, class: 'media', 'aria-label': p.name }, [media(p)]),
      el('small', { class: 'muted', text: p.category }),
      el('h3', {}, [el('a', { href: href, text: p.name })]),
      el('div', { class: 'price', text: F.money(p.price) }),
      add
    ]);
  }

  function header() {
    var host = document.getElementById('site-header'); if (!host) return;
    var u = window.FeaneAuth;
    /* IMAGE PLACEHOLDER: logo. To use an image, replace the text inside .logo with
       <img src="images/logo.svg" alt="Feane" height="32"> */
    host.innerHTML =
      '<div class="topbar">Free shipping on orders over $' + F.freeShippingOver + '</div>' +
      '<header class="site"><div class="wrap bar">' +
      '<a class="logo" href="/">Feane</a>' +
      '<form class="search" action="/shop" role="search">' +
      '<input type="search" name="q" placeholder="Search products" aria-label="Search products">' +
      '<button class="btn sm" type="submit">Search</button></form>' +
      '<nav class="acct" id="acct" aria-label="Account"></nav></div>' +
      '<nav class="wrap cats" id="cats" aria-label="Categories"></nav></header>';
    var acct = document.getElementById('acct');
    if (u) {
      if (u.is_admin) acct.appendChild(el('a', { href: '/admin', text: 'Admin' }));
      acct.appendChild(el('span', { class: 'hello', text: 'Hi, ' + u.name.split(' ')[0] }));
      var logout = el('form', { method: 'post', action: '/logout' });
      logout.appendChild(el('input', { type: 'hidden', name: '_token',
        value: document.querySelector('meta[name="csrf-token"]').content }));
      logout.appendChild(el('button', { type: 'submit', class: 'link-btn', text: 'Log out' }));
      acct.appendChild(logout);
    } else {
      acct.appendChild(el('a', { href: '/login', text: 'Log in' }));
      acct.appendChild(el('a', { href: '/register', text: 'Register' }));
    }
    acct.appendChild(el('a', { href: '/checkout', class: 'cartlink' }, [
      document.createTextNode('Cart '), el('span', { class: 'count', id: 'cartCount', text: '0' })]));
    var cats = document.getElementById('cats'), cur = params().get('cat');
    cats.appendChild(el('a', { href: '/shop', text: 'All products' }));
    F.categories.forEach(function (c) {
      var a = el('a', { href: '/shop?category=' + encodeURIComponent(c), text: c });
      if (cur === c) a.setAttribute('aria-current', 'page');
      cats.appendChild(a);
    });
    var q = host.querySelector('input[type=search]'); if (q && params().get('q')) q.value = params().get('q');
    badge();
  }

  function footer() {
    var host = document.getElementById('site-footer'); if (!host) return;
    var cols = el('div', { class: 'wrap foot-grid' });
    var brand = el('div', {}, [el('a', { class: 'logo', href: '/', text: 'Feane' }),
      el('p', { text: 'Well-made things for everyday life, shipped to your door.' })]);
    var shop = el('div', {}, [el('h4', { text: 'Shop' })]);
    F.categories.forEach(function (c) { shop.appendChild(el('a', { href: '/shop?category=' + encodeURIComponent(c), text: c })); });
    var acc = el('div', {}, [el('h4', { text: 'Account' }),
      el('a', { href: '/login', text: 'Log in' }), el('a', { href: '/register', text: 'Register' }),
      el('a', { href: '/checkout', text: 'Your cart' })]);
    /* Replace the placeholder contact details below with your own. */
    var help = el('div', {}, [el('h4', { text: 'Contact' }),
      el('p', { text: '+01 1234567890' }), el('p', { text: 'demo@gmail.com' }), el('p', { text: 'Support: Mon to Fri, 9am to 6pm' })]);
    [brand, shop, acc, help].forEach(function (c) { cols.appendChild(c); });
    host.className = 'site-footer'; host.appendChild(cols);
    host.appendChild(el('p', { class: 'wrap copy', text: '\u00a9 ' + new Date().getFullYear() + ' Feane. All rights reserved.' }));
  }

  F.ui = { el: el, slug: slug, params: params, media: media, card: card, toast: toast, badge: badge };
  header(); footer();
})();
