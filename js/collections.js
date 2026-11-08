// Collections Page Functionality
document.addEventListener("DOMContentLoaded", () => {
  renderCollections();
});

function renderCollections() {
  const container = document.getElementById("collectionsGrid");

  container.innerHTML = collectionsData
    .map(
      (collection, index) => `
    <div class="collection-card-large fade-in" style="animation-delay: ${
      index * 0.1
    }s;">
      <img src="${collection.image}" alt="${
        collection.name
      }" class="collection-image">
      <div class="collection-overlay">
        <h3 class="collection-title">${collection.name}</h3>
        <p class="collection-description" style="margin-bottom: 25px;">${
          collection.description
        }</p>
        <button class="btn-outline gold-glow" onclick="viewCollection(${
          collection.id
        })">
          Explore Collection
        </button>
      </div>
    </div>
  `
    )
    .join("");
}

function viewCollection(collectionId) {
  // For this demo, redirect to products page
  // In a real app, you would filter products by collection
  window.location.href = "products.html";
}
