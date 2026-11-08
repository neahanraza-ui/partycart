<?php
include 'includes/db.php';

/* -----------------------------
   1. Check if user is logged in
--------------------------------*/
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];


/* -----------------------------
   2. Fetch User Info
--------------------------------*/
$query_user = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query_user);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$user_email = $user['email']; // Important for coupon binding


/* -----------------------------
   3. Fetch Orders
--------------------------------*/
$query_orders = "SELECT * FROM orders 
                WHERE user_id = ? 
                ORDER BY created_at DESC";

$stmt = $conn->prepare($query_orders);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();


/* -----------------------------
   4. Fetch Coupons (FIXED)
   - Shows coupons by user_id
   - Also shows coupons bound to email
--------------------------------*/
$query_coupons = "
    SELECT * FROM coupons
    WHERE user_id = ?
       OR (email IS NOT NULL AND email <> '' AND email = ?)
    ORDER BY id DESC
";

$stmt = $conn->prepare($query_coupons);
$stmt->bind_param("is", $user_id, $user_email);
$stmt->execute();
$coupons = $stmt->get_result();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - LUXORA</title>
    <!-- Fonts and Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    <link rel="stylesheet" href="styles/pages.css">
    <link rel="stylesheet" href="styles/pages.css">
    <link rel="stylesheet" href="styles/fonts.css">
    <link rel="stylesheet" href="styles/luxury-theme.css">
    <link rel="stylesheet" href="styles/tailwind.css">
    <link rel="stylesheet" href="styles/theme.css">

    <style>
        .dashboard-container {
            padding: 120px 50px 50px; /* Top padding to account for fixed navbar */
            max-width: 1400px;
            margin: 0 auto;
            min-height: 80vh;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            border-bottom: 1px solid rgba(184, 150, 46, 0.2);
            padding-bottom: 20px;
        }

        .user-welcome h1 {
            font-family: var(--font-luxury);
            font-size: 36px;
            color: var(--gold-primary);
        }

        .user-welcome p {
            color: rgba(243, 243, 243, 0.6);
            margin-top: 5px;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 40px;
        }

        .sidebar {
            background: rgba(20, 20, 20, 0.5);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            height: fit-content;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 10px;
        }

        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            border-radius: 8px;
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: var(--gold-primary);
            color: var(--bg-primary);
        }

        .content-card {
            background: rgba(20, 20, 20, 0.5);
            border-radius: 12px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            margin-bottom: 30px;
            animation: fadeIn 0.5s ease-out;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-title {
            font-family: var(--font-luxury);
            font-size: 24px;
            color: var(--text-primary);
        }

        /* Order Table */
        .order-table {
            width: 100%;
            border-collapse: collapse;
        }

        .order-table th, .order-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .order-table th {
            color: var(--gold-primary);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
        }

        .order-table td {
            color: rgba(243, 243, 243, 0.8);
            font-size: 14px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending { background: rgba(255, 193, 7, 0.2); color: #ffc107; }
        .status-completed { background: rgba(40, 167, 69, 0.2); color: #28a745; }
        .status-cancelled { background: rgba(220, 53, 69, 0.2); color: #dc3545; }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: rgba(243, 243, 243, 0.5);
        }

        /* Coupon Card */
        .coupon-card {
            background: linear-gradient(135deg, rgba(184, 150, 46, 0.1), rgba(10, 10, 10, 0.8));
            border: 1px dashed var(--gold-primary);
            padding: 15px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .coupon-code {
            font-family: monospace;
            font-size: 18px;
            color: var(--gold-highlight);
            letter-spacing: 2px;
        }

        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            .dashboard-container {
                padding-top: 100px;
            }
        }
        /* -----------------------------
   Profile Settings Form Styling
--------------------------------*/
.profile-form {
  margin-top: 10px;
}

.profile-form label {
  display: block;
  margin: 12px 0 6px;
  font-size: 13px;
  letter-spacing: 0.5px;
  color: rgba(243, 243, 243, 0.75);
  font-weight: 500;
  text-transform: uppercase;
}

.profile-form input {
  width: 100%;
  padding: 14px 16px;
  border-radius: 10px;
  border: 1px solid rgba(255, 255, 255, 0.08);
  background: rgba(10, 10, 10, 0.55);
  color: rgba(243, 243, 243, 0.9);
  outline: none;
  transition: all 0.25s ease;
  font-size: 14px;
}

.profile-form input::placeholder {
  color: rgba(243, 243, 243, 0.35);
}

.profile-form input:focus {
  border-color: rgba(184, 150, 46, 0.55);
  box-shadow: 0 0 0 3px rgba(184, 150, 46, 0.12);
}

.profile-form .form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.profile-form hr {
  margin: 22px 0;
  border: none;
  height: 1px;
  background: rgba(255, 255, 255, 0.08);
}

/* Section title inside card */
.profile-form .form-section-title {
  font-family: var(--font-luxury);
  font-size: 18px;
  margin-bottom: 6px;
  color: var(--gold-primary);
}

/* Alerts (success/error) like your theme */
.profile-alert {
  border-radius: 10px;
  padding: 12px 14px;
  margin: 10px 0 18px;
  border: 1px solid rgba(255, 255, 255, 0.08);
  background: rgba(10, 10, 10, 0.55);
  font-size: 14px;
}

.profile-alert.success {
  border-color: rgba(40, 167, 69, 0.35);
  color: rgba(40, 167, 69, 0.95);
}

.profile-alert.error {
  border-color: rgba(220, 53, 69, 0.35);
  color: rgba(220, 53, 69, 0.95);
}

/* Button matches dashboard gold hover feel */
.profile-form .btn-luxury {
  margin-top: 18px;
  width: 100%;
  padding: 14px 18px;
  border-radius: 10px;
  font-weight: 600;
  letter-spacing: 0.5px;
}

/* Mobile */
@media (max-width: 768px) {
  .profile-form .form-row {
    grid-template-columns: 1fr;
  }
}

    </style>
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <div class="dashboard-container">
        
        <div class="dashboard-header">
            <div class="user-welcome">
                <h1>Hello, <?php echo htmlspecialchars($user['username']); ?></h1>
                <p>Welcome to your personal dashboard</p>
            </div>
            <a href="logout.php" class="btn-outline">Logout</a>
        </div>

        <div class="dashboard-grid">
            
            <aside class="sidebar">
                <ul class="sidebar-menu">
                    <li><a href="#" class="active" onclick="showTab('orders')"><i class="fas fa-box-open" style="margin-right: 10px;"></i> My Orders</a></li>
                    <li><a href="#" onclick="showTab('coupons')"><i class="fas fa-ticket-alt" style="margin-right: 10px;"></i> My Coupons</a></li>
                    <li><a href="#" onclick="showTab('profile')"><i class="fas fa-user-edit" style="margin-right: 10px;"></i> Profile Setting</a></li>
                </ul>
            </aside>

            <main class="content">
                
                <!-- Orders Section -->
                <div id="orders-tab" class="content-card">
                    <div class="card-header">
                        <h2 class="card-title">Order History</h2>
                    </div>
                    <?php if ($orders->num_rows > 0): ?>
                        <table class="order-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($order = $orders->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                                <?php echo htmlspecialchars($order['status']); ?>
                                            </span>
                                        </td>
                                        <td><a href="#" style="color: var(--gold-primary);">View</a></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-shopping-bag" style="font-size: 32px; margin-bottom: 10px;"></i>
                            <p>You haven't placed any orders yet.</p>
                            <a href="products.php" class="btn-luxury" style="margin-top: 15px; display: inline-block;">Start Shopping</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Coupons Section -->
                <div id="coupons-tab" class="content-card" style="display: none;">
                    <div class="card-header">
                        <h2 class="card-title">My Coupons</h2>
                    </div>
                    <?php if ($coupons->num_rows > 0): ?>
                        <?php while($coupon = $coupons->fetch_assoc()): ?>
                            <div class="coupon-card">
                                <div>
                                    <div class="coupon-code" style="font-size: 25px;">CODE:&nbsp;<?php echo htmlspecialchars($coupon['code']); ?></div>
                                    <small style="color: #aaa;">Status: <?php echo htmlspecialchars($coupon['status']); ?></small><br>
                                    <small style="color: #aaa;">Max Uses: <?php echo htmlspecialchars($coupon['max_uses']); ?></small><br>
                                    <small style="color: #aaa;">Used: <?php echo htmlspecialchars($coupon['used_count']); ?></small><br>

                                    <small style="color: #aaa;">DIFICULTY: <?php echo htmlspecialchars($coupon['difficulty']); ?></small>
                                </div>
                                <div style="font-size: 20px; color: var(--gold-primary); font-weight: bold;">
                                    <?php echo $coupon['discount']; ?>% OFF
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>No active coupons at the moment.</p>
                        </div>
                    <?php endif; ?>
                </div>

              <div id="profile-tab" class="content-card" style="display: none;">
  <div class="card-header">
    <h2 class="card-title">Profile Settings</h2>
  </div>

  <?php if (isset($_GET['profile_updated'])): ?>
                    <div class="profile-alert success">Profile updated successfully.</div>
                <?php endif; ?>
            
                <?php if (isset($_GET['error'])): ?>
                    <div class="profile-alert error"><?php echo htmlspecialchars($_GET['error']); ?></div>
                <?php endif; ?>
            
                <form action="update_profile.php" method="POST" class="profile-form">
            
                    <div class="form-row">
                        <div>
                            <label>Username</label>
                            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>"
                                required>
                        </div>
            
                        <div>
                            <label>Email Address</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                        </div>
                    </div>
            
                    <hr>
            
                    <div class="form-section-title">Change Password</div>
            
                    <label>Current Password</label>
                    <input type="password" name="current_password" placeholder="Enter current password">
            
                    <div class="form-row">
                        <div>
                            <label>New Password</label>
                            <input type="password" name="new_password" placeholder="Enter new password">
                        </div>
            
                        <div>
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" placeholder="Confirm new password">
                        </div>
                    </div>
            
                    <button type="submit" class="btn-luxury">Save Changes</button>
                </form>
            </div>
            </main>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
function showTab(tabName, el) {
  document.getElementById('orders-tab').style.display = 'none';
  document.getElementById('coupons-tab').style.display = 'none';
  document.getElementById('profile-tab').style.display = 'none';

  document.querySelectorAll('.sidebar-menu a').forEach(a => a.classList.remove('active'));

  document.getElementById(tabName + '-tab').style.display = 'block';
  el.classList.add('active');
}

        
        // Simple polling for real-time updates (reloads page content every 30 seconds)
        // Ideally we would fetch just the data via AJAX, but for MVP reload or simple fetch is fine.
        /*
        setInterval(function() {
            location.reload(); 
        }, 30000);
        */
    </script>
</body>
</html>
