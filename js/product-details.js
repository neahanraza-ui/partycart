// Product Details Page Functionality
let currentProduct = null;
let currentImageIndex = 0;
let quantity = 1;

document.addEventListener("DOMContentLoaded", () => {
  loadProductDetails();
});

function loadProductDetails() {
  // Get product ID from URL
  const urlParams = new URLSearchParams(window.location.search);
  const productId = parseInt(urlParams.get("id"));

  // Find product
  currentProduct = productsData.find((p) => p.id === productId);

  if (!currentProduct) {
    window.location.href = "products.html";
    return;
  }

  renderProductDetails();
  renderRelatedProducts();
}

function renderProductDetails() {
  const container = document.getElementById("productDetailsContainer");

  const stars = '<i class="fas fa-star"></i>'.repeat(currentProduct.rating);

  container.innerHTML = `
    <div class="product-gallery fade-in">
      <div class="main-image-container">
        <img src="${currentProduct.images[0]}" alt="${
    currentProduct.name
  }" class="main-product-image" id="mainImage">
      </div>
      <div class="thumbnail-gallery">
        ${currentProduct.images
          .map(
            (img, index) => `
          <div class="thumbnail ${
            index === 0 ? "active" : ""
          }" onclick="changeImage(${index})">
            <img src="${img}" alt="${currentProduct.name}">
          </div>
        `
          )
          .join("")}
      </div>
    </div>
    
    <div class="product-details-info slide-in-right">
      <div class="breadcrumb">
        <a href="index.html">Home</a> / 
        <a href="products.html">Products</a> / 
        <span>${currentProduct.category}</span>
      </div>
      
      <h1 class="product-title-details">${currentProduct.name}</h1>
      
      <div class="product-rating">
        <div class="stars">${stars}</div>
        <span class="reviews-count">(${currentProduct.reviews} reviews)</span>
      </div>
      
      <div class="product-price-details">$${currentProduct.price.toLocaleString()}</div>
      
      <p class="product-description">${currentProduct.description}</p>
      
      <div class="quantity-selector">
        <span class="quantity-label">Quantity:</span>
        <div class="quantity-controls">
          <button class="qty-btn" onclick="decreaseQuantity()">
            <i class="fas fa-minus"></i>
          </button>
          <input type="number" class="qty-input" id="quantityInput" value="1" min="1" readonly>
          <button class="qty-btn" onclick="increaseQuantity()">
            <i class="fas fa-plus"></i>
          </button>
        </div>
      </div>
      
      <div class="add-to-cart-section">
        <button class="btn-luxury add-to-cart-btn gold-glow" onclick="addToCart()">
          <i class="fas fa-shopping-bag"></i> Add to Cart
        </button>
        <button class="wishlist-btn gold-glow">
          <i class="far fa-heart"></i>
        </button>
      </div>
      
      <div class="product-meta">
        <div class="meta-item">
          <span class="meta-label">Category:</span>
          <span class="meta-value">${currentProduct.category}</span>
        </div>
        <div class="meta-item">
          <span class="meta-label">SKU:</span>
          <span class="meta-value">LUX-${String(currentProduct.id).padStart(
            5,
            "0"
          )}</span>
        </div>
        <div class="meta-item">
          <span class="meta-label">Availability:</span>
          <span class="meta-value" style="color: var(--gold-primary);">In Stock</span>
        </div>
      </div>
    </div>
  `;

  // Image zoom functionality
  const mainImage = document.getElementById("mainImage");
  mainImage.addEventListener("click", toggleImageZoom);
}

function changeImage(index) {
  currentImageIndex = index;
  const mainImage = document.getElementById("mainImage");
  mainImage.style.opacity = "0";
  mainImage.classList.remove("zoomed");

  setTimeout(() => {
    mainImage.src = currentProduct.images[index];
    mainImage.style.transition = "opacity 0.4s ease";
    mainImage.style.opacity = "1";
  }, 200);

  // Update active thumbnail
  document.querySelectorAll(".thumbnail").forEach((thumb, i) => {
    thumb.classList.toggle("active", i === index);
  });
}

function toggleImageZoom(e) {
  const img = e.target;
  img.classList.toggle("zoomed");

  if (img.classList.contains("zoomed")) {
    const rect = img.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    const xPercent = (x / rect.width) * 100;
    const yPercent = (y / rect.height) * 100;
    img.style.transformOrigin = `${xPercent}% ${yPercent}%`;
  } else {
    img.style.transformOrigin = "center";
  }
}

function increaseQuantity() {
  quantity++;
  document.getElementById("quantityInput").value = quantity;
}

function decreaseQuantity() {
  if (quantity > 1) {
    quantity--;
    document.getElementById("quantityInput").value = quantity;
  }
}

function addToCart() {
  cartManager.addItem(currentProduct, quantity);

  // Reset quantity
  quantity = 1;
  document.getElementById("quantityInput").value = 1;

  // Add animation to button
  const btn = event.target.closest("button");
  btn.style.transform = "scale(0.95)";
  setTimeout(() => {
    btn.style.transform = "scale(1)";
  }, 200);
}

function renderRelatedProducts() {
  const container = document.getElementById("relatedProducts");

  // Get products from same category, excluding current product
  const related = productsData
    .filter(
      (p) =>
        p.category === currentProduct.category && p.id !== currentProduct.id
    )
    .slice(0, 4);

  // If not enough from same category, add more products
  if (related.length < 4) {
    const additionalProducts = productsData
      .filter((p) => p.id !== currentProduct.id && !related.includes(p))
      .slice(0, 4 - related.length);
    related.push(...additionalProducts);
  }

  container.innerHTML = related
    .map(
      (product) => `
    <div class="product-card fade-in">
      <div class="product-image-wrapper" onclick="goToProduct(${product.id})">
        <img src="${product.image}" alt="${product.name}" class="product-image">
        <div class="product-overlay">
          <button class="quick-view-btn" onclick="event.stopPropagation(); goToProduct(${
            product.id
          })">
            Quick View
          </button>
        </div>
      </div>
      <div class="product-info">
        <div class="product-category">${product.category}</div>
        <h3 class="product-name">${product.name}</h3>
        <div class="product-price">$${product.price.toLocaleString()}</div>
      </div>
    </div>
  `
    )
    .join("");
}

function goToProduct(productId) {
  window.location.href = `product-details.html?id=${productId}`;
}

// Wishlist toggle
document.addEventListener("click", (e) => {
  if (e.target.closest(".wishlist-btn")) {
    const btn = e.target.closest(".wishlist-btn");
    const icon = btn.querySelector("i");

    if (icon.classList.contains("far")) {
      icon.classList.remove("far");
      icon.classList.add("fas");
      btn.style.background = "var(--gold-primary)";
      btn.style.color = "var(--bg-primary)";
    } else {
      icon.classList.remove("fas");
      icon.classList.add("far");
      btn.style.background = "transparent";
      btn.style.color = "var(--gold-primary)";
    }
  }
});
