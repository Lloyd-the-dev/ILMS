<?php
include "config.php";
session_start();
$userId = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $courseCode = $_POST["course_code"];
    $courseTitle = $_POST["course_title"];
    $department = $_POST["department"];
    $level = $_POST["level"];
    
    // Handle the image upload
    $targetDir = "uploads/"; 
    $targetFile = $targetDir . basename($_FILES["course_img"]["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is valid
    $check = getimagesize($_FILES["course_img"]["tmp_name"]);
    if ($check === false) {
        echo json_encode(["status" => "error", "message" => "Uploaded file is not an image."]);
        exit;
    }

    // Move the uploaded file to the target directory
    if (!move_uploaded_file($_FILES["course_img"]["tmp_name"], $targetFile)) {
        echo json_encode(["status" => "error", "message" => "Failed to upload image."]);
        exit;
    }

    // Insert course details into the database
    error_log($sql = "INSERT INTO courses (lecturer_id, course_code, course_title, course_img, department, level) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssi", $userId, $courseCode, $courseTitle, $targetFile, $department, $level);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Course added successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to add course."]);
    }

    $stmt->close();
    $conn->close();
}
?>
