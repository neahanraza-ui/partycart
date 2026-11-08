<?php
include 'includes/db.php';

$error = '';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Use prepared statements
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check plain text first (for new users), then fallback to hash (for old users)
    // Password check: supports BOTH plain text and hashed,
// and upgrades plain text to hashed after successful login.
    $password_valid = false;

    if ($user) {
        $dbPass = (string) ($user['password'] ?? '');

        // Detect if hashed
        $info = password_get_info($dbPass);
        $isHashed = ($info['algo'] !== 0);

        if ($isHashed) {
            // Hashed password in DB
            $password_valid = password_verify($password, $dbPass);
        } else {
            // Plain text password in DB (legacy)
            $password_valid = hash_equals(trim($dbPass), trim($password));

            // ✅ Upgrade to hashed after successful plain-text login
            if ($password_valid) {
                $newHash = password_hash(trim($password), PASSWORD_DEFAULT);
                $stmtUp = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmtUp->bind_param("si", $newHash, $user['id']);
                $stmtUp->execute();
            }
        }
    }


    if ($password_valid) {
        // Store user info in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'admin') {
            header('Location: admin/dashboard.php');
        } else {
            // Store a flag to trigger cart sync on the next page load
            $_SESSION['login_sync_needed'] = true;
            header('Location: user_dashboard.php');
        }
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
include 'includes/meta_helper.php';
$meta_tags = fetch_page_meta($conn, 'login');
?>
    <title><?php echo htmlspecialchars($meta_tags['meta_title']); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($meta_tags['meta_description']); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($meta_tags['meta_keywords']); ?>">
    <!-- Fonts and Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    <link rel="stylesheet" href="styles/pages.css">
    <link rel="stylesheet" href="styles/fonts.css">
    <link rel="stylesheet" href="styles/luxury-theme.css">
    <link rel="stylesheet" href="styles/tailwind.css">
    <link rel="stylesheet" href="styles/theme.css">
    
    <style>
        /* Specific Styles for Auth Page */
        .auth-container {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            background: linear-gradient(rgba(10, 10, 10, 0.8), rgba(10, 10, 10, 0.8)), url('assets/auth-bg.jpg');
            background-size: cover;
            background-position: center;
        }

        .auth-box {
            background: rgba(20, 20, 20, 0.95);
            padding: 40px;
            border-radius: 12px;
            border: 1px solid rgba(184, 150, 46, 0.2);
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.8s ease-out;
        }

        .auth-title {
            font-family: var(--font-luxury);
            font-size: 32px;
            color: var(--gold-primary);
            text-align: center;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: var(--text-primary);
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 6px;
            font-family: var(--font-body);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--gold-primary);
            background: rgba(255, 255, 255, 0.1);
        }

        .auth-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: rgba(243, 243, 243, 0.6);
        }

        .auth-footer a {
            color: var(--gold-primary);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }
        .alert-error {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid #dc3545;
            color: #ff6b6b;
        }
    </style>
</head>
<body>

    <!-- Header removed for login page -->
    <div style="position: absolute; top: 20px; left: 20px;">
        <a href="index.php" style="color: var(--gold-primary); text-decoration: none; font-family: var(--font-luxury); font-size: 18px;">&larr; Back to Home</a>
    </div>

    <section class="auth-container">
        <div class="auth-box">
            <h2 class="auth-title">Welcome Back</h2>
            
            <?php if(!empty($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required placeholder="Enter your username">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="Enter your password">
                </div>
                
                <button type="submit" name="login" class="btn-luxury" style="width: 100%;">Login</button>
            </form>

            <div class="auth-footer">
                Don't have an account? <a href="signup.php">Sign Up</a>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>