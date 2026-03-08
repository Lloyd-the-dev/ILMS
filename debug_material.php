<?php
include "config.php";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get material ID from query parameter or use the one from the error
$materialId = isset($_GET['material_id']) ? intval($_GET['material_id']) : 15;

// Query the database
$stmt = $conn->prepare("SELECT * FROM course_materials WHERE material_id = ?");
$stmt->bind_param("i", $materialId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Material not found");
}

$material = $result->fetch_assoc();

// Check file paths
$relativePath = $material['file_path'];
$absolutePath = $material['absolute_path'] ?? null;
$computedAbsolutePath = __DIR__ . '/' . ltrim($relativePath, '/');

// Check if files exist
$relativeExists = file_exists($relativePath);
$absoluteExists = $absolutePath ? file_exists($absolutePath) : false;
$computedExists = file_exists($computedAbsolutePath);

// Output debug information
echo "<h1>Material Debug Information</h1>";
echo "<pre>";
echo "Material ID: " . $materialId . "\n";
echo "File Name: " . $material['file_name'] . "\n";
echo "Relative Path: " . $relativePath . " (" . ($relativeExists ? "Exists" : "Does not exist") . ")\n";
echo "Absolute Path: " . ($absolutePath ?? "Not set") . " (" . ($absoluteExists ? "Exists" : "Does not exist") . ")\n";
echo "Computed Absolute Path: " . $computedAbsolutePath . " (" . ($computedExists ? "Exists" : "Does not exist") . ")\n";
echo "\nDirectory Contents:\n";
echo "Current Directory: " . __DIR__ . "\n";
echo "Uploads Directory: " . __DIR__ . "/uploads/\n";

// List files in uploads directory
if (is_dir(__DIR__ . "/uploads/")) {
    echo "\nFiles in uploads directory:\n";
    $files = scandir(__DIR__ . "/uploads/");
    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            echo "- " . $file . "\n";
        }
    }
}

echo "</pre>";
?> 