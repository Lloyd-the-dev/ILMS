<?php
session_start();
include "config.php";

header('Content-Type: application/json');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// Validate material_id
$materialId = isset($_GET['material_id']) ? intval($_GET['material_id']) : 0;
if ($materialId <= 0) {
    echo json_encode(['error' => 'Invalid material ID']);
    exit;
}

try {
    // Fetch material including quiz_type (defaults handled in PHP if column missing)
    $materialSql = "SELECT course_id, 
                           material_id, 
                           file_name, 
                           file_path, 
                           upload_date" .
                           (columnExists($conn, 'course_materials', 'quiz_type') ? ", quiz_type" : "") . "
                    FROM course_materials 
                    WHERE material_id = ?";

    $stmt = $conn->prepare($materialSql);
    if (!$stmt) {
        throw new Exception("Failed to prepare material query: " . $conn->error);
    }

    $stmt->bind_param("i", $materialId);
    $stmt->execute();
    $materialResult = $stmt->get_result();

    if ($materialResult->num_rows === 0) {
        echo json_encode(['error' => 'Material not found']);
        exit;
    }

    $material = $materialResult->fetch_assoc();

    // Determine quiz type (fallback to 'ai' if column not present or null)
    $quizType = isset($material['quiz_type']) && !empty($material['quiz_type']) ? $material['quiz_type'] : 'ai';

    $response = [
        'material_id' => $material['material_id'],
        'course_id'   => $material['course_id'],
        'file_name'   => $material['file_name'],
        'file_path'   => $material['file_path'],
        'quiz_type'   => $quizType,
        'questions'   => []
    ];

    // If quiz type is custom, load lecturer-authored questions
    if ($quizType === 'custom' && tableExists($conn, 'material_quiz_questions')) {
        $qSql = "SELECT question_id,
                        question_text,
                        option_a,
                        option_b,
                        option_c,
                        option_d,
                        correct_option
                 FROM material_quiz_questions
                 WHERE material_id = ?
                 ORDER BY question_id ASC";
        $qStmt = $conn->prepare($qSql);
        if ($qStmt) {
            $qStmt->bind_param("i", $materialId);
            $qStmt->execute();
            $qResult = $qStmt->get_result();
            while ($row = $qResult->fetch_assoc()) {
                $response['questions'][] = $row;
            }
        }
    }

    echo json_encode($response);
} catch (Exception $e) {
    error_log("Error in getMaterialQuiz.php: " . $e->getMessage());
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}

/**
 * Helper: check if a table exists in the current database
 */
function tableExists($conn, $tableName) {
    $result = $conn->query("SHOW TABLES LIKE '" . $conn->real_escape_string($tableName) . "'");
    return $result && $result->num_rows > 0;
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

