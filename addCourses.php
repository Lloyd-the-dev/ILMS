<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $courseCode = $_POST["course_code"];
    $courseTitle = $_POST["course_title"];
    $department = $_POST["department"];
    
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
    $sql = "INSERT INTO courses (course_code, course_title, course_img, department) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $courseCode, $courseTitle, $targetFile, $department);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Course added successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to add course."]);
    }

    $stmt->close();
    $conn->close();
}
?>
