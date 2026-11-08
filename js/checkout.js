let appliedDiscount = 0;
let appliedCouponCode = null;

function applyCoupon() {
    const code = document.getElementById("couponCode").value.trim();
    const email = document.getElementById("userEmail").value.trim();
    const messageEl = document.getElementById("couponMessage");
    
    if (!code) {
        messageEl.innerText = "Please enter a code.";
        messageEl.style.color = "#dc3545";
        return;
    }
    
    if (!email) {
        messageEl.innerText = "Please enter your shipping email first.";
        messageEl.style.color = "#dc3545";
        // Highlight the email field to guide the user
        document.getElementById("userEmail").focus();
        document.getElementById("userEmail").style.borderColor = "#dc3545";
        return;
    }
    document.getElementById("userEmail").style.borderColor = "";

    messageEl.innerText = "Validating...";
    messageEl.style.color = "var(--gold-primary)";

    fetch("apply_coupon.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ code: code, email: email })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            appliedDiscount = data.discount;
            appliedCouponCode = data.code;
            
            messageEl.innerText = `Success! ${data.discount}% discount applied.`;
            messageEl.style.color = "#4ade80";
            
            updateTotals();
        } else {
            appliedDiscount = 0;
            appliedCouponCode = null;
            messageEl.innerText = data.message;
            messageEl.style.color = "#dc3545";
            updateTotals();
        }
    })
    .catch(err => {
        console.error(err);
        messageEl.innerText = "Error validating coupon.";
        messageEl.style.color = "#dc3545";
    });
}

function updateTotals() {
    const subtotal = cartManager.getTotal();
    const discountAmount = (subtotal * appliedDiscount) / 100;
    const finalTotal = subtotal - discountAmount;

    // Update UI
    document.getElementById("subtotalDisplay").innerText = `$${subtotal.toFixed(2)}`;
    
    const discountRow = document.getElementById("discountRow");
    if (appliedDiscount > 0) {
        discountRow.style.display = "flex";
        document.getElementById("discountPercent").innerText = appliedDiscount;
        document.getElementById("discountDisplay").innerText = `-$${discountAmount.toFixed(2)}`;
    } else {
        discountRow.style.display = "none";
    }
    
    document.getElementById("orderTotalDisplay").innerText = `$${finalTotal.toFixed(2)}`;
}

function placeOrder() {
  const form = document.getElementById("checkoutForm");

  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  // Show loading state
  const btn = document.querySelector(".checkout-submit-btn");
  const originalText = btn.innerHTML;
  btn.innerHTML = '<span>Processing...</span><i class="fas fa-spinner fa-spin"></i>';
  btn.disabled = true;

  // Get user data from the form
  const userName = document.getElementById("userName").value;
  const userEmail = document.getElementById("userEmail").value;
  const userPhone = document.getElementById("userPhone").value;
  const userAddress = document.getElementById("userAddress").value;

  // Get cart data
  const cartData = cartManager.getCart();
  const subtotal = cartManager.getTotal();
  const discountAmount = (subtotal * appliedDiscount) / 100;
  const orderTotal = subtotal - discountAmount;

  // Prepare order data
  const orderData = {
    userName,
    userEmail,
    userPhone,
    userAddress,
    cartData,
    orderTotal,
    couponCode: appliedCouponCode
  };

  // Send data to the server using AJAX
  fetch("process_order.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(orderData),
  })
    .then((response) => {
      // Check if response is OK
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      // Try to parse as JSON
      return response.text().then(text => {
        try {
          return JSON.parse(text);
        } catch (e) {
          console.error("Server response is not valid JSON:", text);
          throw new Error("Server returned invalid response: " + text.substring(0, 100));
        }
      });
    })
    .then((data) => {
      if (data.success) {
        cartManager.clearCart();
        window.location.href = `thank_you.php?order_id=${data.orderId}`;
      } else {
        console.error("Order failed:", data);
        alert(data.message || "There was an error processing your order. Please try again.");
        btn.innerHTML = originalText;
        btn.disabled = false;
      }
    })
    .catch((error) => {
      console.error("Checkout error:", error);
      alert("Error: " + error.message);
      btn.innerHTML = originalText;
      btn.disabled = false;
    });
}
