<?php 

   include "config.php";
   session_start();
   $userId = $_SESSION["user_id"];
   $firstLogin = $_SESSION["first_login"];
   $firstname = $_SESSION["firstname"];
   $accType = $_SESSION["accType"];
   if($firstLogin == 1){
      header("location: edit_profile.php");
   }


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LearnSphere Dashboard</title>
    <!-- Google Fonts Link For Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,0,0">

    <link rel="stylesheet" href="./css/navbar.css">
    <link rel="stylesheet" href="./css/dashboard.css">
    <script src="./js/index.js" defer></script>
</head>
<body>

      <nav class="navbar">
         <span class="hamburger-btn material-symbols-rounded">menu</span>
         <a href="dashboard.php" class="logo">
            <h1>🚀</h1>
            <h2>LearnSphere</h2>
         </a>
         <ul class="links">
            <span class="close-btn material-symbols-rounded">close</span>
            <li><a href="dashboard.php" id="active">Home</a></li>
            <li><a href="courses.php">Courses</a></li>
            <li><a href="about.php">About us</a></li>
            <li><a href="edit_profile.php">Profile</a></li>
            <?php if ($accType == "Student") {?>
               <li><a href="learnings.php">Learnings</a></li>
            <?php }else { ?>
               <li><a href="manage_courses.php">My Courses</a></li>
            <?php } ?>
            
         </ul>
         <button class="login-btn"><a href="index.html">Logout</a></button>
      </nav>

      <h1 class="greeting">Welcome, <?php echo $firstname;?></h1>
</body>
</html>
