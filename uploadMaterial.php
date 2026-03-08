<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers to ensure JSON response
header('Content-Type: application/json');

include "config.php";
session_start();

// Check if user is logged in and is a lecturer
if (!isset($_SESSION["user_id"]) || $_SESSION["accType"] !== "Lecturer") {
    echo json_encode(["status" => "error", "message" => "Unauthorized access"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Check if course_id is provided
        if (!isset($_POST["course_id"])) {
            throw new Exception("Course ID is required");
        }
        
        $courseId = $_POST["course_id"];

        // Check if file was uploaded
        if (!isset($_FILES["course_material"]) || $_FILES["course_material"]["error"] !== UPLOAD_ERR_OK) {
            $error = isset($_FILES["course_material"]) ? $_FILES["course_material"]["error"] : "No file uploaded";
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive in php.ini",
                UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form",
                UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded",
                UPLOAD_ERR_NO_FILE => "No file was uploaded",
                UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder",
                UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk",
                UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload"
            ];
            throw new Exception("File upload error: " . ($errorMessages[$error] ?? "Unknown error"));
        }

        // Create uploads directory if it doesn't exist
        $targetDir = __DIR__ . "/uploads/";
        if (!file_exists($targetDir)) {
            if (!mkdir($targetDir, 0777, true)) {
                throw new Exception("Failed to create uploads directory");
            }
        }

        // Check if directory is writable
        if (!is_writable($targetDir)) {
            throw new Exception("Uploads directory is not writable");
        }

        $fileName = basename($_FILES["course_material"]["name"]);
        $targetFile = $targetDir . $fileName;
        $relativePath = "uploads/" . $fileName;

        // Check file type
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['pdf', 'pptx'];
        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception("Only PDF and PPTX files are allowed");
        }

        // Check file size (10MB max)
        if ($_FILES["course_material"]["size"] > 10 * 1024 * 1024) {
            throw new Exception("File is too large. Maximum size is 10MB");
        }

        // Try to move the uploaded file
        if (!move_uploaded_file($_FILES["course_material"]["tmp_name"], $targetFile)) {
            $error = error_get_last();
            throw new Exception("Failed to move uploaded file: " . ($error ? $error['message'] : "Unknown error"));
        }

        // Insert into database - updated to match actual table structure
        $sql = "INSERT INTO course_materials (course_id, file_name, file_path, upload_date) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Database prepare error: " . $conn->error);
        }

        $stmt->bind_param("iss", $courseId, $fileName, $relativePath);

        if (!$stmt->execute()) {
            // If database insert fails, delete the uploaded file
            unlink($targetFile);
            throw new Exception("Failed to save material in database: " . $stmt->error);
        }

        echo json_encode([
            "status" => "success", 
            "message" => "Material uploaded successfully!",
            "file_path" => $relativePath
        ]);

    } catch (Exception $e) {
        // Log the error
        error_log("Upload error: " . $e->getMessage());
        
        // Return error response
        echo json_encode([
            "status" => "error", 
            "message" => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "status" => "error", 
        "message" => "Invalid request method"
    ]);
}
?>
