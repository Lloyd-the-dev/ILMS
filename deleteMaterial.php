<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $materialId = $_POST["material_id"];

    $sql = "DELETE FROM course_materials WHERE material_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $materialId);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Material deleted successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to delete material."]);
    }
}
?>
