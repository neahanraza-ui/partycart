// Home Page Functionality
document.addEventListener("DOMContentLoaded", () => {
  // Render Best Sellers
  renderBestSellers();

  // Render Featured Collections
  renderFeaturedCollections();

  // Newsletter Form
  const newsletterForm = document.getElementById("newsletterForm");
  if (newsletterForm) {
    newsletterForm.addEventListener("submit", handleNewsletterSubmit);
  }
});

function renderBestSellers() {
  const container = document.getElementById("bestSellers");
  if (!container) return;

  // Get first 8 products as best sellers
  const bestSellers = productsData.slice(0, 8);

  container.innerHTML = bestSellers
    .map(
      (product) => `
    <div class="product-card fade-in">
      <div class="product-image-wrapper" onclick="goToProductDetails(${
        product.id
      })">
        <img src="${product.image}" alt="${product.name}" class="product-image">
        <div class="product-overlay">
          <button class="quick-view-btn" onclick="event.stopPropagation(); goToProductDetails(${
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

  // Reinitialize scroll animations
  initScrollAnimations();
}

function renderFeaturedCollections() {
  const container = document.getElementById("featuredCollections");
  if (!container) return;

  container.innerHTML = collectionsData
    .map(
      (collection) => `
    <div class="collection-card fade-in" onclick="goToCollection(${collection.id})">
      <img src="${collection.image}" alt="${collection.name}" class="collection-image">
      <div class="collection-overlay">
        <h3 class="collection-title">${collection.name}</h3>
        <p class="collection-description">${collection.description}</p>
      </div>
    </div>
  `
    )
    .join("");

  // Reinitialize scroll animations
  initScrollAnimations();
}

function handleNewsletterSubmit(e) {
  e.preventDefault();

  const email = e.target.querySelector('input[type="email"]').value;

  // Show success message
  const form = e.target;
  const successMessage = document.createElement("div");
  successMessage.className = "success-message";
  successMessage.innerHTML =
    '<i class="fas fa-check-circle"></i> Thank you for subscribing!';

  form.parentElement.insertBefore(successMessage, form);
  form.reset();

  setTimeout(() => {
    successMessage.style.animation = "fadeOut 0.4s ease-out";
    setTimeout(() => successMessage.remove(), 400);
  }, 3000);
}

function goToProductDetails(productId) {
  window.location.href = `product-details.html?id=${productId}`;
}

function goToCollection(collectionId) {
  window.location.href = `collections.html?id=${collectionId}`;
}

// Add fadeOut animation
const fadeOutStyle = document.createElement("style");
fadeOutStyle.textContent = `
  @keyframes fadeOut {
    from {
      opacity: 1;
      transform: translateY(0);
    }
    to {
      opacity: 0;
      transform: translateY(-20px);
    }
  }
`;
document.head.appendChild(fadeOutStyle);
