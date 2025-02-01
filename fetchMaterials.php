<?php
include "config.php";

if (!isset($_GET["course_id"])) {
    echo json_encode([]);
    exit;
}

$courseId = $_GET["course_id"];

$sql = "SELECT * FROM course_materials WHERE course_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $courseId);
$stmt->execute();
$result = $stmt->get_result();

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
