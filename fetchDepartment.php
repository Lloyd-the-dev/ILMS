<?php 
    include "config.php";


    $sql = "SELECT `department_name`, `faculty` FROM departments"; 
    $result = $conn->query($sql);

    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
?>