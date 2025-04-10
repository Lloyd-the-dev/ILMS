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
$materialId = isset($_GET['material_id']) ? intval($_GET['material_id']) : 0;

// Validate the material_id
if ($materialId <= 0) {
    echo json_encode(['error' => 'Invalid material ID']);
    exit;
}

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
    
    // Add debug logging
    error_log("Checking quiz progress for user $userId and material $materialId");
    
    if ($result->num_rows > 0) {
        $progress = $result->fetch_assoc();
        error_log("Found quiz progress: " . json_encode($progress));
        echo json_encode($progress);
    } else {
        error_log("No quiz progress found, returning 'not_attempted'");
        echo json_encode(['status' => 'not_attempted']);
    }
} catch (Exception $e) {
    // Log the error and return a JSON error response
    error_log("Database error in fetchQuizProgress.php: " . $e->getMessage());
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>