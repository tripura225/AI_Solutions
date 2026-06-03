<?php
// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'ai_solutions';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Your password
$plain_password = 'Abcd#$123';
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

// Delete old admin
mysqli_query($conn, "DELETE FROM admin_users WHERE username = 'admin'");

// Insert new admin with hashed password
$sql = "INSERT INTO admin_users (username, password, email) VALUES ('admin', '$hashed_password', 'admin@ai-solution.com')";

if (mysqli_query($conn, $sql)) {
    echo "✅ Admin user created successfully!<br>";
    echo "Username: admin<br>";
    echo "Password: Abcd#$123<br>";
    echo "<br><a href='backend/admin/login.php'>Go to Admin Login</a>";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>