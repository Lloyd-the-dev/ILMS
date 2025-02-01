<?php
include "config.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["accType"] !== "Lecturer") {
    echo json_encode([]);
    exit;
}

$userId = $_SESSION["user_id"];

$sql = "SELECT * FROM courses WHERE lecturer_id = ?";
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
