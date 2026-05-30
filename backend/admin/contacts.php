<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Mark as read
if (isset($_GET['mark_read'])) {
    $id = $_GET['mark_read'];
    executeQuery($conn, "UPDATE contacts SET status = 'read' WHERE id = ?", [$id]);
    header('Location: contacts.php');
    exit();
}

// Delete contact
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    executeQuery($conn, "DELETE FROM contacts WHERE id = ?", [$id]);
    header('Location: contacts.php');
    exit();
}

$contacts = executeQuery($conn, "SELECT * FROM contacts ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacts - Admin</title>
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
        table {
            width: 100%; background: white; border-radius: 0.5rem;
            overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #0F4C5C; color: white; }
        .status-new { background: #E76F51; color: white; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; }
        .status-read { background: #2A9D8F; color: white; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; }
        .btn { padding: 0.25rem 0.5rem; border-radius: 0.25rem; text-decoration: none; font-size: 0.75rem; }
        .btn-read { background: #2A9D8F; color: white; }
        .btn-delete { background: #e74c3c; color: white; }
        .logout-btn { background: #E76F51; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none; }
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
            <h1>Contact Messages</h1>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
        
        <table>
            <thead>
                <tr><th>ID</th><th>Name</th><th>Email</th><th>Service</th><th>Message</th><th>Status</th><th>Date</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php while ($contact = mysqli_fetch_assoc($contacts)): ?>
                <tr>
                    <td><?php echo $contact['id']; ?></td>
                    <td><?php echo htmlspecialchars($contact['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($contact['email']); ?></td>
                    <td><?php echo htmlspecialchars($contact['service'] ?: 'N/A'); ?></td>
                    <td><?php echo substr(htmlspecialchars($contact['message']), 0, 50); ?>...</td>
                    <td><span class="status-<?php echo $contact['status']; ?>"><?php echo $contact['status']; ?></span></td>
                    <td><?php echo date('M d, Y', strtotime($contact['created_at'])); ?></td>
                    <td>
                        <?php if ($contact['status'] == 'new'): ?>
                            <a href="?mark_read=<?php echo $contact['id']; ?>" class="btn btn-read">Mark Read</a>
                        <?php endif; ?>
                        <a href="?delete=<?php echo $contact['id']; ?>" class="btn btn-delete" onclick="return confirm('Delete?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>