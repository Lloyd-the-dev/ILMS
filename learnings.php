<?php
include "config.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.html"); // Redirect if not logged in
    exit;
}

$userId = $_SESSION["user_id"];
$firstname = $_SESSION["firstname"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LearnSphere - My Learnings</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="./css/navbar.css">
    <link rel="stylesheet" href="./css/dashboard.css">
    <script src="./js/index.js" defer></script>

    <style>
        .course-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s ease-in-out;
        }

        .course-card:hover {
            transform: scale(1.05);
        }

        .course-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .course-card .card-body {
            text-align: center;
        }
    </style>
</head>
<body>

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
            <li><a href="edit_profile.php">Profile</a></li>
            <li><a href="learnings.php" id="active">Learnings</a></li>
        </ul>
        <button class="login-btn"><a href="index.html">Logout</a></button>
    </nav>

    <div class="container mt-4">
        <h1 class="greeting text-center">Welcome, <?php echo $firstname; ?>!</h1>
        <h3 class="text-center mt-4 text-muted fw-bold">My Enrolled Courses</h3>

        <div class="row mt-3" id="learningsGrid"></div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            fetchLearnings();
        });

        function fetchLearnings() {
            fetch("fetchLearnings.php")
                .then(response => response.json())
                .then(data => {
                    const learningsGrid = document.getElementById("learningsGrid");
                    learningsGrid.innerHTML = "";

                    if (data.length === 0) {
                        learningsGrid.innerHTML = "<p class='text-center text-muted'>You have not enrolled in any courses yet.</p>";
                        return;
                    }

                    data.forEach(course => {
                        learningsGrid.innerHTML += `
                            <div class="col-md-4 col-lg-3 mb-4">
                                <div class="card course-card">
                                    <img src="${course.course_img}" alt="${course.course_title}">
                                    <div class="card-body">
                                        <h5 class="card-title">${course.course_title}</h5>
                                        <button class="btn btn-primary">Access Course</button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                })
                .catch(error => console.error("Error fetching learnings:", error));
        }
    </script>
</body>
</html>
