<?php
session_start();
include 'db.php';

/* Check if admin is logged in */
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

/* Fetch messages (Newest first) */
$sql = "SELECT id, name, email, phone, subject, message 
        FROM contact_messages 
        ORDER BY id DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Admin</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="admin.css">

    <style>
        /* ===== Base ===== */
        body {
            background: #0a0a0a;
            color: rgba(243, 243, 243, 0.9);
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Layout */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            padding: 40px;
        }

        /* Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 28px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #d4af37;
        }

        /* Glass Card */
        .messages-card {
            background: rgba(10, 10, 10, 0.55);
            backdrop-filter: blur(18px);
            border: 1px solid rgba(184, 150, 46, 0.18);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.65);
            overflow-x: auto;
        }

        /* Table */
        .messages-table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Table Header */
        .messages-table th {
            padding: 16px;
            text-align: left;
            background: rgba(0, 0, 0, 0.4);
            color: #d4af37;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(184, 150, 46, 0.3);
        }

        /* Table Cells */
        .messages-table td {
            padding: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 14px;
            color: rgba(243, 243, 243, 0.85);
        }

        /* Hover */
        .messages-table tr:hover {
            background: rgba(184, 150, 46, 0.05);
            transition: 0.3s ease;
        }

        /* Truncate long messages */
        .messages-table td.message-cell {
            max-width: 250px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* No messages */
        .no-messages {
            padding: 30px;
            text-align: center;
            color: rgba(243, 243, 243, 0.5);
        }

        /* Responsive */
        @media(max-width: 768px) {
            .main-content {
                padding: 20px;
            }

            .messages-table th,
            .messages-table td {
                padding: 12px;
                font-size: 12px;
            }
        }
    </style>
</head>

<body>
    
    <div class="admin-wrapper">
        
        <?php include('sidebar.php'); ?>

        <main class="main-content">

            <div class="page-header">
                <h1>Contact Messages</h1>
            </div>

            <div class="messages-card">
                <table class="messages-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Subject</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                    <td class="message-cell">
                                        <?php echo htmlspecialchars($row['message']); ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="no-messages">
                                    No contact messages found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </main>

    </div>

</body>

</html>