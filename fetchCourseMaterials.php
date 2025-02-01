<?php
include "config.php";

if (isset($_GET['course_id'])) {
    $courseId = $_GET['course_id'];
    $query = "SELECT * FROM course_materials WHERE course_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $courseId);
    $stmt->execute();
    $result = $stmt->get_result();
    $materials = [];

    while ($row = $result->fetch_assoc()) {
        $materials[] = $row;
    }

    echo json_encode($materials);
}
?>
