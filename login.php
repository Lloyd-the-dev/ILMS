<?php 

include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input from the form
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = $_POST["password"]; // Don't escape password, it will be used with password_verify

    // SQL query to find the user by email
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result) == 1) {
        // Fetch user data
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $hashedPassword = $row["password"]; // The hashed password from the database

        // Verify the entered password with the hashed password
        if(password_verify($password, $hashedPassword)) {
            // Password is correct, start a session and set session variables
            session_start();
            $_SESSION["user_id"] = $row["user_id"];
            $_SESSION["firstname"] = $row["firstname"];
            $_SESSION["mail"] = $row["email"];
            $_SESSION["accType"] = $row["accType"];
            $_SESSION["first_login"] = $row["first_login"];
                        

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();  // Make sure to stop further script execution
        } else {
            // Password doesn't match
            echo '<script type="text/JavaScript">';
            echo 'alert("password mismatch, try again!");';
            echo 'window.location.href = "index.html";';
            echo '</script>';
        }
    } else {
        // Email not found
        echo '<script type="text/JavaScript">';
        echo 'alert("Invalid credentials, try again!");';
        echo 'window.location.href = "index.html";';
        echo '</script>';
    }
    
    // Close the connection
    mysqli_close($conn);
}
?>
