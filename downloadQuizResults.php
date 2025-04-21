<?php
include "config.php";
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in and is a lecturer
if (!isset($_SESSION["user_id"]) || $_SESSION["accType"] !== "Lecturer") {
    header("Location: index.html");
    exit;
}

if (!isset($_GET['material_id'])) {
    die("Material ID not provided");
}

// Clear any previous output
ob_clean();

$materialId = intval($_GET['material_id']); // Ensure it's an integer

// Log the material ID and type
error_log("Fetching quiz results for material ID: " . $materialId . " (type: " . gettype($materialId) . ")");

try {
    // Fetch quiz results using prepared statement
    $query = $conn->prepare("
        SELECT u.firstname, 
               u.lastname, 
               qp.status, 
               qp.attempt_date
        FROM quiz_progress qp
        JOIN users u ON qp.user_id = u.user_id
        WHERE qp.material_id = ?
        ORDER BY qp.attempt_date DESC");
            
    if (!$query) {
        throw new Exception("Failed to prepare quiz results query: " . $conn->error);
    }

    $query->bind_param("i", $materialId);
    if (!$query->execute()) {
        throw new Exception("Failed to execute quiz results query: " . $query->error);
    }

    $result = $query->get_result();
    
    // Log the number of rows found
    $rowCount = $result->num_rows;
    error_log("Found " . $rowCount . " quiz results");

    if ($rowCount == 0) {
        throw new Exception("No quiz results found for this material");
    }

    // Set headers for CSV download
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename="quiz_results_' . $materialId . '_' . date('Y-m-d') . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Create a temporary file
    $temp = tmpfile();
    if ($temp === false) {
        throw new Exception("Failed to create temporary file");
    }
    
    // Write UTF-8 BOM
    fwrite($temp, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Write CSV headers
    fputcsv($temp, ['Student Name', 'Status', 'Attempt Date']);
    
    // Write data rows
    while ($row = $result->fetch_assoc()) {
        $studentName = trim($row['firstname'] . ' ' . $row['lastname']);
        $status = trim($row['status']);
        $attemptDate = trim($row['attempt_date']);
        
        fputcsv($temp, [
            $studentName,
            $status,
            $attemptDate
        ]);
    }
    
    // Reset file pointer to beginning
    fseek($temp, 0);
    
    // Output the file contents
    fpassthru($temp);
    
    // Clean up
    fclose($temp);
    $query->close();
    $conn->close();
    
} catch (Exception $e) {
    error_log("Error in downloadQuizResults.php: " . $e->getMessage());
    header("HTTP/1.1 500 Internal Server Error");
    echo "Error: " . $e->getMessage();
}
exit;
?> 