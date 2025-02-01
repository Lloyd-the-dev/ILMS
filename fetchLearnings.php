<?php
include "config.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    echo json_encode([]);
    exit;
}

$userId = $_SESSION["user_id"];

$sql = "SELECT c.course_id, c.course_code, c.course_title, c.course_img 
        FROM enrollments e
        JOIN courses c ON e.course_id = c.course_id
        WHERE e.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
