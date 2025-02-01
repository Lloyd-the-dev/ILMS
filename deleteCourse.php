<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $courseId = $_POST["course_id"];

    // Delete course from database
    $sql = "DELETE FROM courses WHERE course_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $courseId);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Course deleted successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to delete course."]);
    }

    $stmt->close();
    $conn->close();
}
?>
