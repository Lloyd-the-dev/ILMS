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
        .list-group-item {
            margin-bottom: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .list-group-item strong {
            font-weight: 600;
        }
        .material-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            width: 100%; 
        }

        .material-item a {
            flex: 1; 
            margin-right: 10px; 
            white-space: nowrap; 
            overflow: hidden; 
            text-overflow: ellipsis; 
        }

        .material-item .btn {
            white-space: nowrap; /* Prevent button text from wrapping */
        }
    </style>
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
            <li><a href="about.php">About us</a></li>
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
    <!-- Quiz Results Modal -->
    <div class="modal fade" id="quizResultsModal" tabindex="-1" aria-labelledby="quizResultsLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quizResultsLabel">Quiz Results</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="quizResultsContent"></div>
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

        function viewQuizResults(materialId) {
            fetch(`fetchQuizResults.php?material_id=${materialId}`)
                .then(response => response.json())
                .then(data => {
                    const quizResultsContent = document.getElementById("quizResultsContent");
                    quizResultsContent.innerHTML = "";

                    if (data.length === 0) {
                        quizResultsContent.innerHTML = "<p class='text-muted'>No quiz results available for this material.</p>";
                        return;
                    }

                    // Display the number of students who passed and failed
                    const passedCount = data.filter(result => result.status === "passed").length;
                    const failedCount = data.filter(result => result.status === "failed").length;

                    quizResultsContent.innerHTML = `
                        <p><strong>Passed:</strong> ${passedCount}</p>
                        <p><strong>Failed:</strong> ${failedCount}</p>
                        <hr>
                        <h6>Detailed Results:</h6>
                        <ul class="list-group">
                            ${data.map(result => `
                                <li class="list-group-item">
                                    <strong>Student:</strong> ${result.firstname} ${result.lastname}<br>
                                    <strong>Status:</strong> ${result.status}<br>
                                    <strong>Attempt Date:</strong> ${result.attempt_date}
                                </li>
                            `).join("")}
                        </ul>
                    `;

                    // Show the quiz results modal
                    const quizResultsModal = new bootstrap.Modal(document.getElementById('quizResultsModal'));
                    quizResultsModal.show();
                })
                .catch(error => console.error("Error fetching quiz results:", error));
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
                            <div class="material-item">
                                <a href="${material.file_path}" target="_blank" title="${material.file_name}">${material.file_name}</a>
                                <div>
                                    <button class="btn btn-info btn-sm me-2" onclick="viewQuizResults(${material.material_id})">View Quiz Results</button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteMaterial(${material.material_id})">Delete</button>
                                </div>
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
