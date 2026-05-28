<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        $data = $_POST;
    }
    
    $full_name = sanitize($data['full_name'] ?? '');
    $email = sanitize($data['email'] ?? '');
    $phone = sanitize($data['phone'] ?? '');
    $company = sanitize($data['company'] ?? '');
    $service = sanitize($data['service'] ?? '');
    $message = sanitize($data['message'] ?? '');
    
    $errors = [];
    
    if (empty($full_name)) $errors[] = 'Full name is required';
    if (empty($email)) $errors[] = 'Email is required';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format';
    if (empty($message)) $errors[] = 'Message is required';
    
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => $errors[0]]);
        exit();
    }
    
    $sql = "INSERT INTO contacts (full_name, email, phone, company, service, message) VALUES (?, ?, ?, ?, ?, ?)";
    $result = executeQuery($conn, $sql, [$full_name, $email, $phone, $company, $service, $message]);
    
    if ($result !== false) {
        echo json_encode(['success' => true, 'message' => 'Thank you! We will get back to you within 24 hours.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>