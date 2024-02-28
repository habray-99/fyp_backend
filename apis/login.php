<?php
require '../database/Database.php';

try {
    // Get the database connection
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Check if the request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['message' => 'Invalid request method']);
        exit();
    }

    // Get the input from the request
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT * FROM member WHERE member_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check if the user exists and the password matches
    if ($user && password_verify($password, $user['member_password'])) {
        unset($user['member_password']);
        // Login successful
        http_response_code(200);
        echo json_encode(['message' => 'Login successful', 'user' => $user]);
    } else {
        // Login failed
        http_response_code(401);
        echo json_encode(['message' => 'Login failed']);
    }
} catch (Exception $e) {
    // Handle the exception
    http_response_code(500);
    echo json_encode(['message' => 'Server error', 'error' => $e->getMessage()]);
}
