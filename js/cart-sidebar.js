document.addEventListener("DOMContentLoaded", () => {
  initAddToCartListeners();
  initCartIconListener();
});

/* =========================
   ADD TO CART
   ========================= */
function initAddToCartListeners() {
  document.addEventListener("submit", function (e) {
    if (!e.target.classList.contains("add-to-cart-form")) return;

    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const btn = form.querySelector("button");
    const originalText = btn.innerHTML;

    btn.innerHTML = "Adding...";
    btn.disabled = true;

    fetch("add_to_cart.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        btn.disabled = false;

        if (data.success) {
          btn.innerHTML = "Added";
          setTimeout(() => (btn.innerHTML = originalText), 1500);

          openCartSidebar();

          // ❌ DO NOT TRUST add_to_cart count
          forceCartSync();
        } else {
          btn.innerHTML = originalText;
          alert("Failed to add product.");
        }
      })
      .catch((err) => {
        console.error(err);
        btn.disabled = false;
        btn.innerHTML = originalText;
      });
  });
}

/* =========================
   CART ICON
   ========================= */
function initCartIconListener() {
  const cartBtn = document.querySelector(".cart-button");

  if (cartBtn) {
    cartBtn.addEventListener("click", (e) => {
      e.preventDefault();
      openCartSidebar();
    });
  }
}

/* =========================
   SIDEBAR
   ========================= */
function openCartSidebar() {
  const sidebar = document.getElementById("cartSidebar");
  const overlay = document.getElementById("cartOverlay");

  if (!sidebar || !overlay) return;

  sidebar.classList.add("open");
  overlay.classList.add("open");

  updateSidebarContent();
}

function closeCartSidebar() {
  document.getElementById("cartSidebar")?.classList.remove("open");
  document.getElementById("cartOverlay")?.classList.remove("open");
}

/* =========================
   LOAD SIDEBAR DATA
   ========================= */
function updateSidebarContent() {
  const body = document.getElementById("cartSidebarBody");
  const totalElem = document.getElementById("cartSidebarTotal");

  body.style.opacity = "0.5";

  fetch("get_cart_sidebar.php")
    .then((res) => res.json())
    .then((data) => {
      body.innerHTML = data.html;
      if (totalElem) totalElem.textContent = "$" + data.total;

      safeUpdateCartCount(data.count);

      body.style.transition = "opacity 0.3s ease";
      body.style.opacity = "1";
    })
    .catch((err) => {
      console.error(err);
      body.style.opacity = "1";
    });
}

/* =========================
   UPDATE ITEM
   ========================= */
function updateCartItem(id, action) {
  const formData = new FormData();
  formData.append("id", id);
  formData.append("action", action);

  fetch("update_cart_ajax.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        updateSidebarContent();
      }
    })
    .catch(console.error);
}

/* =========================
   SAFE COUNT HANDLING
   ========================= */
function safeUpdateCartCount(count) {
  count = parseInt(count);

  // 🔒 HARD LIMIT
  if (isNaN(count) || count < 0) count = 0;
  if (count > 99) count = 99;

  const badge = document.getElementById("cartCount");
  if (badge) badge.textContent = count;
}

/* =========================
   FORCE TRUE SYNC
   ========================= */
function forceCartSync() {
  fetch("get_cart_sidebar.php")
    .then((res) => res.json())
    .then((data) => {
      safeUpdateCartCount(data.count);
    })
    .catch(console.error);
}
