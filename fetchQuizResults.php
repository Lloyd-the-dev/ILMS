<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["accType"] !== "Lecturer") {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

$materialId = $_GET["material_id"];

try {
    // Fetch quiz results for the material
    $stmt = $conn->prepare("
        SELECT u.firstname, u.lastname, qp.status, qp.attempt_date
        FROM quiz_progress qp
        JOIN users u ON qp.user_id = u.user_id
        WHERE qp.material_id = ?
    ");
    $stmt->bind_param("i", $materialId);
    $stmt->execute();
    $result = $stmt->get_result();
    $quizResults = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode($quizResults);
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>