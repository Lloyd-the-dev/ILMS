<?php
include "config.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION["user_id"])) {
        echo json_encode(["status" => "error", "message" => "User not logged in."]);
        exit;
    }

    $userId = $_SESSION["user_id"];
    $courseId = $_POST["course_id"];

    // Check if the student is already enrolled
    $checkQuery = "SELECT * FROM enrollments WHERE user_id = ? AND course_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ii", $userId, $courseId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "You are already enrolled in this course."]);
        exit;
    }

    // Insert enrollment into database
    $sql = "INSERT INTO enrollments (user_id, course_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $courseId);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Enrolled successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to enroll."]);
    }

    $stmt->close();
    $conn->close();
}
?>
