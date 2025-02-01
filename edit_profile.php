<?php
include "config.php"; 

session_start();
$userId = $_SESSION["user_id"];
$firstLogin = $_SESSION["first_login"];
$accType = $_SESSION["accType"];

if($firstLogin == 1){
    echo '<script type ="text/JavaScript">'; 
    echo 'alert("On first Login you should update your profile details")';
    echo '</script>';

     // Reset firstLogin to 0 so the message doesn't appear again
     $_SESSION["first_login"] = 0;

     // Update the database to reflect that the user is no longer on their first login
     $updateLoginStatus = "UPDATE users SET first_login = '0' WHERE user_id = '$userId'";
     if ($conn->query($updateLoginStatus) !== TRUE) {
         echo "Error updating login status: " . $conn->error;
     }
}

$sql = "SELECT * FROM users WHERE user_id = '$userId'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $Firstname = $row['firstname'];
    $Lastname = $row['lastname'];
    $Fullname = $row['firstname']. " " . $row['lastname'];
    $Email = $row['email'];
    $matricNo = $row['matricNo'];

} else {
    echo "User not found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $Fullname; ?>'s Profile</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/navbar.css">
    <link rel="stylesheet" href="./css/edit_profile.css">
    <script src="./js/index.js" defer></script>

    <style>
        .container {
            margin-top: 200px !important; /* Creates space below the navbar */
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

    <header>
         <nav class="navbar">
            <span class="hamburger-btn material-symbols-rounded">menu</span>
            <a href="#" class="logo">
               <h1>🚀</h1>
               <h2>LearnSphere</h2>
            </a>
            <ul class="links">
               <span class="close-btn material-symbols-rounded">close</span>
               <li><a href="dashboard.php">Home</a></li>
               <li><a href="courses.php">Courses</a></li>
               <li><a href="#">About us</a></li>
               <li><a href="edit_profile.php" id="active">Profile</a></li>
               <li><a href="learnings.php">Learnings</a></li>
            </ul>
            <button class="login-btn"><a href="index.html">Logout</a></button>
         </nav>
    </header>


    <div class="container rounded bg-white mt-5 p-4">
        <div class="row">
            <div class="col-md-4 border-right">
                <div class="d-flex flex-column align-items-center text-center p-3 py-5"><img class="rounded-circle mt-5" src="./img/user-profile.webp" width="90"><span class="font-weight-bold"><?php echo $Fullname; ?></span><span class="text-black-50"><?php echo $Email; ?></span></div>
            </div>
            <div class="col-md-8">
                <div class="p-3 py-5">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-right">Edit Profile</h6>
                    </div>
                    <form action="edit_profile.php" method="POST" onsubmit="return validatePasswords()">
                        <div class="row mt-2">
                            <div class="col-md-6"><input type="text" class="form-control" placeholder="first name" value="<?php echo $Firstname; ?>" name="first_name"></div>
                            <div class="col-md-6"><input type="text" class="form-control" value="<?php echo $Lastname; ?>" placeholder="Lastname" name="last_name"></div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6"><input type="text" class="form-control" placeholder="Email" value="<?php echo $Email; ?>" name="email"></div>
                            <?php if($accType == "Student") {?>
                            <div class="col-md-6"><input type="text" class="form-control" placeholder="matric number" value="<?php echo $matricNo; ?>" name="matricNo"></div>
                            <?php } ?>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <input type="password" id="newPassword" class="form-control" placeholder="New Password" name="new_password">
                            </div>
                            <div class="col-md-6">
                                <input type="password" id="verifyPassword" class="form-control" placeholder="Verify Password" name="verify_password">
                            </div>                        
                        </div>
                        </div>
                        <div class="mt-5 text-right"><button class="btn btn-primary profile-button" type="submit" name="submit">Save Profile</button></div>
                        </div>
                    </form>
                    
                    
            </div>
    </div>

    <script>
       function validatePasswords() {
            const newPassword = document.getElementById('newPassword').value;
            const verifyPassword = document.getElementById('verifyPassword').value;

            if (newPassword !== verifyPassword) {
                alert('Passwords do not match. Please try again.');
                return false;
            }
            return true;
        }


    </script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>
</html>

<?php
    if (isset($_POST['submit'])) {
        $userId = $_SESSION['user_id'];
        $firstName = $_POST['first_name'];
        $lastName = $_POST['last_name'];
        $email = $_POST['email'];
        $matricNo = $_POST['matricNo'];
        $newPassword = $_POST['new_password'];
    
        if (!empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET firstname = ?, lastname = ?, email = ?, password = ?, matricNo = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssi", $firstName, $lastName, $email, $hashedPassword, $matricNo, $userId);
        } else {
            $sql = "UPDATE users SET firstname = ?, lastname = ?, email = ?, matricNo = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $firstName, $lastName, $email, $matricNo, $userId);
        }
    
        if ($stmt->execute()) {
            echo '<script type ="text/JavaScript">'; 
            echo 'alert("Profile Updated successfully");';
            echo 'window.location.href = "edit_profile.php";'; 
            echo '</script>';  
        } else {
            echo '<script type ="text/JavaScript">'; 
            echo 'alert("error updating");';
            echo 'window.location.href = "edit_profile.php";'; 
            echo '</script>'; 
        }
    
        $stmt->close();
        $conn->close();
    }


?>