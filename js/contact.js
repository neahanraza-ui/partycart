// contact.js
document.addEventListener("DOMContentLoaded", () => {
  const contactForm = document.getElementById("contactForm");

  if (contactForm) {
    contactForm.addEventListener("submit", handleContactSubmit);
  }
});

function handleContactSubmit(e) {
  e.preventDefault();

  const form = e.target;
  const btn = form.querySelector('button[type="submit"]');
  const originalText = btn.innerHTML;

  // Show loading state
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
  btn.disabled = true;

  // Prepare form data for submission
  const formData = new FormData(form);

  // Send data via AJAX (using Fetch API)
  fetch("contact_form_handler.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      // Reset button
      btn.innerHTML = originalText;
      btn.disabled = false;

      if (data.success) {
        // Show success message
        const successContainer = document.getElementById(
          "contactSuccessMessage"
        );
        successContainer.innerHTML = `
        <div class="success-message">
          <i class="fas fa-check-circle"></i> 
          Thank you for your message! Our team will respond within 24 hours.
        </div>
      `;

        // Reset form
        form.reset();

        // Scroll to success message
        successContainer.scrollIntoView({
          behavior: "smooth",
          block: "nearest",
        });

        // Remove success message after 5 seconds
        setTimeout(() => {
          successContainer.innerHTML = "";
        }, 5000);
      } else {
        // Show error message
        const errorContainer = document.getElementById("contactErrorMessage");
        errorContainer.innerHTML = `
        <div class="error-message">
          <i class="fas fa-times-circle"></i> 
          There was an error sending your message. Please try again.
        </div>
      `;
      }
    })
    .catch((error) => {
      // Reset button
      btn.innerHTML = originalText;
      btn.disabled = false;

      // Handle fetch error
      const errorContainer = document.getElementById("contactErrorMessage");
      errorContainer.innerHTML = `
      <div class="error-message">
        <i class="fas fa-times-circle"></i> 
        Something went wrong, please try again later.
      </div>
    `;
    });
}
