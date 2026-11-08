<?php
include 'includes/db.php';
include 'includes/meta_helper.php';

// Fetch Meta Tags
$meta_tags = fetch_page_meta($conn, 'contact');

// Handle Form Submission
$message_status = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $message = $conn->real_escape_string($_POST['message']);

    $sql = "INSERT INTO contact_messages (name, email, phone, subject, message) VALUES ('$name', '$email', '$phone', '$subject', '$message')";

    if ($conn->query($sql) === TRUE) {
        $message_status = '<div class="alert success">Message sent successfully!</div>';
    } else {
        $message_status = '<div class="alert error">Error: ' . $conn->error . '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($meta_tags['meta_title']); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($meta_tags['meta_description']); ?>">
  <meta name="keywords" content="<?php echo htmlspecialchars($meta_tags['meta_keywords']); ?>">
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="styles/pages.css">
  <link rel="stylesheet" href="styles/fonts.css">
  <link rel="stylesheet" href="styles/luxury-theme.css">
  <link rel="stylesheet" href="styles/tailwind.css">
  <link rel="stylesheet" href="styles/theme.css">
  <style>
/* ===== Contact Page Luxury Theme ===== */

body{
  background: #0a0a0a;
  color: rgba(243,243,243,0.9);
  font-family: 'Inter', sans-serif;
}

.contact-page{
  padding: 80px 20px;
  display: flex;
  justify-content: center;
}

.contact-form-container{
  width: 100%;
  max-width: 720px;
  padding: 40px;
  border-radius: 14px;

  background: rgba(10, 10, 10, 0.55);
  backdrop-filter: blur(18px);
  border: 1px solid rgba(184, 150, 46, 0.18);
  box-shadow: 0 25px 60px rgba(0,0,0,0.65);
}

/* Heading */
.contact-form-container h1{
  font-family: var(--font-luxury);
  font-size: 32px;
  text-align: center;
  margin-bottom: 35px;
  color: var(--gold-primary);
  letter-spacing: 2px;
  text-transform: uppercase;
}

/* Form layout */
#contactForm{
  display: flex;
  flex-direction: column;
  gap: 22px;
}

.form-group{
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label{
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  color: rgba(243,243,243,0.7);
}

/* Inputs */
.form-group input,
.form-group textarea{
  padding: 14px 16px;
  border-radius: 10px;
  border: 1px solid rgba(255,255,255,0.08);
  background: rgba(0,0,0,0.35);
  color: #fff;
  font-size: 14px;
  transition: all 0.3s ease;
  outline: none;
}

/* Focus effect */
.form-group input:focus,
.form-group textarea:focus{
  border-color: var(--gold-primary);
  box-shadow: 0 0 0 2px rgba(184,150,46,0.25);
}

/* Textarea */
textarea{
  resize: vertical;
  min-height: 140px;
}

/* Button */
.btn-luxury{
  margin-top: 10px;
  padding: 18px;
  border-radius: 12px;
  border: none;
  cursor: pointer;
  font-weight: 800;
  letter-spacing: 2px;
  text-transform: uppercase;
  font-size: 13px;

  background: linear-gradient(135deg, var(--gold-primary), var(--gold-secondary));
  color: #000;

  transition: all 0.35s ease;
}

.btn-luxury:hover{
  transform: translateY(-4px);
  box-shadow: 0 15px 40px rgba(184,150,46,0.35);
  filter: brightness(1.08);
}

.btn-luxury:active{
  transform: translateY(-1px);
}

/* Success & Error Messages */
.alert{
  margin-top: 25px;
  padding: 14px;
  border-radius: 10px;
  text-align: center;
  font-size: 14px;
}

.alert.success{
  background: rgba(50, 120, 70, 0.15);
  border: 1px solid rgba(80,200,120,0.35);
  color: #6df0a3;
}

.alert.error{
  background: rgba(120, 40, 40, 0.15);
  border: 1px solid rgba(255,100,100,0.35);
  color: #ff7a7a;
}

/* Responsive */
@media (max-width: 768px){
  .contact-form-container{
    padding: 30px 20px;
  }

  .contact-form-container h1{
    font-size: 24px;
  }
}

  </style>
</head>

<body>
  <?php include 'includes/header.php'; ?>

  <!-- Contact Page -->
  <section class="contact-page">
    <div class="contact-form-container">
      <h1>Contact Us</h1>
      <form id="contactForm" method="POST">
        <div class="form-group">
          <label>Name *</label>
          <input type="text" name="name" required />
        </div>
        <div class="form-group">
          <label>Email *</label>
          <input type="email" name="email" required />
        </div>
        <div class="form-group">
          <label>Phone</label>
          <input type="tel" name="phone" />
        </div>
        <div class="form-group">
          <label>Subject *</label>
          <input type="text" name="subject" required />
        </div>
        <div class="form-group">
          <label>Message *</label>
          <textarea name="message" required></textarea>
        </div>
        <button type="submit" class="btn-luxury">Send Message</button>
      </form>

      <div id="contactSuccessMessage"><?php echo $message_status; ?></div>
      <div id="contactErrorMessage"></div>
    </div>
  </section>

  <?php include 'includes/footer.php'; ?>

  <script src="js/contact.js"></script>
</body>
</html>