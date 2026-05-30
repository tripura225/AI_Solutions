<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

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

// Set charset
mysqli_set_charset($conn, "utf8mb4");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        $data = $_POST;
    }
    
    // Check if data is received
    if (empty($data)) {
        echo json_encode(['success' => false, 'message' => 'No data received']);
        exit();
    }
    
    // Sanitize inputs
    $full_name = isset($data['full_name']) ? trim($data['full_name']) : '';
    $email = isset($data['email']) ? trim($data['email']) : '';
    $phone = isset($data['phone']) ? trim($data['phone']) : '';
    $company = isset($data['company']) ? trim($data['company']) : '';
    $service = isset($data['service']) ? trim($data['service']) : '';
    $message = isset($data['message']) ? trim($data['message']) : '';
    
    // Escape for database
    $full_name = mysqli_real_escape_string($conn, $full_name);
    $email = mysqli_real_escape_string($conn, $email);
    $phone = mysqli_real_escape_string($conn, $phone);
    $company = mysqli_real_escape_string($conn, $company);
    $service = mysqli_real_escape_string($conn, $service);
    $message = mysqli_real_escape_string($conn, $message);
    
    $errors = [];
    
    // Validation
    if (empty($full_name)) {
        $errors[] = 'Full name is required';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    
    if (empty($phone)) {
        $errors[] = 'Phone number is required';
    }
    
    if (empty($company)) {
        $errors[] = 'Company name is required';
    }
    
    if (empty($service) || $service === 'Select Service Interest') {
        $errors[] = 'Please select a service';
    }
    
    if (empty($message)) {
        $errors[] = 'Message is required';
    }
    
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => $errors[0]]);
        exit();
    }
    
    // Insert into database using direct query (simpler for debugging)
    $sql = "INSERT INTO contacts (full_name, email, phone, company, service, message) 
            VALUES ('$full_name', '$email', '$phone', '$company', '$service', '$message')";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Thank you! We will get back to you within 24 hours.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

mysqli_close($conn);
?>