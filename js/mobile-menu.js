/**
 * Mobile Menu Toggle Functionality
 * Handles hamburger menu for responsive navigation
 */

document.addEventListener('DOMContentLoaded', function() {
  const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
  const navbarLinks = document.querySelector('.navbar-links');
  const mobileMenuOverlay = document.querySelector('.mobile-menu-overlay');
  const body = document.body;
  
  // Create overlay if it doesn't exist
  if (!mobileMenuOverlay) {
    const overlay = document.createElement('div');
    overlay.className = 'mobile-menu-overlay';
    document.body.appendChild(overlay);
  }
  
  // Toggle menu function
  function toggleMenu() {
    if (mobileMenuToggle && navbarLinks) {
      mobileMenuToggle.classList.toggle('active');
      navbarLinks.classList.toggle('active');
      
      const overlay = document.querySelector('.mobile-menu-overlay');
      if (overlay) {
        overlay.classList.toggle('active');
      }
      
      // Prevent body scroll when menu is open
      if (navbarLinks.classList.contains('active')) {
        body.style.overflow = 'hidden';
      } else {
        body.style.overflow = '';
      }
    }
  }
  
  // Close menu function
  function closeMenu() {
    if (mobileMenuToggle && navbarLinks) {
      mobileMenuToggle.classList.remove('active');
      navbarLinks.classList.remove('active');
      
      const overlay = document.querySelector('.mobile-menu-overlay');
      if (overlay) {
        overlay.classList.remove('active');
      }
      
      body.style.overflow = '';
    }
  }
  
  // Toggle menu on button click
  if (mobileMenuToggle) {
    mobileMenuToggle.addEventListener('click', function(e) {
      e.stopPropagation();
      toggleMenu();
    });
  }
  
  // Close menu when clicking overlay
  document.addEventListener('click', function(e) {
    const overlay = document.querySelector('.mobile-menu-overlay');
    if (overlay && e.target === overlay) {
      closeMenu();
    }
  });
  
  // Close menu when clicking a link
  if (navbarLinks) {
    const links = navbarLinks.querySelectorAll('a');
    links.forEach(link => {
      link.addEventListener('click', function() {
        closeMenu();
      });
    });
  }
  
  // Close menu on window resize if viewport becomes larger
  let resizeTimer;
  window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
      if (window.innerWidth > 768) {
        closeMenu();
      }
    }, 250);
  });
  
  // Handle escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && navbarLinks && navbarLinks.classList.contains('active')) {
      closeMenu();
    }
  });
});
