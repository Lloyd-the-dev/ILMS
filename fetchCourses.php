<?php
include "config.php";
session_start();

$userId = $_SESSION["user_id"];

$sql = "SELECT c.*, 
               (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.course_id AND e.user_id = ?) AS enrolled
        FROM courses c";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// ✅ Check if "enrolled" is being returned
header('Content-Type: application/json');
echo json_encode($data, JSON_PRETTY_PRINT);
?>
