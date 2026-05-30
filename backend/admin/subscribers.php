<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Delete subscriber
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    executeQuery($conn, "DELETE FROM newsletter_subscribers WHERE id = ?", [$id]);
    header('Location: subscribers.php');
    exit();
}

$subscribers = executeQuery($conn, "SELECT * FROM newsletter_subscribers ORDER BY subscribed_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscribers - Admin</title>
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
        .btn-delete { background: #e74c3c; color: white; padding: 0.25rem 0.5rem; border-radius: 0.25rem; text-decoration: none; font-size: 0.75rem; }
        .logout-btn { background: #E76F51; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>AI Solutions Admin</h3>
        <a href="dashboard.php">Dashboard</a>
        <a href="contacts.php">Contacts</a>
        <a href="subscribers.php" class="active">Subscribers</a>
        <a href="chats.php">Chat Logs</a>
        <a href="logout.php" style="margin-top: 2rem;">Logout</a>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Newsletter Subscribers</h1>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
        
        <table>
            <thead><tr><th>ID</th><th>Email</th><th>Subscribed Date</th><th>Actions</th></tr></thead>
            <tbody>
                <?php while ($sub = mysqli_fetch_assoc($subscribers)): ?>
                <tr>
                    <td><?php echo $sub['id']; ?></td>
                    <td><?php echo htmlspecialchars($sub['email']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($sub['subscribed_at'])); ?></td>
                    <td><a href="?delete=<?php echo $sub['id']; ?>" class="btn-delete" onclick="return confirm('Delete?')">Delete</a></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>