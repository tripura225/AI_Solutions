<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Delete chat
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    executeQuery($conn, "DELETE FROM chat_logs WHERE id = ?", [$id]);
    header('Location: chats.php');
    exit();
}

$chats = executeQuery($conn, "SELECT * FROM chat_logs ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Logs - Admin</title>
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
        .sidebar a:hover, .sidebar a.active { background: #E76F51; }
        .main-content { margin-left: 250px; padding: 2rem; }
        .header {
            background: white; padding: 1rem 2rem; border-radius: 0.5rem;
            margin-bottom: 2rem; display: flex; justify-content: space-between;
            align-items: center;
        }
        table { width: 100%; background: white; border-radius: 0.5rem; overflow: hidden; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #0F4C5C; color: white; }
        .user-msg { color: #0F4C5C; font-weight: 500; }
        .bot-msg { color: #E76F51; }
        .btn-delete { background: #e74c3c; color: white; padding: 0.25rem 0.5rem; border-radius: 0.25rem; text-decoration: none; font-size: 0.75rem; }
        .logout-btn { background: #E76F51; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>AI Solutions Admin</h3>
        <a href="dashboard.php">Dashboard</a>
        <a href="contacts.php">Contacts</a>
        <a href="subscribers.php">Subscribers</a>
        <a href="chats.php" class="active">Chat Logs</a>
        <a href="logout.php" style="margin-top: 2rem;">Logout</a>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Chat Logs</h1>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
        
        <table>
            <thead><tr><th>ID</th><th>Session ID</th><th>User Message</th><th>Bot Response</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody>
                <?php while ($chat = mysqli_fetch_assoc($chats)): ?>
                <tr>
                    <td><?php echo $chat['id']; ?></td>
                    <td><?php echo substr($chat['session_id'], 0, 20); ?>...</td>
                    <td class="user-msg"><?php echo htmlspecialchars(substr($chat['user_message'], 0, 50)); ?>...</td>
                    <td class="bot-msg"><?php echo htmlspecialchars(substr($chat['bot_response'], 0, 50)); ?>...</td>
                    <td><?php echo date('M d, H:i', strtotime($chat['created_at'])); ?></td>
                    <td><a href="?delete=<?php echo $chat['id']; ?>" class="btn-delete" onclick="return confirm('Delete?')">Delete</a></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>