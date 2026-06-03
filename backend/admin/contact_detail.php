<?php
session_start();

// Include database connection and email config
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/send_email_config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$result = mysqli_query($conn, "SELECT * FROM contacts WHERE id = $id");
$contact = mysqli_fetch_assoc($result);

if (!$contact) {
    header('Location: contacts.php');
    exit();
}

// Mark as read when viewing
if ($contact['status'] == 'new') {
    mysqli_query($conn, "UPDATE contacts SET status = 'read' WHERE id = $id");
}

$reply_sent = false;
$reply_error = '';

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_reply'])) {
    $reply_message = trim($_POST['reply_message'] ?? '');
    
    if (empty($reply_message)) {
        $reply_error = 'Reply message is required';
    } else {
        // Send email using PHPMailer
        $email_result = sendReplyEmail(
            $contact['email'],
            $contact['full_name'],
            $reply_message,
            $contact['message']
        );
        
        if ($email_result['success']) {
            // Save reply to database
            $escaped_reply = mysqli_real_escape_string($conn, $reply_message);
            mysqli_query($conn, "UPDATE contacts SET admin_reply = '$escaped_reply', status = 'replied', replied_at = NOW() WHERE id = $id");
            $reply_sent = true;
            // Refresh contact data
            $result = mysqli_query($conn, "SELECT * FROM contacts WHERE id = $id");
            $contact = mysqli_fetch_assoc($result);
        } else {
            $reply_error = $email_result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Detail - Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f5f5f5; }
        .sidebar {
            position: fixed; left: 0; top: 0; width: 250px; height: 100%;
            background: #0F4C5C; color: white; padding: 2rem 1rem;
        }
        .sidebar h3 { margin-bottom: 2rem; text-align: center; }
        .sidebar a {
            display: block; color: white; text-decoration: none;
            padding: 0.75rem 1rem; margin: 0.5rem 0;
            border-radius: 0.5rem;
        }
        .sidebar a:hover { background: #E76F51; }
        .main-content { margin-left: 250px; padding: 2rem; }
        .header {
            background: white; padding: 1rem 2rem; border-radius: 0.5rem;
            margin-bottom: 2rem; display: flex; justify-content: space-between;
            align-items: center;
        }
        .card {
            background: white; border-radius: 0.5rem; padding: 1.5rem;
            margin-bottom: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card h3 { color: #0F4C5C; margin-bottom: 1rem; border-bottom: 2px solid #E76F51; padding-bottom: 0.5rem; }
        .message-box {
            background: #f8f9fa; padding: 1rem; border-radius: 0.5rem;
            margin-top: 0.5rem; border-left: 3px solid #E76F51;
        }
        .reply-box {
            background: #e8f5e9; padding: 1rem; border-radius: 0.5rem;
            margin-top: 0.5rem; border-left: 3px solid #4CAF50;
        }
        .label { font-weight: 600; color: #0F4C5C; margin-bottom: 0.25rem; margin-top: 1rem; }
        .label:first-of-type { margin-top: 0; }
        .reply-form textarea {
            width: 100%; padding: 0.75rem; border: 1px solid #ddd;
            border-radius: 0.5rem; margin-bottom: 1rem; font-family: inherit; resize: vertical;
        }
        .reply-form textarea:focus {
            outline: none; border-color: #E76F51;
        }
        .btn-send {
            background: #0F4C5C; color: white; padding: 0.75rem 1.5rem;
            border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 600;
        }
        .btn-send:hover { background: #E76F51; }
        .btn-back {
            background: #6c757d; color: white; padding: 0.5rem 1rem;
            text-decoration: none; border-radius: 0.5rem; display: inline-block;
        }
        .success { background: #d4edda; color: #155724; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; }
        .error { background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; }
        .logout-btn { background: #E76F51; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none; }
        .info-value { 
            background: #f5f5f5; padding: 0.5rem; border-radius: 0.25rem; 
            margin-top: 0.25rem; margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>AI Solutions Admin</h3>
        <a href="dashboard.php">Dashboard</a>
        <a href="contacts.php" class="active">Contacts</a>
        <a href="subscribers.php">Subscribers</a>
        <a href="chats.php">Chat Logs</a>
        <a href="logout.php" style="margin-top: 2rem;">Logout</a>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Contact Message Detail</h1>
            <div>
                <a href="contacts.php" class="btn-back">← Back to Contacts</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
        
        <?php if ($reply_sent): ?>
            <div class="success">✅ Reply sent successfully to <?php echo htmlspecialchars($contact['email']); ?>!</div>
        <?php endif; ?>
        
        <?php if ($reply_error): ?>
            <div class="error">❌ <?php echo $reply_error; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <h3>Contact Information</h3>
            <div class="label">Name:</div>
            <div class="info-value"><?php echo htmlspecialchars($contact['full_name']); ?></div>
            
            <div class="label">Email:</div>
            <div class="info-value"><?php echo htmlspecialchars($contact['email']); ?></div>
            
            <div class="label">Phone:</div>
            <div class="info-value"><?php echo htmlspecialchars($contact['phone'] ?: 'Not provided'); ?></div>
            
            <div class="label">Company:</div>
            <div class="info-value"><?php echo htmlspecialchars($contact['company'] ?: 'Not provided'); ?></div>
            
            <div class="label">Service Interest:</div>
            <div class="info-value"><?php echo htmlspecialchars($contact['service'] ?: 'Not specified'); ?></div>
            
            <div class="label">Date Received:</div>
            <div class="info-value"><?php echo date('F d, Y - h:i A', strtotime($contact['created_at'])); ?></div>
        </div>
        
        <div class="card">
            <h3>User Message</h3>
            <div class="message-box">
                <?php echo nl2br(htmlspecialchars($contact['message'])); ?>
            </div>
        </div>
        
        <?php if (!empty($contact['admin_reply'])): ?>
        <div class="card">
            <h3>Admin Reply (Sent via Email)</h3>
            <div class="reply-box">
                <strong>Reply sent on:</strong> <?php echo date('F d, Y - h:i A', strtotime($contact['replied_at'])); ?><br><br>
                <?php echo nl2br(htmlspecialchars($contact['admin_reply'])); ?>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <h3>Send Reply to <?php echo htmlspecialchars($contact['full_name']); ?></h3>
            <form method="POST" class="reply-form">
                <div class="label">To:</div>
                <div class="info-value" style="background: #e8f0fe;"><?php echo htmlspecialchars($contact['email']); ?></div>
                
                <div class="label" style="margin-top: 1rem;">Reply Message:</div>
                <textarea name="reply_message" rows="6" placeholder="Type your reply here..." required></textarea>
                
                <button type="submit" name="send_reply" class="btn-send">Send Reply via Email</button>
            </form>
            <p style="font-size: 12px; color: #666; margin-top: 1rem;">
                📧 The user will receive this reply instantly via email.
            </p>
        </div>
    </div>
</body>
</html>