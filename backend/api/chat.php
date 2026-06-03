<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Start session for unique chat session ID
session_start();

// Generate unique session ID for this chat
if (!isset($_SESSION['chat_session_id'])) {
    $_SESSION['chat_session_id'] = uniqid('chat_', true);
}
$session_id = $_SESSION['chat_session_id'];

// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'ai_solutions';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {   
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        $data = $_POST;
    }
    
    $user_message = isset($data['message']) ? trim($data['message']) : '';
    $bot_response = isset($data['response']) ? trim($data['response']) : '';
    
    if (empty($user_message)) {
        echo json_encode(['success' => false, 'message' => 'Message is required']);
        exit();
    }
    
    // If bot_response is empty, store a default message
    if (empty($bot_response)) {
        $bot_response = "Bot response will be logged separately";
    }
    
    // SECURE: Using prepared statement to prevent SQL injection
   // Add email field
$user_email = isset($data['email']) ? trim($data['email']) : '';

// Update SQL
$sql = "INSERT INTO chat_logs (session_id, user_message, bot_response, user_email) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssss", $session_id, $user_message, $bot_response, $user_email);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sss", $session_id, $user_message, $bot_response);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true, 'session_id' => $session_id, 'message' => 'Chat saved']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Execute error: ' . mysqli_stmt_error($stmt)]);
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['success' => false, 'message' => 'Prepare error: ' . mysqli_error($conn)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

mysqli_close($conn);
?>