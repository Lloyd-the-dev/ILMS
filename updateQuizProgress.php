<?php
session_start();
include "config.php";

// Set headers for JSON response
header('Content-Type: application/json');

// Debug: Log access to this file
error_log("updateQuizProgress.php accessed");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in', 'success' => false]);
    exit;
}

// Check if it's a direct browser access (not a POST request)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'This script requires a POST request', 'success' => false]);
    exit;
}

// Get the raw POST data
$inputJSON = file_get_contents('php://input');

// Debug: Log the raw input
error_log("Raw input received: " . $inputJSON);

// Try to decode the JSON
$input = json_decode($inputJSON, TRUE);

// Check if JSON is valid
if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON decode error: " . json_last_error_msg());
    echo json_encode(['error' => 'Invalid JSON data: ' . json_last_error_msg(), 'success' => false]);
    exit;
}

// Validate input
if (!isset($input['material_id']) || !isset($input['status'])) {
    echo json_encode(['error' => 'Missing required parameters', 'success' => false]);
    exit;
}

$userId = $_SESSION['user_id'];
$materialId = intval($input['material_id']);
$status = $input['status'];

// Debug: Log the processed values
error_log("Processing quiz update: User ID: $userId, Material ID: $materialId, Status: $status");

// Validate status value
if ($status !== 'passed' && $status !== 'failed') {
    echo json_encode(['error' => 'Invalid status value', 'success' => false]);
    exit;
}

try {
    // First check if a record already exists
    $checkSql = "SELECT * FROM quiz_progress WHERE user_id = ? AND material_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    
    if (!$checkStmt) {
        throw new Exception("Failed to prepare check statement: " . $conn->error);
    }
    
    $checkStmt->bind_param("ii", $userId, $materialId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update existing record
        $updateSql = "UPDATE quiz_progress SET status = ?, attempt_date = NOW() WHERE user_id = ? AND material_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        
        if (!$updateStmt) {
            throw new Exception("Failed to prepare update statement: " . $conn->error);
        }
        
        $updateStmt->bind_param("sii", $status, $userId, $materialId);
        $success = $updateStmt->execute();
        
        if (!$success) {
            throw new Exception("Failed to update quiz progress: " . $updateStmt->error);
        }
        
        error_log("Updated quiz progress for user $userId, material $materialId to $status");
    } else {
        // Insert new record
        $insertSql = "INSERT INTO quiz_progress (user_id, material_id, status, attempt_date) VALUES (?, ?, ?, NOW())";
        $insertStmt = $conn->prepare($insertSql);
        
        if (!$insertStmt) {
            throw new Exception("Failed to prepare insert statement: " . $conn->error);
        }
        
        $insertStmt->bind_param("iis", $userId, $materialId, $status);
        $success = $insertStmt->execute();
        
        if (!$success) {
            throw new Exception("Failed to insert quiz progress: " . $insertStmt->error);
        }
        
        error_log("Inserted new quiz progress for user $userId, material $materialId with status $status");
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Quiz progress updated successfully',
        'data' => [
            'user_id' => $userId,
            'material_id' => $materialId,
            'status' => $status
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Error in updateQuizProgress.php: " . $e->getMessage());
    echo json_encode([
        'error' => $e->getMessage(),
        'success' => false
    ]);
}
?>