<?php
session_start();
include "config.php"; // Include the database configuration file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate the request data
if (!$data || !isset($data['material_id']) || !isset($data['status'])) {
    echo json_encode(['error' => 'Invalid request data']);
    exit;
}

$userId = $_SESSION['user_id'];
$materialId = $data['material_id'];
$status = $data['status']; // 'passed' or 'failed'

try {
    // Prepare the SQL query
    $sql = "
        INSERT INTO quiz_progress (user_id, material_id, status) 
        VALUES (?, ?, ?) 
        ON DUPLICATE KEY UPDATE status = VALUES(status)
    ";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("Failed to prepare SQL statement: " . $conn->error);
    }

    // Bind parameters and execute the query
    $stmt->bind_param("iis", $userId, $materialId, $status);
    $stmt->execute();

    // Check if the query was successful
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'No rows affected']);
    }
} catch (Exception $e) {
    // Log the error and return a JSON error response
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>