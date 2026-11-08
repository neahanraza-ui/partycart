/**
 * Admin Panel Mobile Menu Toggle
 * Handles sidebar toggle for responsive admin panel
 */

document.addEventListener('DOMContentLoaded', function() {
    // Create mobile toggle button if it doesn't exist
    let adminToggle = document.querySelector('.admin-mobile-toggle');
    if (!adminToggle && document.querySelector('.sidebar')) {
        adminToggle = document.createElement('button');
        adminToggle.className = 'admin-mobile-toggle';
        adminToggle.innerHTML = '<i class="fas fa-bars"></i>';
        document.body.appendChild(adminToggle);
    }
    
    // Create overlay if it doesn't exist
    let overlay = document.querySelector('.admin-sidebar-overlay');
    if (!overlay && document.querySelector('.sidebar')) {
        overlay = document.createElement('div');
        overlay.className = 'admin-sidebar-overlay';
        document.body.appendChild(overlay);
    }
    
    const sidebar = document.querySelector('.sidebar');
    
    // Toggle sidebar function
    function toggleSidebar() {
        if (sidebar) {
            sidebar.classList.toggle('active');
            if (overlay) {
                overlay.classList.toggle('active');
            }
            if (adminToggle) {
                adminToggle.innerHTML = sidebar.classList.contains('active') 
                    ? '<i class="fas fa-times"></i>' 
                    : '<i class="fas fa-bars"></i>';
            }
        }
    }
    
    // Close sidebar function
    function closeSidebar() {
        if (sidebar) {
            sidebar.classList.remove('active');
            if (overlay) {
                overlay.classList.remove('active');
            }
            if (adminToggle) {
                adminToggle.innerHTML = '<i class="fas fa-bars"></i>';
            }
        }
    }
    
    // Toggle on button click
    if (adminToggle) {
        adminToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleSidebar();
        });
    }
    
    // Close on overlay click
    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }
    
    // Close on sidebar link click (mobile)
    if (sidebar) {
        const sidebarLinks = sidebar.querySelectorAll('a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    closeSidebar();
                }
            });
        });
    }
    
    // Close on window resize if viewport becomes larger
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth > 768) {
                closeSidebar();
            }
        }, 250);
    });
    
    // Handle escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar && sidebar.classList.contains('active')) {
            closeSidebar();
        }
    });
});
