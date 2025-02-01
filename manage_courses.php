<?php
include "config.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["accType"] !== "Lecturer") {
    header("Location: index.html"); // Redirect unauthorized users
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
    <title>Manage Courses</title>

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

        .upload-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .upload-btn:hover {
            background-color: #0056b3;
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
            <li><a href="manage_courses.php" id="active">Manage Courses</a></li>
        </ul>
        <button class="login-btn"><a href="index.html">Logout</a></button>
    </nav>

    <div class="container mt-4">
        <h1 class="greeting text-center">Welcome, <?php echo $firstname; ?>!</h1>
        <h3 class="text-center mt-4 text-muted fw-bold">Courses You Created</h3>

        <div class="row mt-3" id="manageCoursesGrid"></div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- View Materials Modal -->
    <div class="modal fade" id="viewMaterialsModal" tabindex="-1" aria-labelledby="viewMaterialsLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewMaterialsLabel">Course Materials</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="view_course_id">
                    <div id="materialsList"></div>

                    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#uploadModal">Add New Material</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Material Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Course Material</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadForm" enctype="multipart/form-data">
                        <input type="hidden" id="upload_course_id" name="course_id">
                        <div class="mb-3">
                            <label class="form-label">Course Material (PDF, PPTX)</label>
                            <input type="file" class="form-control" id="course_material" name="course_material" accept=".pdf, .pptx" required>
                        </div>
                        <button type="submit" class="btn btn-success">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById("uploadForm").addEventListener("submit", function (event) {
            event.preventDefault();

            let formData = new FormData(this);

            fetch("uploadMaterial.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === "success") {
                    document.getElementById("uploadForm").reset();
                    let modal = document.getElementById("uploadModal");
                    let bootstrapModal = bootstrap.Modal.getInstance(modal);
                    bootstrapModal.hide();
                }
            })
            .catch(error => console.error("Error uploading material:", error));
        });
   
        document.addEventListener("DOMContentLoaded", function () {
            fetchLecturerCourses();
        });

        function fetchLecturerCourses() {
            fetch("fetchLecturerCourses.php")
                .then(response => response.json())
                .then(data => {
                    const manageCoursesGrid = document.getElementById("manageCoursesGrid");
                    manageCoursesGrid.innerHTML = "";

                    if (data.length === 0) {
                        manageCoursesGrid.innerHTML = "<p class='text-center text-muted'>You have not created any courses yet.</p>";
                        return;     
                    }

                    data.forEach(course => {
                        manageCoursesGrid.innerHTML += `
                            <div class="col-md-4 col-lg-3 mb-4">
                                <div class="card course-card">
                                    <img src="${course.course_img}" alt="${course.course_title}">
                                    <div class="card-body">
                                        <h5 class="card-title">${course.course_title}</h5>
                                        <button class="upload-btn" data-course-id="${course.course_id}" data-bs-toggle="modal" data-bs-target="#viewMaterialsModal">View Materials</button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    document.querySelectorAll(".upload-btn").forEach(button => {
                        button.addEventListener("click", function () {
                            const courseId = this.getAttribute("data-course-id");  // Get the course ID from the clicked button
                            document.getElementById("view_course_id").value = courseId; // Set it in the view modal
                            document.getElementById("upload_course_id").value = courseId; // Set the course_id in the upload modal
                            fetchCourseMaterials(courseId);  // Load the materials for that course
                        });
                    });
                })
                .catch(error => console.error("Error fetching courses:", error));
        }
        function fetchCourseMaterials(courseId) {
            fetch("fetchMaterials.php?course_id=" + courseId)
                .then(response => response.json())
                .then(data => {
                    const materialsList = document.getElementById("materialsList");
                    materialsList.innerHTML = "";

                    if (data.length === 0) {
                        materialsList.innerHTML = "<p class='text-muted'>No materials uploaded yet.</p>";
                        return;
                    }

                    data.forEach(material => {
                        materialsList.innerHTML += `
                            <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                                <a href="${material.file_path}" target="_blank">${material.file_name}</a>
                                <button class="btn btn-danger btn-sm" onclick="deleteMaterial(${material.material_id})">Delete</button>
                            </div>
                        `;
                    });
                })
                .catch(error => console.error("Error fetching materials:", error));
        }
        function deleteMaterial(materialId) {
            if (!confirm("Are you sure you want to delete this material?")) return;

            fetch("deleteMaterial.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `material_id=${materialId}`
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === "success") {
                    fetchCourseMaterials(document.getElementById("view_course_id").value);
                }
            })
            .catch(error => console.error("Error deleting material:", error));
        }
 

    </script>

</body>
</html>
