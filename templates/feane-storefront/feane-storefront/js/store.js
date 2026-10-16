/* Feane storefront: demo data layer on localStorage (accounts, products, cart, orders).
   To go live, replace the methods on window.Feane with fetch() calls to your backend;
   the pages only talk to this object. */
(function () {
  'use strict';
  var K = { users: 'feane_users', session: 'feane_session', products: 'feane_products_v2',
            resets: 'feane_resets', cart: 'feane_cart', orders: 'feane_orders' };
  var FREE_SHIPPING_OVER = 50, SHIPPING_FEE = 5.95;

  function read(k, d) { try { var v = JSON.parse(localStorage.getItem(k)); return v == null ? d : v; } catch (e) { return d; } }
  function write(k, v) { localStorage.setItem(k, JSON.stringify(v)); }
  function hex(buf) { return Array.prototype.map.call(new Uint8Array(buf), function (x) { return ('0' + x.toString(16)).slice(-2); }).join(''); }
  function rand(n) { var a = new Uint8Array(n); crypto.getRandomValues(a); return hex(a); }
  function cents(n) { return Math.round(n * 100) / 100; }
  function hash(pw, salt) {
    if (window.crypto && crypto.subtle) return crypto.subtle.digest('SHA-256', new TextEncoder().encode(salt + pw)).then(hex);
    var h = 5381, s = salt + pw; // weak fallback for non-secure contexts
    for (var i = 0; i < s.length; i++) h = ((h << 5) + h + s.charCodeAt(i)) | 0;
    return Promise.resolve('weak' + h);
  }
  function checkPassword(pw) { if ((pw || '').length < 8) throw new Error('Use a password of at least 8 characters.'); }
  function safeImg(s) {
    s = (s || '').trim();
    if (!s) return '';
    if (!/^(https:\/\/|images\/|\.\/|\/)[^\s"'<>]*$/i.test(s)) throw new Error('Use an image path like images/products/t-shirt.jpg or an https:// link.');
    return s;
  }

  var F = window.Feane = {
    categories: ['Clothing', 'Shoes', 'Bags', 'Home', 'Tech', 'Accessories'],
    money: function (n) { return '$' + Number(n).toFixed(2); },
    shipping: function (subtotal) { return subtotal >= FREE_SHIPPING_OVER || subtotal === 0 ? 0 : SHIPPING_FEE; },
    freeShippingOver: FREE_SHIPPING_OVER,

    /* ---------- accounts ---------- */
    current: function () {
      var id = read(K.session, null);
      if (!id) return null;
      var u = read(K.users, []).filter(function (x) { return x.id === id; })[0];
      return u ? { id: u.id, name: u.name, email: u.email, admin: !!u.admin } : null;
    },
    register: async function (name, email, pw) {
      name = (name || '').trim(); email = (email || '').trim().toLowerCase();
      if (name.length < 2) throw new Error('Enter your name.');
      if (!/^\S+@\S+\.\S+$/.test(email)) throw new Error('Enter a valid email address.');
      checkPassword(pw);
      var users = read(K.users, []);
      if (users.some(function (u) { return u.email === email; })) throw new Error('An account with this email already exists. Log in instead.');
      var salt = rand(8);
      var u = { id: rand(6), name: name, email: email, salt: salt, hash: await hash(pw, salt), admin: users.length === 0 };
      users.push(u); write(K.users, users); write(K.session, u.id);
      return F.current();
    },
    login: async function (email, pw) {
      email = (email || '').trim().toLowerCase();
      var u = read(K.users, []).filter(function (x) { return x.email === email; })[0];
      if (!u || (await hash(pw || '', u.salt)) !== u.hash) throw new Error('Email or password is incorrect.');
      write(K.session, u.id);
      return F.current();
    },
    logout: function () { localStorage.removeItem(K.session); },
    requestReset: function (email) { // a real site would email the link; the demo returns the token
      email = (email || '').trim().toLowerCase();
      if (!/^\S+@\S+\.\S+$/.test(email)) throw new Error('Enter a valid email address.');
      var u = read(K.users, []).filter(function (x) { return x.email === email; })[0];
      if (!u) return null;
      var resets = read(K.resets, {}), token = rand(16);
      resets[token] = { id: u.id, exp: Date.now() + 15 * 60 * 1000 };
      write(K.resets, resets);
      return token;
    },
    resetTokenValid: function (token) { var r = read(K.resets, {})[token]; return !!r && r.exp > Date.now(); },
    finishReset: async function (token, pw) {
      checkPassword(pw);
      var resets = read(K.resets, {}), r = resets[token];
      if (!r || r.exp < Date.now()) throw new Error('This reset link has expired. Request a new one.');
      var users = read(K.users, []), salt = rand(8);
      for (var i = 0; i < users.length; i++) {
        if (users[i].id === r.id) { users[i].salt = salt; users[i].hash = await hash(pw, salt); }
      }
      write(K.users, users); delete resets[token]; write(K.resets, resets);
    },

    /* ---------- products (create / read / update / delete) ---------- */
    products: {
      list: function () {
        var p = read(K.products, null);
        if (!p) {
          var s = [
            ['Organic Cotton T-Shirt', 24, 'Clothing', 'Soft, midweight organic cotton with a relaxed fit.'],
            ['Classic Denim Jacket', 79, 'Clothing', 'Washed denim with a timeless cut and sturdy metal buttons.'],
            ['Canvas Low-Top Sneakers', 59, 'Shoes', 'Lightweight everyday sneakers with a cushioned insole.'],
            ['Everyday Canvas Tote', 32, 'Bags', 'Roomy tote with an inner pocket and reinforced handles.'],
            ['Ceramic Pour-Over Set', 38, 'Home', 'Hand-glazed dripper and carafe for a slow morning coffee.'],
            ['Linen Throw Blanket', 54, 'Home', 'Washed linen that gets softer with every wash.'],
            ['Wireless Earbuds', 69, 'Tech', 'Bluetooth earbuds with 24 hours of battery in the case.'],
            ['Minimalist Wrist Watch', 95, 'Accessories', 'Stainless steel case, sapphire-coated glass and a leather strap.']
          ];
          p = s.map(function (x) { return { id: rand(4), name: x[0], price: x[1], category: x[2], description: x[3], image: '' }; });
          write(K.products, p);
        }
        return p;
      },
      get: function (id) { return F.products.list().filter(function (x) { return x.id === id; })[0] || null; },
      _clean: function (d) {
        var name = (d.name || '').trim(), price = parseFloat(d.price);
        if (!name) throw new Error('Enter a product name.');
        if (isNaN(price) || price < 0) throw new Error('Enter a price of 0 or more.');
        return { name: name, price: cents(price), category: d.category || 'Other',
                 description: (d.description || '').trim(), image: safeImg(d.image) };
      },
      add: function (d) {
        var p = F.products.list(), item = F.products._clean(d); item.id = rand(4);
        p.push(item); write(K.products, p); return item;
      },
      update: function (id, d) {
        var p = F.products.list(), item = F.products._clean(d), found = false;
        p = p.map(function (x) { if (x.id === id) { found = true; item.id = id; return item; } return x; });
        if (!found) throw new Error('That product no longer exists.');
        write(K.products, p); return item;
      },
      remove: function (id) { write(K.products, F.products.list().filter(function (x) { return x.id !== id; })); }
    },

    /* ---------- cart ---------- */
    cart: {
      _raw: function () { return read(K.cart, []); },
      items: function () {
        return F.cart._raw().map(function (l) { return { product: F.products.get(l.id), qty: l.qty }; })
          .filter(function (l) { return l.product; });
      },
      add: function (id, qty) {
        var c = F.cart._raw(), line = c.filter(function (l) { return l.id === id; })[0];
        qty = Math.max(1, parseInt(qty, 10) || 1);
        if (line) line.qty = Math.min(99, line.qty + qty); else c.push({ id: id, qty: Math.min(99, qty) });
        write(K.cart, c);
      },
      setQty: function (id, qty) {
        qty = parseInt(qty, 10);
        if (!qty || qty < 1) return F.cart.remove(id);
        write(K.cart, F.cart._raw().map(function (l) { if (l.id === id) l.qty = Math.min(99, qty); return l; }));
      },
      remove: function (id) { write(K.cart, F.cart._raw().filter(function (l) { return l.id !== id; })); },
      clear: function () { localStorage.removeItem(K.cart); },
      count: function () { return F.cart.items().reduce(function (n, l) { return n + l.qty; }, 0); },
      subtotal: function () { return cents(F.cart.items().reduce(function (n, l) { return n + l.qty * l.product.price; }, 0)); }
    },

    /* ---------- orders ---------- */
    orders: {
      place: function (info) {
        var u = F.current();
        if (!u) throw new Error('Log in to place your order.');
        var name = (info.name || '').trim(), phone = (info.phone || '').trim(), address = (info.address || '').trim();
        if (!name || !phone || !address) throw new Error('Fill in your name, phone number and shipping address.');
        var items = F.cart.items();
        if (!items.length) throw new Error('Your cart is empty.');
        var sub = F.cart.subtotal(), ship = F.shipping(sub);
        var o = {
          id: 'FE-' + rand(3).toUpperCase(), userId: u.id, email: u.email,
          items: items.map(function (l) { return { id: l.product.id, name: l.product.name, price: l.product.price, qty: l.qty }; }),
          subtotal: sub, shipping: ship, total: cents(sub + ship),
          info: { name: name, phone: phone, address: address }, date: new Date().toISOString(), status: 'Received'
        };
        var all = read(K.orders, []); all.unshift(o); write(K.orders, all);
        F.cart.clear();
        return o;
      },
      list: function () {
        var u = F.current();
        if (!u) return [];
        var all = read(K.orders, []);
        return u.admin ? all : all.filter(function (o) { return o.userId === u.id; });
      }
    }
  };
})();
