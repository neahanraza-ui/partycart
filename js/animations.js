// Scroll Animations
function initScrollAnimations() {
  const observerOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -50px 0px",
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("visible");
      }
    });
  }, observerOptions);

  // Observe all elements with animation classes
  const animatedElements = document.querySelectorAll(
    ".fade-in, .slide-in-left, .slide-in-right"
  );
  animatedElements.forEach((el) => observer.observe(el));
}

// Initialize animations when DOM is loaded
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initScrollAnimations);
} else {
  initScrollAnimations();
}

// Add smooth scroll behavior
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute("href"));
    if (target) {
      target.scrollIntoView({
        behavior: "smooth",
        block: "start",
      });
    }
  });
});

// Navbar scroll effect
let lastScroll = 0;
const navbar = document.querySelector(".navbar");

if (navbar) {
  window.addEventListener("scroll", () => {
    const currentScroll = window.pageYOffset;

    if (currentScroll > 100) {
      navbar.style.background = "rgba(10, 10, 10, 0.98)";
      navbar.style.boxShadow = "0 5px 20px rgba(0, 0, 0, 0.5)";
    } else {
      navbar.style.background = "rgba(10, 10, 10, 0.95)";
      navbar.style.boxShadow = "none";
    }

    lastScroll = currentScroll;
  });
}

// Product card hover effects enhancement
function initProductHoverEffect() {
  const productCards = document.querySelectorAll(".product-card");
  productCards.forEach((card) => {
    card.addEventListener("mouseenter", function () {
      this.style.transition = "all 0.4s cubic-bezier(0.4, 0, 0.2, 1)";
    });
  });
}

// Form input animations
function initFormAnimations() {
  const inputs = document.querySelectorAll(
    ".form-input, .newsletter-input, .search-input"
  );

  inputs.forEach((input) => {
    input.addEventListener("focus", function () {
      this.style.transform = "translateY(-2px)";
    });

    input.addEventListener("blur", function () {
      this.style.transform = "translateY(0)";
    });
  });
}

// Image lazy loading with fade effect
function initLazyLoading() {
  const images = document.querySelectorAll("img[data-src]");

  const imageObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const img = entry.target;
        img.src = img.dataset.src;
        img.style.opacity = "0";
        img.style.transition = "opacity 0.6s ease";

        img.onload = () => {
          img.style.opacity = "1";
          img.removeAttribute("data-src");
        };

        imageObserver.unobserve(img);
      }
    });
  });

  images.forEach((img) => imageObserver.observe(img));
}

// Profile Dropdown Toggle
function initProfileDropdown() {
  const profileDropdown = document.getElementById("profileDropdown");

  if (profileDropdown) {
    // Close dropdown when clicking outside
    document.addEventListener("click", (e) => {
      if (!profileDropdown.contains(e.target)) {
        profileDropdown.classList.remove("active");
      }
    });

    // Close dropdown on Escape key
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") {
        profileDropdown.classList.remove("active");
      }
    });
  }
}

// Update DOMContentLoaded listener to cover EVERYTHING
document.addEventListener("DOMContentLoaded", () => {
  initScrollAnimations();
  initFormAnimations();
  initLazyLoading();
  initProfileDropdown();
  initProductHoverEffect();
});
