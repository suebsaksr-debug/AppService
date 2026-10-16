(function () {
  'use strict';

  var cartKey = 'feane_storefront_cart';
  var productsNode = document.getElementById('storefront-products');
  var products = productsNode ? JSON.parse(productsNode.textContent) : [];
  var productsById = {};
  products.forEach(function (product) { productsById[String(product.id)] = product; });

  function readCart() {
    try {
      var cart = JSON.parse(localStorage.getItem(cartKey) || '{}');
      return cart && typeof cart === 'object' ? cart : {};
    } catch (error) {
      return {};
    }
  }

  function saveCart(cart) {
    localStorage.setItem(cartKey, JSON.stringify(cart));
    updateBadge(cart);
  }

  function updateBadge(cart) {
    var count = Object.keys(cart).reduce(function (total, id) {
      return total + (parseInt(cart[id], 10) || 0);
    }, 0);
    var badge = document.getElementById('cartCount');
    if (badge) badge.textContent = count;
  }

  function money(amount) {
    return '$' + Number(amount).toFixed(2);
  }

  document.querySelectorAll('[data-cart-add]').forEach(function (button) {
    button.addEventListener('click', function () {
      var id = String(button.getAttribute('data-cart-add'));
      var cart = readCart();
      cart[id] = Math.min(99, (parseInt(cart[id], 10) || 0) + 1);
      saveCart(cart);
      button.textContent = 'Added';
      window.setTimeout(function () { button.textContent = 'Add to cart'; }, 1000);
    });
  });

  var list = document.getElementById('checkout-items');
  var form = document.getElementById('checkout-form');
  if (list && form) {
    var cart = readCart();
    var totalNode = document.getElementById('checkout-total');
    var emptyNode = document.getElementById('cart-empty');

    function renderCart() {
      list.replaceChildren();
      var activeItems = Object.keys(cart).map(function (id) {
        var product = productsById[id];
        return product ? { product: product, quantity: parseInt(cart[id], 10) || 0 } : null;
      }).filter(function (item) { return item && item.quantity > 0; });
      var subtotal = activeItems.reduce(function (sum, item) {
        return sum + Number(item.product.price) * item.quantity;
      }, 0);
      var shipping = subtotal === 0 || subtotal >= 50 ? 0 : 5.95;
      emptyNode.hidden = activeItems.length > 0;
      form.querySelector('button[type=submit]').disabled = activeItems.length === 0;

      activeItems.forEach(function (item) {
        var row = document.createElement('div');
        row.className = 'line';
        var image = document.createElement('img');
        image.src = item.product.image || 'images/categories/clothing.jpg';
        image.alt = '';
        image.width = 64;
        image.height = 64;
        image.style.objectFit = 'cover';
        var name = document.createElement('strong');
        name.textContent = item.product.name;
        var qty = document.createElement('input');
        qty.type = 'number';
        qty.min = '1';
        qty.max = '99';
        qty.value = item.quantity;
        qty.setAttribute('aria-label', 'Quantity for ' + item.product.name);
        qty.addEventListener('change', function () {
          cart[String(item.product.id)] = Math.max(1, Math.min(99, parseInt(qty.value, 10) || 1));
          saveCart(cart);
          renderCart();
        });
        var lineTotal = document.createElement('span');
        lineTotal.textContent = money(Number(item.product.price) * item.quantity);
        var remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'link-btn';
        remove.textContent = 'Remove';
        remove.addEventListener('click', function () {
          delete cart[String(item.product.id)];
          saveCart(cart);
          renderCart();
        });
        row.append(image, name, qty, lineTotal, remove);
        list.appendChild(row);
      });

      totalNode.innerHTML = '<div class="sum"><span>Subtotal</span><strong>' + money(subtotal) +
        '</strong></div><div class="sum"><span>Shipping</span><strong>' + money(shipping) +
        '</strong></div><div class="sum total"><span>Total</span><strong>' + money(subtotal + shipping) +
        '</strong></div>';
      updateBadge(cart);
    }

    form.addEventListener('submit', function () {
      var fields = document.getElementById('order-items-fields');
      fields.replaceChildren();
      Object.keys(cart).forEach(function (id, index) {
        if (!productsById[id]) return;
        var productId = document.createElement('input');
        productId.type = 'hidden';
        productId.name = 'items[' + index + '][id]';
        productId.value = id;
        var quantity = document.createElement('input');
        quantity.type = 'hidden';
        quantity.name = 'items[' + index + '][quantity]';
        quantity.value = cart[id];
        fields.append(productId, quantity);
      });
    });

    if (new URLSearchParams(window.location.search).has('order')) {
      localStorage.removeItem(cartKey);
      cart = {};
    }
    renderCart();
  } else {
    updateBadge(readCart());
  }
})();
