<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$type = isset($_GET['type']) ? $_GET['type'] : '';
$format = isset($_GET['format']) ? $_GET['format'] : 'csv';

if (empty($type)) {
    die('No export type specified');
}

// Set filename
$filename = $type . '_' . date('Y-m-d_H-i-s');

// Set headers for download
if ($format == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Add UTF-8 BOM for Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
} else {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
    
    $output = fopen('php://output', 'w');
}

// Export based on type
switch ($type) {
case 'contacts':
    $result = mysqli_query($conn, "SELECT * FROM contacts ORDER BY created_at DESC");
    
    // Add Job Title to headers
    fputcsv($output, ['ID', 'Full Name', 'Email', 'Phone', 'Job Title', 'Company', 'Service', 'Message', 'Status', 'Created At', 'Admin Reply', 'Replied At']);
    
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['id'],
            $row['full_name'],
            $row['email'],
            $row['phone'],
            $row['job_title'] ?? '',
            $row['company'],
            $row['service'],
            strip_tags($row['message']),
            $row['status'],
            $row['created_at'],
            strip_tags($row['admin_reply'] ?? ''),
            $row['replied_at'] ?? ''
        ]);
    }
    break;
        
    case 'subscribers':
        // Get all subscribers
        $result = mysqli_query($conn, "SELECT * FROM newsletter_subscribers ORDER BY subscribed_at DESC");
        
        // Add headers
        fputcsv($output, ['ID', 'Email', 'Subscribed At']);
        
        // Add data rows
        while ($row = mysqli_fetch_assoc($result)) {
            fputcsv($output, [
                $row['id'],
                $row['email'],
                $row['subscribed_at']
            ]);
        }
        break;
        
    case 'chats':
        // Get all chat logs
        $result = mysqli_query($conn, "SELECT * FROM chat_logs ORDER BY created_at DESC");
        
        // Add headers
        fputcsv($output, ['ID', 'Session ID', 'User Message', 'Bot Response', 'Created At']);
        
        // Add data rows
        while ($row = mysqli_fetch_assoc($result)) {
            fputcsv($output, [
                $row['id'],
                $row['session_id'],
                strip_tags($row['user_message']),
                strip_tags($row['bot_response']),
                $row['created_at']
            ]);
        }
        break;
        
    default:
        die('Invalid export type');
}

fclose($output);
exit();
?>