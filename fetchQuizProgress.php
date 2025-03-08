<?php

session_start();
include "config.php"; // Include the database configuration file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// Get the material_id from the query parameters
$userId = $_SESSION['user_id'];
$materialId = $_GET['material_id'];

try {
    // Prepare the SQL query
    $sql = "SELECT status FROM quiz_progress WHERE user_id = ? AND material_id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("Failed to prepare SQL statement: " . $conn->error);
    }

    // Bind parameters and execute the query
    $stmt->bind_param("ii", $userId, $materialId);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->get_result();
    $progress = $result->fetch_assoc();

    // Return the quiz progress or a default status
    echo json_encode($progress ? $progress : ['status' => 'not_attempted']);
} catch (Exception $e) {
    // Log the error and return a JSON error response
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>