(function () {
  const POSSIBLE_KEYS = [
    "luxora_cart",
    "cart",
    "cartItems",
    "shopping_cart",
    "luxoraCart",
    "cart_data",
    "myCart",
  ];

  // Helper function to safely parse JSON
  function tryParse(v) {
    try {
      return JSON.parse(v);
    } catch {
      return null;
    }
  }

  // Function to detect the cart from either localStorage or sessionStorage
  function detectCart() {
    // Try localStorage first
    for (const key of POSSIBLE_KEYS) {
      const v = localStorage.getItem(key);
      if (!v) continue;
      const parsed = tryParse(v);
      if (Array.isArray(parsed)) {
        return { storage: "localStorage", key, cart: parsed };
      }
      // Sometimes it's stored as an object with 'items' property
      if (parsed && Array.isArray(parsed.items)) {
        return { storage: "localStorage", key, cart: parsed.items };
      }
    }

    // Then, try sessionStorage
    for (const key of POSSIBLE_KEYS) {
      const v = sessionStorage.getItem(key);
      if (!v) continue;
      const parsed = tryParse(v);
      if (Array.isArray(parsed)) {
        return { storage: "sessionStorage", key, cart: parsed };
      }
      // Again, check for 'items' property if stored as object
      if (parsed && Array.isArray(parsed.items)) {
        return { storage: "sessionStorage", key, cart: parsed.items };
      }
    }

    return { storage: null, key: null, cart: [] }; // No cart found
  }

  // Function to sync the cart with the server
  window.syncCartToSession = async function () {
    const found = detectCart();
    console.log("Cart detected:", found);

    // If no cart was found, log a warning and exit
    if (!found.cart || found.cart.length === 0) {
      console.warn(
        "No cart found in browser storage. Your cart.js may not be saving to storage.",
      );
      return false;
    }

    try {
      // Send the cart data to the server via POST request
      const res = await fetch("sync_cart.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ cart: found.cart }),
      });

      const data = await res.json();
      console.log("sync_cart.php response:", data);

      // Return true if sync was successful
      return data.success === true;
    } catch (e) {
      console.error("syncCartToSession failed:", e);
      return false;
    }
  };
})();
