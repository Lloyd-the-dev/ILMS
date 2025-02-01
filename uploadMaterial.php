<?php
include "config.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $courseId = $_POST["course_id"];

    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["course_material"]["name"]);

    if (move_uploaded_file($_FILES["course_material"]["tmp_name"], $targetFile)) {
        // Insert into database
        $sql = "INSERT INTO course_materials (course_id, file_name, file_path) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $courseId, $_FILES["course_material"]["name"], $targetFile);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Material uploaded successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to save material in database."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "File upload failed."]);
    }
}
?>
