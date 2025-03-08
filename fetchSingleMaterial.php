<?php
// fetchSingleMaterial.php
include "config.php";

if (isset($_GET['material_id'])) {
    $materialId = $_GET['material_id'];
    $query = "SELECT * FROM course_materials WHERE material_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $materialId);
    $stmt->execute();
    $result = $stmt->get_result();
    $material = $result->fetch_assoc();

    echo json_encode($material);
}
?>
