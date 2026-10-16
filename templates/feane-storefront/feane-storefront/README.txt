Feane storefront
================
Open index.html in a browser (or serve the folder with any static server).

Add your images
---------------
Every place a photo belongs shows a dashed grey box that prints the file path and size to use.
  images/hero.jpg                  1200 x 900   home page hero
  images/promo.jpg                 1280 x 720   home page promo banner
  images/categories/<name>.jpg      600 x 450   category tiles (clothing, shoes, bags, home, tech, accessories)
  images/products/<product>.jpg    1000 x 1000  product photos, named after the product (organic-cotton-t-shirt.jpg)
  images/products/<product>-2.jpg  400 x 400    extra product angles (-2, -3, -4)
Product photos can also be set per product with the "Image path" field in admin.html.
In the code, search for "IMAGE PLACEHOLDER" to find each spot (index.html, product.html, cart.html,
admin.html, js/shell.js). Logo and favicon placeholders are in js/shell.js and the page <head>.

Accounts: the first person to register becomes the admin and sees the Admin link.
Data is stored in the browser (localStorage); move js/store.js to a real backend before going live.
