<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Get counts
$contacts_count = mysqli_fetch_assoc(executeQuery($conn, "SELECT COUNT(*) as count FROM contacts"))['count'];
$subscribers_count = mysqli_fetch_assoc(executeQuery($conn, "SELECT COUNT(*) as count FROM newsletter_subscribers"))['count'];
$chats_count = mysqli_fetch_assoc(executeQuery($conn, "SELECT COUNT(*) as count FROM chat_logs"))['count'];
$unread_count = mysqli_fetch_assoc(executeQuery($conn, "SELECT COUNT(*) as count FROM contacts WHERE status = 'new'"))['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AI Solutions</title>
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
            border-radius: 0.5rem; transition: background 0.3s;
        }
        .sidebar a:hover, .sidebar a.active { background: #E76F51; }
        .main-content { margin-left: 250px; padding: 2rem; }
        .header {
            background: white; padding: 1rem 2rem; border-radius: 0.5rem;
            margin-bottom: 2rem; display: flex; justify-content: space-between;
            align-items: center;
        }
        .stats-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem; margin-bottom: 2rem;
        }
        .stat-card {
            background: white; padding: 1.5rem; border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stat-card h3 { color: #7A8B9E; font-size: 0.875rem; margin-bottom: 0.5rem; }
        .stat-number { font-size: 2rem; font-weight: bold; color: #0F4C5C; }
        .logout-btn {
            background: #E76F51; color: white; padding: 0.5rem 1rem;
            border-radius: 0.5rem; text-decoration: none;
        }
        .logout-btn:hover { background: #D45A3A; }
        .export-buttons {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-top: 1rem;
}
.export-link {
    display: block;
    padding: 0.5rem;
    background: #f5f5f5;
    color: #0F4C5C;
    text-decoration: none;
    border-radius: 0.25rem;
    text-align: center;
}
.export-link:hover {
    background: #E76F51;
    color: white;
}
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>AI Solutions Admin</h3>
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="contacts.php">Contacts</a>
        <a href="subscribers.php">Subscribers</a>
        <a href="chats.php">Chat Logs</a>
        <a href="logout.php" style="margin-top: 2rem;">Logout</a>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Dashboard</h1>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Contacts</h3>
                <div class="stat-number"><?php echo $contacts_count; ?></div>
            </div>
            <div class="stat-card">
                <h3>Unread Messages</h3>
                <div class="stat-number" style="color: #E76F51;"><?php echo $unread_count; ?></div>
            </div>
            <div class="stat-card">
                <h3>Newsletter Subscribers</h3>
                <div class="stat-number"><?php echo $subscribers_count; ?></div>
            </div>
            <div class="stat-card">
                <h3>Chat Sessions</h3>
                <div class="stat-number"><?php echo $chats_count; ?></div>
            </div>
            <div class="stat-card">
    <h3>📊 Export Data</h3>
    <div class="export-buttons">
        <a href="export.php?type=contacts&format=csv" class="export-link">📄 Export Contacts (CSV)</a>
        <a href="export.php?type=subscribers&format=csv" class="export-link">📧 Export Subscribers (CSV)</a>
        <a href="export.php?type=chats&format=csv" class="export-link">💬 Export Chat Logs (CSV)</a>
    </div>
</div>
        </div>
    </div>
</body>
</html>