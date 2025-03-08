<?php 
   include "config.php";
   session_start();
   $userId = $_SESSION["user_id"];
   $firstname = $_SESSION["firstname"];
   $accType = $_SESSION["accType"];
   $department = $_SESSION["department"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google" content="notranslate">
    <title>LearnSphere | About</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,0,0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

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
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="courses.php">Courses</a></li>
            <li><a href="about.php" id="active">About us</a></li>
            <li><a href="edit_profile.php">Profile</a></li>
            <?php if ($accType == "Student") {?>
                <li><a href="learnings.php">Learnings</a></li>
            <?php }else { ?>
                <li><a href="manage_courses.php">My Courses</a></li>
            <?php } ?>
        </ul>
        <button class="login-btn"><a href="index.html">Logout</a></button>
    </nav>

    <div class="container mt-5">
        <!-- Intro Content -->
        <div class="row">
            <div class="col-lg-6">
                <img class="img-fluid rounded mb-4" src="./img/books.jpg" alt="Office Image">
            </div>
            <div class="col-lg-6">
                <h2>About LearnSphere</h2>
                <p style="font-size: 1.1em; text-align: justify;">LearnSphere is an innovative <strong>Intelligent Learning Management System (ILMS)</strong> designed and developed by <strong>Favour Oreoluwa Ojo</strong> in 2025. This cutting-edge platform revolutionizes the way students engage with educational content by leveraging <strong>Artificial Intelligence (AI)</strong> to deliver personalized, adaptive, and dynamic learning experiences.</p>
                <p style="font-size: 1.1em; text-align: justify;"> LearnSphere is not just another Learning Management System (LMS); it is a transformative tool that bridges the gap between traditional education and modern technology, ensuring that every student achieves their full potential.</p>
                <p style="font-size: 1.1em; text-align: justify;">LearnSphere is more than just a platform; it is a movement toward a smarter, more inclusive, and more effective way of learning. Whether you're a student looking to master new skills or an educator seeking to enhance your teaching methods, LearnSphere is here to transform your educational journey. Welcome to the future of learning. Welcome to <strong>LearnSphere !</strong></p></div></div>

        <!-- Team Members -->
        <h2>Our Team</h2>
        <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card h-100 text-center">
            <img class="card-img-top" src="./img/books.jpg" alt="John Doe">
            <div class="card-body">
                <h4 class="card-title">John Doe</h4>
                <h6 class="card-subtitle mb-2 text-muted">Managing Director</h6>
                <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Possimus aut mollitia eum ipsum fugiat odio officiis odit.</p>
            </div>
            <div class="card-footer">
                <a href="mailto:Johndoe@gmail.com">Johndoe@gmail.com</a>
            </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card h-100 text-center">
            <img class="card-img-top" src="./img/books.jpg" alt="Ethan Anderson">
            <div class="card-body">
                <h4 class="card-title">Ethan Anderson</h4>
                <h6 class="card-subtitle mb-2 text-muted">Human Resources</h6>
                <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Possimus aut mollitia eum ipsum fugiat odio officiis odit.</p>
            </div>
            <div class="card-footer">
                <a href="mailto:ethan@gmail.com">ethan@gmail.com</a>
            </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card h-100 text-center">
            <img class="card-img-top" src="./img/books.jpg" alt="Emily Davidson">
            <div class="card-body">
                <h4 class="card-title">Emily Davidson</h4>
                <h6 class="card-subtitle mb-2 text-muted">Financial Secretary</h6>
                <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Possimus aut mollitia eum ipsum fugiat odio officiis odit.</p>
            </div>
            <div class="card-footer">
                <a href="mailto:emilyDev@gmail.com">emilyDev@gmail.com</a>
            </div>
            </div>
        </div>
        </div>

        <!-- Our Customers -->
        <h2>Our Customers</h2>
        <div class="row">
        <div class="col-lg-2 col-sm-4 mb-4">
            <img class="img-fluid" src="./img/education 1.jpg" alt="Customer 1">
        </div>
        <div class="col-lg-2 col-sm-4 mb-4">
            <img class="img-fluid" src="./img/education 1.jpg" alt="Customer 2">
        </div>
        <div class="col-lg-2 col-sm-4 mb-4">
            <img class="img-fluid" src="./img/education 1.jpg" alt="Customer 3">
        </div>
        <div class="col-lg-2 col-sm-4 mb-4">
            <img class="img-fluid" src="./img/education 1.jpg" alt="Customer 4">
        </div>
        <div class="col-lg-2 col-sm-4 mb-4">
            <img class="img-fluid" src="./img/education 1.jpg" alt="Customer 5">
        </div>
        <div class="col-lg-2 col-sm-4 mb-4">
            <img class="img-fluid" src="./img/education 1.jpg" alt="Customer 6">
        </div>
        </div>
    </div>
 <!-- Footer -->
  <footer class="py-5 bg-dark">
    <div class="container">
    <p class="m-0 text-center text-white"><strong>Copyright &copy; Created by Favour Oreoluwa Ojo | 2025
      Empowering Education Through AI.</strong></p>
    </div>
  </footer>



 
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
</body>

</html>