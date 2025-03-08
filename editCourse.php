<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $courseId = $_POST["course_id"];
    $courseCode = $_POST["course_code"];
    $courseTitle = $_POST["course_title"];
    $department = $_POST["department"];
    $level = $_POST["level"];

    $sql = "UPDATE courses SET course_code = ?, course_title = ?, department = ?, level = ? WHERE course_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $courseCode, $courseTitle, $department, $level, $courseId);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Course updated successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update course."]);
    }

    $stmt->close();
    $conn->close();
}
?>
