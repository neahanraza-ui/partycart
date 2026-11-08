// Cart Page Functionality
document.addEventListener("DOMContentLoaded", () => {
  renderCart();
});

function renderCart() {
  const container = document.getElementById("cartContent");
  const cart = cartManager.getCart();

  if (cart.length === 0) {
    container.innerHTML = `
      <div class="empty-cart fade-in">
        <i class="fas fa-shopping-bag empty-cart-icon"></i>
        <h2>Your cart is empty</h2>
        <p>Discover our exclusive collection of luxury products</p>
        <button class="btn-luxury gold-glow" onclick="window.location.href='products.html'">
          Continue Shopping
        </button>
      </div>
    `;
    return;
  }

  const subtotal = cart.reduce(
    (sum, item) => sum + item.price * item.quantity,
    0
  );
  const shipping = subtotal > 500 ? 0 : 50;
  const tax = subtotal * 0.08;
  const total = subtotal + shipping + tax;

  container.innerHTML = `
    <div class="cart-container fade-in">
      <div class="cart-items">
        <h2 style="font-family: var(--font-luxury); font-size: 28px; color: var(--gold-primary); margin-bottom: 30px;">
          Cart Items (${cart.length})
        </h2>
        ${cart
          .map(
            (item) => `
            <div class="cart-item" id="cart-item-${item.id}">
              <img src="${item.image_url_1}" alt="${item.name}" class="cart-item-image">
              <div class="cart-item-details">
                <h3>${item.name}</h3>
                <div class="cart-item-price">$${item.price}</div>
                <input type="number" value="${item.quantity}" min="1" onchange="updateItemQuantity(${item.id}, this.value)">
                <button onclick="removeItem(${item.id})">Remove</button>
              </div>
            </div>
          `
          )
          .join("")}
      </div>
      
      <div class="cart-summary">
        <h2 class="summary-title">Order Summary</h2>
        <div class="summary-row">
          <span class="summary-label">Subtotal</span>
          <span class="summary-value">$${subtotal.toFixed(2)}</span>
        </div>
        <div class="summary-row">
          <span class="summary-label">Shipping</span>
          <span class="summary-value">${
            shipping === 0 ? "FREE" : `$${shipping}`
          }</span>
        </div>
        <div class="summary-row">
          <span class="summary-label">Tax (8%)</span>
          <span class="summary-value">$${tax.toFixed(2)}</span>
        </div>
        <div class="summary-total">
          <span class="summary-label">Total</span>
          <span class="summary-value">$${total.toFixed(2)}</span>
        </div>
        <button class="btn-luxury checkout-btn" onclick="proceedToCheckout()">Proceed to Checkout</button>
      </div>
    </div>
  `;
}
function updateItemQuantity(productId, newQuantity) {
  if (newQuantity < 1) return;

  cartManager.updateQuantity(productId, newQuantity);
  renderCart();
}

function removeItem(productId) {
  const item = document.getElementById(`cart-item-${productId}`);

  // Animate removal
  item.style.animation = "slideOutLeft 0.4s ease-out";

  setTimeout(() => {
    cartManager.removeItem(productId);
    renderCart();
  }, 400);
}

function proceedToCheckout() {
  window.location.href = "checkout.html";
}

// Add slideOutLeft animation
const style = document.createElement("style");
style.textContent = `
  @keyframes slideOutLeft {
    from {
      opacity: 1;
      transform: translateX(0);
    }
    to {
      opacity: 0;
      transform: translateX(-100px);
    }
  }
`;
document.head.appendChild(style);
