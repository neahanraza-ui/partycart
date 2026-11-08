class CartManager {
  constructor() {
    this.cart = this.loadCart();  // Load cart from localStorage
    this.updateCartCount();
  }

  // Load cart from localStorage
  loadCart() {
    const savedCart = localStorage.getItem("luxora_cart");
    return savedCart ? JSON.parse(savedCart) : [];
  }

  // Save cart to localStorage
  saveCart() {
    localStorage.setItem("luxora_cart", JSON.stringify(this.cart));
    this.updateCartCount();  // Update cart count after saving
    this.syncWithServer();   // Sync with server if logged in
  }

  // Synchronize cart with server
  async syncWithServer() {
    // We check for a session flag or user_id if exposed, 
    // but typically we attempt to sync and the server handles auth checks
    try {
      const response = await fetch("sync_cart.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ cart: this.cart })
      });
      const data = await response.json();
      if (data.success) {
        console.log("Cart synced with server.");
      }
    } catch (err) {
      console.error("Cart sync failed:", err);
    }
  }

  // Load cart from server (call this on login or initial load)
  async loadFromServer() {
    try {
      const response = await fetch("get_cart.php");
      const data = await response.json();
      if (data.success && data.cart) {
        let changed = false;
        
        // Merge logic to avoid resetting guest cart
        data.cart.forEach(serverItem => {
          const existingItem = this.cart.find(item => item.id === serverItem.id);
          if (existingItem) {
            // Use server quantity if it represents a more recent state, 
            // or just ensure we have at least what's on the server
            if (existingItem.quantity < serverItem.quantity) {
              existingItem.quantity = serverItem.quantity;
              changed = true;
            }
          } else {
            this.cart.push(serverItem);
            changed = true;
          }
        });

        if (changed) {
          this.saveCart(); // Saves to local and syncs back to DB
          if (typeof this.displayCart === 'function') this.displayCart();
        }
      }
    } catch (err) {
      console.error("Load cart from server failed:", err);
    }
  }

  // Add product to the cart
  addItem(product, quantity = 1) {
    const existingItem = this.cart.find(item => item.id === product.id);
    if (existingItem) {
      existingItem.quantity += quantity;
    } else {
      this.cart.push({ ...product, quantity });
    }
    this.saveCart();
    this.showNotification("Item added to cart!");
  }

  // Remove item from the cart
  removeItem(productId) {
    this.cart = this.cart.filter(item => item.id !== productId);
    this.saveCart();
    this.updateCartCount();  // Update cart count after removal
  }

  // Update cart count in the navbar
  updateCartCount() {
    const cartCountElements = document.querySelectorAll("#cartCount");
    const count = this.cart.reduce((total, item) => total + item.quantity, 0);
    cartCountElements.forEach(element => {
      element.textContent = count;
      element.style.display = count > 0 ? "inline-block" : "none";
    });
  }

  // Show notification when an item is added to the cart
  showNotification(message) {
    const notification = document.createElement("div");
    notification.className = "cart-notification";
    notification.innerHTML = `
      <span>${message}</span>
    `;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
  }

  updateQuantity(productId, quantity) {
    const item = this.cart.find((item) => item.id === productId);
    if (item) {
      item.quantity = Math.max(1, quantity);
      this.saveCart();
      this.displayCart(); // Re-render cart content after update
    }
  }

  getCart() {
    return this.cart;
  }

  getTotal() {
    return this.cart.reduce(
      (total, item) => total + item.price * item.quantity,
      0
    );
  }

  getItemCount() {
    return this.cart.reduce((count, item) => count + item.quantity, 0);
  }

  clearCart() {
    this.cart = [];
    this.saveCart();
    // Only update display if cart elements exist on the page
    if (document.getElementById("cartContent")) {
      this.displayCart();
    }
  }

  updateCartCount() {
    const cartCountElements = document.querySelectorAll("#cartCount");
    const count = this.getItemCount();
    cartCountElements.forEach((element) => {
      element.textContent = count;
      element.style.display = count > 0 ? "flex" : "none";
    });
  }

  displayCart() {
    const cartContent = document.getElementById("cartContent");
    // Exit early if cart display element doesn't exist on this page
    if (!cartContent) return;
    
    const cart = this.getCart();

    if (cart.length === 0) {
      cartContent.innerHTML = "<p>Your cart is empty</p>";
    } else {
      cartContent.innerHTML = "";
      cart.forEach((item) => {
        // Create HTML content for each item
        let itemHTML = `
          <div class="cart-item">
            <img src="${item.image || item.image_url_1 || 'default-image.jpg'}" alt="${item.name}">
            <div class="cart-item-details">
              <h3>${item.name}</h3>
              <p>$${item.price}</p>
              <p>Quantity: 
                <input type="number" value="${item.quantity}" min="1" onchange="updateItemQuantity(${item.id}, this.value)">
              </p>
              <button onclick="removeItem(${item.id})">Remove</button>
            </div>
          </div>
        `;
        cartContent.innerHTML += itemHTML;
      });
      this.displayTotal();
    }
  }

  displayTotal() {
    const total = this.getTotal();
    const cartTotal = document.getElementById("cartTotal");
    // Exit early if total display element doesn't exist on this page
    if (!cartTotal) return;
    cartTotal.innerHTML = `<p>Total: $${total.toFixed(2)}</p>`;
  }

  showNotification(message) {
    // Create notification element
    const notification = document.createElement("div");
    notification.className = "cart-notification";
    notification.innerHTML = `
      <i class="fas fa-check-circle"></i>
      <span>${message}</span>
    `;

    // Add styles
    notification.style.cssText = `
      position: fixed;
      top: 100px;
      right: 50px;
      background: linear-gradient(135deg, var(--gold-primary), var(--gold-secondary));
      color: var(--bg-primary);
      padding: 18px 30px;
      border-radius: 0;
      font-size: 14px;
      font-weight: 600;
      letter-spacing: 1px;
      z-index: 10000;
      animation: slideInRight 0.4s ease-out;
      box-shadow: 0 10px 30px rgba(184, 150, 46, 0.4);
      display: flex;
      align-items: center;
      gap: 12px;
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
      notification.style.animation = "slideOutRight 0.4s ease-out";
      setTimeout(() => notification.remove(), 400);
    }, 3000);
  }
}

// Add notification animations to document
const style = document.createElement("style");
style.textContent = `
  @keyframes slideInRight {
    from {
      opacity: 0;
      transform: translateX(100px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }
  
  @keyframes slideOutRight {
    from {
      opacity: 1;
      transform: translateX(0);
    }
    to {
      opacity: 0;
      transform: translateX(100px);
    }
  }
`;
document.head.appendChild(style);

// Initialize cart manager
const cartManager = new CartManager();

// Functions to remove and update cart items
function removeItem(productId) {
  cartManager.removeItem(productId);
}

function updateItemQuantity(productId, quantity) {
  cartManager.updateQuantity(productId, quantity);
}

document.getElementById("checkoutBtn").addEventListener("click", () => {
  const cart = cartManager.getCart();

  if (cart.length > 0) {
    sessionStorage.setItem("cart_data", JSON.stringify(cart));
    window.location.href = "checkout.html"; // Or redirect to your checkout page
  } else {
    alert(
      "Your cart is empty! Please add items to your cart before checking out."
    );
  }
});
