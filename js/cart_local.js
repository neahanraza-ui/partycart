const CART_KEY = "luxora_cart";

function getLocalCart() {
  try {
    const c = JSON.parse(localStorage.getItem(CART_KEY));
    return Array.isArray(c) ? c : [];
  } catch {
    return [];
  }
}

function saveLocalCart(cart) {
  localStorage.setItem(CART_KEY, JSON.stringify(cart));
}

function addToLocalCart(product) {
  const cart = getLocalCart();

  const id = String(product.id);
  const existing = cart.find((i) => String(i.id) === id);

  if (existing) {
    existing.quantity = Number(existing.quantity || 1) + 1;
  } else {
    cart.push({
      id: product.id,
      name: product.name,
      price: Number(product.price),
      image: product.image || product.image_url || "default-image.jpg",
      quantity: 1,
    });
  }

  saveLocalCart(cart);
  return cart.length;
}

function removeFromLocalCart(index) {
  const cart = getLocalCart();
  cart.splice(index, 1);
  saveLocalCart(cart);
}

function increaseLocalQty(index) {
  const cart = getLocalCart();
  if (!cart[index]) return;
  cart[index].quantity = Number(cart[index].quantity || 1) + 1;
  saveLocalCart(cart);
}

function clearLocalCart() {
  localStorage.removeItem(CART_KEY);
}
