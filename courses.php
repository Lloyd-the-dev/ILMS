<?php 
   include "config.php";
   session_start();
   $userId = $_SESSION["user_id"];
   $firstname = $_SESSION["firstname"];
   $accType = $_SESSION["accType"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LearnSphere Courses</title>
    
    <!-- Google Fonts Link For Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,0,0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="./css/navbar.css">
    <link rel="stylesheet" href="./css/dashboard.css">
    <script src="./js/index.js" defer></script>

    <style>
        /* Card style for students */
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

        .enroll-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .enroll-btn:hover {
            background-color: #0056b3;
        }

        /* List view style for lecturers */
        .course-list {
            width: 100%;
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
        }

        .course-list .course-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            background: white;
            margin-bottom: 8px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .course-list .course-item img {
            width: 50px;
            height: 50px;
            border-radius: 5px;
            margin-right: 10px;
        }

        .course-list .actions {
            display: flex;
            gap: 10px;
        }

        .course-list .actions button {
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .edit-btn {
            background-color: #f4b400;
            color: white;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
        }

        .test-btn {
            background-color: #007bff;
            color: white;
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
            <li><a href="courses.php" id="active">Courses</a></li>
            <li><a href="#">About us</a></li>
            <li><a href="edit_profile.php">Profile</a></li>
            <?php if ($accType == "Student") {?>
               <li><a href="learnings.php">Learnings</a></li>
            <?php }else { ?>
               <li><a href="manage_courses.php">My Courses</a></li>
            <?php } ?>
        </ul>
        <button class="login-btn"><a href="index.html">Logout</a></button>
    </nav>

    <div class="container mt-4">
        <h1 class="greeting text-center">Welcome, <?php echo $firstname; ?>!</h1>
        <h3 class="text-center mt-4 text-muted fw-bold">Available Courses</h3>

        <?php if($accType == "Student") { ?>
            <!-- Student Grid View -->
            <div class="row mt-3" id="courseGrid"></div>
        <?php } else { ?>
            <!-- Lecturer List View -->
            <div class="course-list">
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addCourseModal">Add Course +</button>
                <div id="courseList"></div>
            </div>

             <!-- Add Course Modal -->
            <div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addCourseLabel">Add New Course</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addCourseForm" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label">Course Code</label>
                                    <input type="text" class="form-control" id="course_code" name="course_code" required style="text-transform: uppercase;">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Course Title</label>
                                    <input type="text" class="form-control" id="course_title" name="course_title" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Department</label>
                                    <select class="form-control" id="department" name="department" required></select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Course Image</label>
                                    <input type="file" class="form-control" id="course_img" name="course_img" accept="image/*" required>
                                </div>
                                <button type="submit" class="btn btn-success">Add Course</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Course Modal -->
            <div class="modal fade" id="editCourseModal" tabindex="-1" aria-labelledby="editCourseLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editCourseLabel">Edit Course</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editCourseForm">
                                <input type="hidden" id="edit_course_id" name="course_id">
                                <div class="mb-3">
                                    <label class="form-label">Course Code</label>
                                    <input type="text" class="form-control" id="edit_course_code" name="course_code" required style="text-transform: uppercase;">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Course Title</label>
                                    <input type="text" class="form-control" id="edit_course_title" name="course_title" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Department</label>
                                    <select class="form-control" id="edit_department" name="department" required></select>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Course</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        <?php } ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            fetchCourses();
            fetchDepartments();
        });

        // Fetch Courses & Render
        function fetchCourses() {
            fetch("fetchCourses.php")
                .then(response => response.json())
                .then(data => {
                    const accType = "<?php echo $accType; ?>";
                    if (accType === "Student") {
                        renderStudentCourses(data);
                    } else {
                        renderLecturerCourses(data);
                    }
                })
                .catch(error => console.error("Error fetching courses:", error));
        }

        // Render Courses for Students (Grid View)
        function renderStudentCourses(courses) {
            const courseGrid = document.getElementById("courseGrid");
            courseGrid.innerHTML = "";

            courses.forEach(course => {
                const courseCard = document.createElement("div");
                courseCard.classList.add("col-md-4", "col-lg-3", "mb-4");

                
                let isEnrolled = course.enrolled > 0; 

                let buttonHTML = isEnrolled
                    ? `<button class="btn btn-success" disabled>Enrolled ✅</button>`
                    : `<button class="enroll-btn btn btn-primary" data-course-id="${course.course_id}">Enroll Now</button>`;

                courseCard.innerHTML = `
                    <div class="card course-card">
                        <img src="${course.course_img}" alt="${course.course_title}">
                        <div class="card-body">
                            <h5 class="card-title">${course.course_title}</h5>
                            ${buttonHTML}
                        </div>
                    </div>
                `;

                courseGrid.appendChild(courseCard);
            });

            // Add event listeners to all enroll buttons
            document.querySelectorAll(".enroll-btn").forEach(button => {
                button.addEventListener("click", function () {
                    const courseId = this.getAttribute("data-course-id");
                    enrollCourse(courseId, this);
                });
            });
        }

        // Function to enroll in a course
        function enrollCourse(courseId, button) {
            button.disabled = true; // Disable button to prevent multiple clicks

            fetch("enrollCourse.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `course_id=${courseId}`
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === "success") {
                    button.classList.remove("enroll-btn", "btn-primary");
                    button.classList.add("btn-success");
                    button.textContent = "Enrolled ✅"; // Update button text after successful enrollment
                   button.disabled = true;
                } else {
                    button.disabled = false; // Re-enable button if error occurs
                }
            })
            .catch(error => {
                console.error("Error enrolling:", error);
                button.disabled = false;
            });
        }



    

       // Render Courses for Lecturers (List View)
       function renderLecturerCourses(courses) {
            const courseList = document.getElementById("courseList");
            courseList.innerHTML = "";
            courses.forEach(course => {
                const courseItem = document.createElement("div");
                courseItem.classList.add("course-item");
                courseItem.innerHTML = `
                    <div style="display: flex; align-items: center;">
                        <img src="${course.course_img}" alt="${course.course_title}">
                        <span>${course.course_title}</span>
                    </div>
                    <div class="actions">
                        <button class="delete-btn" data-course-id="${course.course_id}">🗑️</button>
                        <button class="edit-btn" data-course-id="${course.course_id}" 
                                data-course-code="${course.course_code}" 
                                data-course-title="${course.course_title}" 
                                data-department="${course.department}">✏️</button>
                        <button class="test-btn">Test</button>
                    </div>
                `;
                courseList.appendChild(courseItem);
            });

            // Attach delete event listener
            document.querySelectorAll(".delete-btn").forEach(button => {
                button.addEventListener("click", function () {
                    const courseId = this.getAttribute("data-course-id");
                    deleteCourse(courseId);
                });
            });

            // Attach edit event listener
            document.querySelectorAll(".edit-btn").forEach(button => {
                button.addEventListener("click", function () {
                    const courseId = this.getAttribute("data-course-id");
                    const courseCode = this.getAttribute("data-course-code");
                    const courseTitle = this.getAttribute("data-course-title");
                    const department = this.getAttribute("data-department");
                    openEditModal(courseId, courseCode, courseTitle, department);
                });
            });
        }

        // Delete Course Function
        function deleteCourse(courseId) {
            if (!confirm("Are you sure you want to delete this course?")) return;

            fetch("deleteCourse.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `course_id=${courseId}`
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === "success") {
                    fetchCourses();  // Refresh course list
                }
            })
            .catch(error => console.error("Error deleting course:", error));
        }

        // Open Edit Course Modal
        function openEditModal(courseId, courseCode, courseTitle, department) {
            document.getElementById("edit_course_id").value = courseId;
            document.getElementById("edit_course_code").value = courseCode;
            document.getElementById("edit_course_title").value = courseTitle;

            // Reuse fetchDepartments function for the edit modal
            fetchDepartments("edit_department", department);

            // Open the modal
            let modal = new bootstrap.Modal(document.getElementById("editCourseModal"));
            modal.show();
        }

        // Handle Edit Course Submission
        document.getElementById("editCourseForm").addEventListener("submit", function (event) {
            event.preventDefault();

            let formData = new FormData(this);

            fetch("editCourse.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === "success") {
                    fetchCourses();  // Refresh course list
                    let modal = document.getElementById("editCourseModal");
                    let bootstrapModal = bootstrap.Modal.getInstance(modal);
                    bootstrapModal.hide();
                }
            })
            .catch(error => console.error("Error editing course:", error));
        });

        // Fetch Departments and Populate Dropdown
        function fetchDepartments(dropdownId, selectedDepartment = null) {
            fetch('./fetchDepartment.php')
                .then(response => response.json())
                .then(data => {
                    const departmentDropdown = document.getElementById(dropdownId);
                    departmentDropdown.innerHTML = ''; // Clear existing options

                    data.forEach(dept => {
                        const option = document.createElement('option');
                        option.value = dept.department_name;
                        option.textContent = dept.department_name;

                        // Pre-select the current department if provided
                        if (selectedDepartment && dept.department_name === selectedDepartment) {
                            option.selected = true;
                        }

                        departmentDropdown.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching departments:', error));
        }



        // Handle Course Submission
        document.getElementById("addCourseForm").addEventListener("submit", function (event) {
            event.preventDefault();
            
            let formData = new FormData(this);
            
            fetch("addCourses.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === "success") {
                    fetchCourses();  // Reload courses dynamically
                    document.getElementById("addCourseForm").reset();
                    let modal = document.getElementById("addCourseModal"); 
                    let bootstrapModal = bootstrap.Modal.getInstance(modal);
                    bootstrapModal.hide();
                }
            })
            .catch(error => console.error("Error adding course:", error));
        });
    </script>
</body>
</html>
