<?php
session_start();
include "config.php";

header('Content-Type: application/json');

// Only lecturers can change quiz type
if (!isset($_SESSION["user_id"]) || $_SESSION["accType"] !== "Lecturer") {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit;
}

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["success" => false, "message" => "Invalid JSON data: " . json_last_error_msg()]);
    exit;
}

$materialId = isset($input['material_id']) ? intval($input['material_id']) : 0;
$quizType   = isset($input['quiz_type']) ? strtolower(trim($input['quiz_type'])) : '';

if ($materialId <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid material ID"]);
    exit;
}

if (!in_array($quizType, ['ai', 'custom'], true)) {
    echo json_encode(["success" => false, "message" => "Invalid quiz type"]);
    exit;
}

try {
    if (!columnExists($conn, 'course_materials', 'quiz_type')) {
        throw new Exception("Column 'quiz_type' does not exist on table 'course_materials'. Please run the provided SQL migration.");
    }

    $sql = "UPDATE course_materials SET quiz_type = ? WHERE material_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare update statement: " . $conn->error);
    }

    $stmt->bind_param("si", $quizType, $materialId);
    if (!$stmt->execute()) {
        throw new Exception("Failed to update quiz type: " . $stmt->error);
    }

    echo json_encode(["success" => true, "message" => "Quiz type updated successfully"]);
} catch (Exception $e) {
    error_log("Error in setQuizType.php: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Error updating quiz type: " . $e->getMessage()]);
}

/**
 * Helper: check if a column exists in a table
 */
function columnExists($conn, $table, $column) {
    $table = $conn->real_escape_string($table);
    $column = $conn->real_escape_string($column);
    $sql = "SHOW COLUMNS FROM `$table` LIKE '$column'";
    $result = $conn->query($sql);
    return $result && $result->num_rows > 0;
}

?>

