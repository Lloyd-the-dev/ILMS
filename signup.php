<?php 
include "config.php";
if (isset($_POST["submit"])) {

    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $accType = mysqli_real_escape_string($conn, $_POST["accType"]);
    $department = mysqli_real_escape_string($conn, $_POST["department"]);
    $firstLogin = 1;

    // Check if email exists
    $checkEmailQuery = "SELECT * FROM `users` WHERE `email` = '$email'";
    $result = mysqli_query($conn, $checkEmailQuery);

    if (mysqli_num_rows($result) > 0) {
        echo '<script>alert("Email is already registered. Please use a different email!"); window.location.href = "index.html";</script>';
        exit();
    } else {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $sql = "INSERT INTO users (email, password, accType, department, first_login) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $email, $hashedPassword, $accType, $department, $firstLogin);

        if ($stmt->execute()) {
            echo '<script>alert("Account successfully created"); window.location.href = "index.html";</script>';
            exit();
        } else {
            echo '<script>alert("Error creating account: ' . addslashes($stmt->error) . '"); window.location.href = "index.html";</script>';
            exit();
        }

        // Close resources
        $stmt->close();
        $conn->close();
    }
}
?>
