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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quizResultsLabel">Quiz Results</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6>Quiz Results</h6>
                        <button class="btn btn-success" onclick="downloadQuizResults()">
                            <i class="bi bi-download"></i> Download Results as CSV
                        </button>
                    </div>
                    <div id="quizResultsContent"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage Quiz Modal -->
    <div class="modal fade" id="quizEditorModal" tabindex="-1" aria-labelledby="quizEditorLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quizEditorLabel">Manage Quiz for Material</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="quizEditorForm">
                        <input type="hidden" id="quiz_material_id" name="material_id">
                        <div class="mb-3">
                            <label class="form-label d-block">Quiz Type</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="quiz_type" id="quiz_type_ai" value="ai" checked>
                                <label class="form-check-label" for="quiz_type_ai">Use AI-generated quiz</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="quiz_type" id="quiz_type_custom" value="custom">
                                <label class="form-check-label" for="quiz_type_custom">Use custom quiz (lecturer-defined)</label>
                            </div>
                            <div class="form-text">
                                Select whether students should see the AI-generated quiz or your own custom questions for this material.
                            </div>
                        </div>

                        <div id="customQuizSection">
                            <h6>Custom Quiz Questions</h6>
                            <p class="text-muted">Add up to 5 multiple-choice questions (A–D). At least one question is required when using a custom quiz.</p>
                            <div id="quizQuestionsContainer"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveQuizBtn">Save Quiz Settings</button>
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
            initQuizForm();

            const aiRadio = document.getElementById("quiz_type_ai");
            const customRadio = document.getElementById("quiz_type_custom");
            if (aiRadio) aiRadio.addEventListener("change", updateQuizTypeUI);
            if (customRadio) customRadio.addEventListener("change", updateQuizTypeUI);

            const saveBtn = document.getElementById("saveQuizBtn");
            if (saveBtn) {
                saveBtn.addEventListener("click", saveQuizSettings);
            }
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

        // Initialize empty quiz form (5 questions)
        function initQuizForm() {
            const container = document.getElementById("quizQuestionsContainer");
            if (!container) return;
            container.innerHTML = "";

            const totalQuestions = 5;
            for (let i = 1; i <= totalQuestions; i++) {
                container.innerHTML += `
                    <div class="border rounded p-3 mb-3 quiz-question-block" data-index="${i}">
                        <h6>Question ${i}</h6>
                        <div class="mb-2">
                            <label class="form-label">Question Text</label>
                            <textarea class="form-control" id="q${i}_text" rows="2"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Option A</label>
                                <input type="text" class="form-control" id="q${i}_optionA">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Option B</label>
                                <input type="text" class="form-control" id="q${i}_optionB">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Option C</label>
                                <input type="text" class="form-control" id="q${i}_optionC">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Option D</label>
                                <input type="text" class="form-control" id="q${i}_optionD">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Correct Option</label>
                            <select class="form-select" id="q${i}_correct">
                                <option value="">Select correct option</option>
                                <option value="A">Option A</option>
                                <option value="B">Option B</option>
                                <option value="C">Option C</option>
                                <option value="D">Option D</option>
                            </select>
                        </div>
                    </div>
                `;
            }
        }

        function updateQuizTypeUI() {
            const useAI = document.getElementById("quiz_type_ai").checked;
            const customSection = document.getElementById("customQuizSection");
            const container = document.getElementById("quizQuestionsContainer");

            if (useAI) {
                if (customSection) customSection.classList.add("opacity-50");
                if (container) {
                    container.querySelectorAll("input, textarea, select").forEach(el => {
                        el.disabled = true;
                    });
                }
            } else {
                if (customSection) customSection.classList.remove("opacity-50");
                if (container) {
                    container.querySelectorAll("input, textarea, select").forEach(el => {
                        el.disabled = false;
                    });
                }
            }
        }

        function resetQuizForm() {
            initQuizForm();
            const aiRadio = document.getElementById("quiz_type_ai");
            const customRadio = document.getElementById("quiz_type_custom");
            if (aiRadio) aiRadio.checked = true;
            if (customRadio) customRadio.checked = false;
            updateQuizTypeUI();
        }

        function openQuizEditor(materialId) {
            const materialInput = document.getElementById("quiz_material_id");
            if (!materialInput) return;
            materialInput.value = materialId;

            // Reset form fields
            resetQuizForm();

            // Fetch existing quiz configuration (type + questions)
            fetch(`getMaterialQuiz.php?material_id=${materialId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error("Error loading quiz data:", data.error);
                        return;
                    }

                    const quizType = data.quiz_type || "ai";
                    const aiRadio = document.getElementById("quiz_type_ai");
                    const customRadio = document.getElementById("quiz_type_custom");
                    if (quizType === "custom") {
                        if (customRadio) customRadio.checked = true;
                        if (aiRadio) aiRadio.checked = false;
                    } else {
                        if (aiRadio) aiRadio.checked = true;
                        if (customRadio) customRadio.checked = false;
                    }

                    // Populate questions if any
                    if (Array.isArray(data.questions)) {
                        data.questions.forEach((q, index) => {
                            const i = index + 1;
                            if (i > 5) return; // limit to 5
                            const qText = document.getElementById(`q${i}_text`);
                            const qA = document.getElementById(`q${i}_optionA`);
                            const qB = document.getElementById(`q${i}_optionB`);
                            const qC = document.getElementById(`q${i}_optionC`);
                            const qD = document.getElementById(`q${i}_optionD`);
                            const qCorrect = document.getElementById(`q${i}_correct`);

                            if (qText) qText.value = q.question_text || "";
                            if (qA) qA.value = q.option_a || "";
                            if (qB) qB.value = q.option_b || "";
                            if (qC) qC.value = q.option_c || "";
                            if (qD) qD.value = q.option_d || "";
                            if (qCorrect) qCorrect.value = q.correct_option || "";
                        });
                    }

                    updateQuizTypeUI();
                })
                .catch(error => console.error("Error fetching material quiz:", error));

            const quizModalEl = document.getElementById("quizEditorModal");
            if (quizModalEl) {
                const quizModal = new bootstrap.Modal(quizModalEl);
                quizModal.show();
            }
        }

        function saveQuizSettings() {
            const materialId = document.getElementById("quiz_material_id") ? document.getElementById("quiz_material_id").value : "";
            const aiRadio = document.getElementById("quiz_type_ai");
            const useAI = aiRadio ? aiRadio.checked : true;

            if (!materialId) {
                alert("No material selected.");
                return;
            }

            if (useAI) {
                // Just set quiz type to AI
                fetch("setQuizType.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ material_id: parseInt(materialId), quiz_type: "ai" })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert(data.message || "Failed to update quiz type.");
                        return;
                    }
                    alert("Quiz settings saved. Students will see AI-generated quizzes for this material.");
                    const modalEl = document.getElementById("quizEditorModal");
                    if (modalEl) {
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();
                    }
                })
                .catch(error => {
                    console.error("Error updating quiz type:", error);
                    alert("Error updating quiz type.");
                });
                return;
            }

            // Collect custom questions
            const questions = [];
            for (let i = 1; i <= 5; i++) {
                const qTextEl = document.getElementById(`q${i}_text`);
                const qAEl = document.getElementById(`q${i}_optionA`);
                const qBEl = document.getElementById(`q${i}_optionB`);
                const qCEl = document.getElementById(`q${i}_optionC`);
                const qDEl = document.getElementById(`q${i}_optionD`);
                const qCorrectEl = document.getElementById(`q${i}_correct`);

                const qText = qTextEl ? qTextEl.value.trim() : "";
                const qA = qAEl ? qAEl.value.trim() : "";
                const qB = qBEl ? qBEl.value.trim() : "";
                const qC = qCEl ? qCEl.value.trim() : "";
                const qD = qDEl ? qDEl.value.trim() : "";
                const qCorrect = qCorrectEl ? qCorrectEl.value.trim() : "";

                // Skip completely empty question blocks
                if (!qText && !qA && !qB && !qC && !qD && !qCorrect) {
                    continue;
                }

                questions.push({
                    question_text: qText,
                    option_a: qA,
                    option_b: qB,
                    option_c: qC,
                    option_d: qD,
                    correct_option: qCorrect
                });
            }

            if (questions.length === 0) {
                alert("Please add at least one complete question for a custom quiz.");
                return;
            }

            fetch("saveCustomQuiz.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    material_id: parseInt(materialId),
                    questions: questions
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    alert(data.message || "Failed to save custom quiz.");
                    return;
                }
                alert("Custom quiz saved. Students will see your questions for this material.");
                const modalEl = document.getElementById("quizEditorModal");
                if (modalEl) {
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();
                }
            })
            .catch(error => {
                console.error("Error saving custom quiz:", error);
                alert("Error saving custom quiz.");
            });
        }

        function viewQuizResults(materialId) {
            console.log("Viewing quiz results for material ID:", materialId);
            // Store the current material ID for download
            window.currentMaterialId = materialId;
            console.log("Stored material ID:", window.currentMaterialId);
            
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
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Passed</h5>
                                        <h2>${passedCount}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Failed</h5>
                                        <h2>${failedCount}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h6>Detailed Results:</h6>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Status</th>
                                        <th>Attempt Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.map(result => `
                                        <tr>
                                            <td>${result.firstname} ${result.lastname}</td>
                                            <td><span class="badge ${result.status === 'passed' ? 'bg-success' : 'bg-danger'}">${result.status}</span></td>
                                            <td>${result.attempt_date}</td>
                                        </tr>
                                    `).join("")}
                                </tbody>
                            </table>
                        </div>
                    `;

                    // Show the quiz results modal
                    const quizResultsModal = new bootstrap.Modal(document.getElementById('quizResultsModal'));
                    quizResultsModal.show();
                })
                .catch(error => console.error("Error fetching quiz results:", error));
        }

        function downloadQuizResults() {
            console.log("Attempting to download results for material ID:", window.currentMaterialId);
            if (!window.currentMaterialId) {
                console.error("No material ID found in window.currentMaterialId");
                alert("No material selected");
                return;
            }
            
            // Create a download link
            fetch(`downloadQuizResults.php?material_id=${window.currentMaterialId}`)
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(text || 'Failed to download quiz results');
                        });
                    }
                    return response.blob();
                })
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `quiz_results_${window.currentMaterialId}_${new Date().toISOString().split('T')[0]}.csv`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                })
                .catch(error => {
                    console.error('Error downloading quiz results:', error);
                    alert('Error downloading quiz results: ' + error.message);
                });
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
                                    <button class="btn btn-warning btn-sm me-2" onclick="openQuizEditor(${material.material_id})">Manage Quiz</button>
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
