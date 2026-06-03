<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$chat = executeQuery($conn, "SELECT * FROM chat_logs WHERE id = ?", [$id]);
$chat = mysqli_fetch_assoc($chat);

if (!$chat) {
    header('Location: chats.php');
    exit();
}

$reply_sent = false;
$reply_error = '';

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_reply'])) {
    $user_email = trim($_POST['user_email'] ?? '');
    $reply_message = trim($_POST['reply_message'] ?? '');
    
    if (empty($user_email)) {
        $reply_error = 'Email address is required';
    } elseif (empty($reply_message)) {
        $reply_error = 'Reply message is required';
    } else {
        // Send email
        $to = $user_email;
        $subject = "Reply from AI Solutions - Regarding your inquiry";
        $headers = "From: info@ai-solution.com\r\n";
        $headers .= "Reply-To: info@ai-solution.com\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        $email_body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0F4C5C; color: white; padding: 20px; text-align: center; }
                .content { background: #f9f9f9; padding: 20px; }
                .message { background: white; padding: 15px; border-left: 4px solid #E76F51; margin: 15px 0; }
                .footer { text-align: center; padding: 15px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>AI Solutions</h2>
                </div>
                <div class='content'>
                    <p>Dear Customer,</p>
                    <div class='message'>
                        <p><strong>Reply to your inquiry:</strong></p>
                        <p>" . nl2br(htmlspecialchars($reply_message)) . "</p>
                    </div>
                    <p>Original message:</p>
                    <div class='message' style='background:#f0f0f0'>
                        <p><strong>You asked:</strong></p>
                        <p>" . htmlspecialchars($chat['user_message']) . "</p>
                    </div>
                    <p>Best regards,<br><strong>AI Solutions Team</strong></p>
                </div>
                <div class='footer'>
                    <p>This is an automated response from AI Solutions.</p>
                    <p>© 2026 AI Solutions. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        if (mail($to, $subject, $email_body, $headers)) {
            $reply_sent = true;
            // Log that reply was sent (optional)
            $log_sql = "UPDATE chat_logs SET status = 'replied' WHERE id = ?";
            executeQuery($conn, $log_sql, [$id]);
        } else {
            $reply_error = 'Failed to send email. Please check mail configuration.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Detail - Admin</title>
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
        .user-msg { background: #e3f2fd; border-left-color: #2196F3; }
        .bot-msg { background: #f1f8e9; border-left-color: #4CAF50; }
        .label { font-weight: 600; color: #0F4C5C; margin-bottom: 0.25rem; }
        .reply-form { margin-top: 1rem; }
        .reply-form textarea, .reply-form input {
            width: 100%; padding: 0.75rem; border: 1px solid #ddd;
            border-radius: 0.5rem; margin-bottom: 1rem; font-family: inherit;
        }
        .reply-form textarea:focus, .reply-form input:focus {
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
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>AI Solutions Admin</h3>
        <a href="dashboard.php">Dashboard</a>
        <a href="contacts.php">Contacts</a>
        <a href="subscribers.php">Subscribers</a>
        <a href="chats.php">Chat Logs</a>
        <a href="logout.php" style="margin-top: 2rem;">Logout</a>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Chat Detail</h1>
            <div>
                <a href="chats.php" class="btn-back">← Back to Chats</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
        
        <?php if ($reply_sent): ?>
            <div class="success">✅ Reply sent successfully to the user!</div>
        <?php endif; ?>
        
        <?php if ($reply_error): ?>
            <div class="error">❌ <?php echo $reply_error; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <h3>Conversation Details</h3>
            <div class="label">Session ID:</div>
            <p><?php echo htmlspecialchars($chat['session_id']); ?></p>
            
            <div class="label" style="margin-top: 1rem;">Date & Time:</div>
            <p><?php echo date('F d, Y - h:i A', strtotime($chat['created_at'])); ?></p>
        </div>
        
        <div class="card">
            <h3>User Message</h3>
            <div class="message-box user-msg">
                <?php echo nl2br(htmlspecialchars($chat['user_message'])); ?>
            </div>
        </div>
        
        <div class="card">
            <h3>Bot Response</h3>
            <div class="message-box bot-msg">
                <?php echo nl2br(htmlspecialchars($chat['bot_response'])); ?>
            </div>
        </div>
        
        <div class="card">
            <h3>Send Reply to User</h3>
            <form method="POST" class="reply-form">
                <div class="label">User Email:</div>
                <input type="email" name="user_email" placeholder="Enter user's email address" required>
                
                <div class="label">Reply Message:</div>
                <textarea name="reply_message" rows="5" placeholder="Type your reply here..."></textarea required>
                
                <button type="submit" name="send_reply" class="btn-send">Send Reply via Email</button>
            </form>
            <p style="font-size: 12px; color: #666; margin-top: 1rem;">
                ⚠️ Note: For email to work, you need to configure SMTP settings in XAMPP's php.ini file.
            </p>
        </div>
    </div>
</body>
</html>