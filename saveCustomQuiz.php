<?php
session_start();
include "config.php";

header('Content-Type: application/json');

// Only lecturers can create/edit custom quizzes
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
$questions  = isset($input['questions']) && is_array($input['questions']) ? $input['questions'] : [];

if ($materialId <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid material ID"]);
    exit;
}

if (empty($questions)) {
    echo json_encode(["success" => false, "message" => "No questions provided"]);
    exit;
}

try {
    // Ensure table for storing questions exists
    if (!tableExists($conn, 'material_quiz_questions')) {
        throw new Exception("Table 'material_quiz_questions' does not exist. Please run the provided SQL migration.");
    }

    // Start transaction
    $conn->begin_transaction();

    // Remove existing questions for this material
    $deleteSql = "DELETE FROM material_quiz_questions WHERE material_id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    if (!$deleteStmt) {
        throw new Exception("Failed to prepare delete statement: " . $conn->error);
    }
    $deleteStmt->bind_param("i", $materialId);
    $deleteStmt->execute();

    // Insert new questions
    $insertSql = "INSERT INTO material_quiz_questions 
        (material_id, question_text, option_a, option_b, option_c, option_d, correct_option) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    if (!$insertStmt) {
        throw new Exception("Failed to prepare insert statement: " . $conn->error);
    }

    foreach ($questions as $q) {
        $questionText   = isset($q['question_text']) ? trim($q['question_text']) : '';
        $optionA        = isset($q['option_a']) ? trim($q['option_a']) : '';
        $optionB        = isset($q['option_b']) ? trim($q['option_b']) : '';
        $optionC        = isset($q['option_c']) ? trim($q['option_c']) : '';
        $optionD        = isset($q['option_d']) ? trim($q['option_d']) : '';
        $correctOption  = isset($q['correct_option']) ? strtoupper(trim($q['correct_option'])) : '';

        if ($questionText === '' || $optionA === '' || $optionB === '' || 
            $optionC === '' || $optionD === '' || 
            !in_array($correctOption, ['A', 'B', 'C', 'D'], true)
        ) {
            throw new Exception("All questions must have text, four options and a valid correct option (A, B, C, or D).");
        }

        $insertStmt->bind_param(
            "issssss",
            $materialId,
            $questionText,
            $optionA,
            $optionB,
            $optionC,
            $optionD,
            $correctOption
        );

        if (!$insertStmt->execute()) {
            throw new Exception("Failed to insert quiz question: " . $insertStmt->error);
        }
    }

    // Set quiz_type to 'custom' for this material (if column exists)
    if (columnExists($conn, 'course_materials', 'quiz_type')) {
        $updateMaterialSql = "UPDATE course_materials SET quiz_type = 'custom' WHERE material_id = ?";
        $updateStmt = $conn->prepare($updateMaterialSql);
        if ($updateStmt) {
            $updateStmt->bind_param("i", $materialId);
            $updateStmt->execute();
        }
    }

    $conn->commit();

    echo json_encode([
        "success" => true,
        "message" => "Custom quiz saved successfully"
    ]);
} catch (Exception $e) {
    $conn->rollback();
    error_log("Error in saveCustomQuiz.php: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "Error saving quiz: " . $e->getMessage()
    ]);
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

