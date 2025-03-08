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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.min.js" integrity="sha512-Z8CqofpIcnJN80feS2uccz+pXWgZzeKxDsDNMD/dJ6997/LSRY+W4NmEt9acwR+Gt9OHN0kkI1CTianCwoqcjQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.worker.min.js" integrity="sha512-lHibs5XrZL9hXP3Dhr/d2xJgPy91f2mhVAasrSbMkbmoTSm2Kz8DuSWszBLUg31v+BM6tSiHSqT72xwjaNvl0g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
            <li><a href="learnings.php" id="active">Learnings</a></li>
        </ul>
        <button class="login-btn"><a href="index.html">Logout</a></button>
    </nav>

    <div class="container mt-4">
        <h1 class="greeting text-center">Welcome, <?php echo $firstname; ?>!</h1>
        <h3 class="text-center mt-4 text-muted fw-bold">My Enrolled Courses</h3>

        <div class="row mt-3" id="learningsGrid"></div>
    </div>
    <!-- Add a modal for displaying course materials -->
    <div class="modal" id="courseMaterialModal" tabindex="-1" aria-labelledby="courseMaterialModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="courseMaterialModalLabel">Course Materials</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="courseMaterialContent">
                    <!-- Dynamic content will be inserted here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Quiz Modal -->
    <div class="modal" id="quizModal" tabindex="-1" aria-labelledby="quizModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quizModalLabel">Quiz</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="quizContent">
                    <!-- Quiz questions will be inserted here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="submitQuiz()">Submit Quiz</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Check if pdfjsLib is available
        if (typeof pdfjsLib === "undefined") {
            console.error("pdfjsLib is not loaded. Check your script source.");
        } else {
            console.log("pdfjsLib loaded successfully.");
        }

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
                                        <button class="btn btn-primary" onclick="accessCourse(${course.course_id})">Access Course</button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                })
                .catch(error => console.error("Error fetching learnings:", error));
        }

        async function accessCourse(courseId) {
            const materials = await fetch(`fetchCourseMaterials.php?course_id=${courseId}`)
                .then(response => response.json())
                .catch(error => console.error("Error fetching course materials:", error));

            const materialContent = document.getElementById("courseMaterialContent");
            materialContent.innerHTML = ""; // Clear previous content

            if (materials.length === 0) {
                materialContent.innerHTML = "<p>No materials available for this course.</p>";
                return;
            }

            for (let i = 0; i < materials.length; i++) {
                const material = materials[i];
                const fileExtension = material.file_path.split('.').pop().toLowerCase();

                // Check if the user can attempt this quiz
                const canAttempt = i === 0 || await checkQuizProgress(materials[i - 1].material_id);

                if (fileExtension === "pdf") {
                    materialContent.innerHTML += `
                        <div class="material-item">
                            <h6>${material.file_name}</h6>
                            <iframe src="${material.file_path}" width="100%" height="500px" style="border: none;"></iframe>
                            <button class="btn btn-primary" onclick="${canAttempt ? `startQuiz('${material.material_id}')` : `alert('Woah buddy! Gotta pass the current quiz before moving on.')`}">Take Quiz</button>
                        </div>
                    `;
                } else if (fileExtension === "ppt" || fileExtension === "pptx") {
                    materialContent.innerHTML += `
                        <div class="material-item">
                            <h6>${material.file_name}</h6>
                            <p>PowerPoint files cannot be displayed directly. <a href="${material.file_path}" target="_blank">Download</a></p>
                            <button class="btn btn-primary" onclick="${canAttempt ? `startQuiz('${material.material_id}')` : `alert('Woah buddy! Gotta pass the current quiz before moving on.')`}">Take Quiz</button>
                        </div>
                    `;
                } else {
                    materialContent.innerHTML += `
                        <div class="material-item">
                            <h6>${material.file_name}</h6>
                            <a href="${material.file_path}" target="_blank" class="btn btn-info">Download</a>
                        </div>
                    `;
                }
            }

            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('courseMaterialModal'));
            modal.show();
        }

        let currentMaterialId = null;
        async function startQuiz(materialId) {
            try {
                // Clear old correct answers from localStorage
                localStorage.removeItem("correctAnswers");
                currentMaterialId = materialId;
                // Show loading spinner
                const quizContent = document.getElementById("quizContent");
                quizContent.innerHTML = `
                    <div class="d-flex justify-content-center my-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `;

                // Open the quiz modal
                const quizModal = new bootstrap.Modal(document.getElementById('quizModal'));
                quizModal.show();

                // Fetch the material data using the single material endpoint
                const response = await fetch(`fetchSingleMaterial.php?material_id=${materialId}`);
                console.log("Response from fetchSingleMaterial.php:", response);

                // Parse the response as JSON
                const material = await response.json();
                console.log("Material data:", material);

                // Ensure the material is a PDF
                if (material.file_path.endsWith('.pdf')) {
                    console.log("Extracting text from PDF:", material.file_path);
                    const pdfText = await extractTextFromPDF(material.file_path);
                    console.log("Extracted text content:", pdfText);

                    // Generate quiz questions using the Gemini API
                    console.log("Generating quiz questions...");
                    const questions = await generateQuizQuestions(pdfText);
                    console.log("Generated quiz questions:", questions);

                    displayQuiz(questions);
                } else {
                    console.error("The material is not a PDF.");
                    const quizContent = document.getElementById("quizContent");
                    if (quizContent) {
                        quizContent.innerHTML = "<p class='text-danger'>The selected material is not a PDF.</p>";
                    }
                }
            } catch (err) {
                console.error("Error during quiz generation:", err);
                const quizContent = document.getElementById("quizContent");
                if (quizContent) {
                    quizContent.innerHTML = "<p class='text-danger'>Failed to generate quiz questions. Please try again later.</p>";
                }
            }
        }

        async function extractTextFromPDF(pdfUrl) {
            try {
                console.log(`Fetching PDF from URL: ${pdfUrl}`);

                const response = await fetch(pdfUrl);
                if (!response.ok) {
                    throw new Error(`Failed to fetch PDF. Status: ${response.status}`);
                }

                const pdfData = await response.arrayBuffer();
                console.log("PDF data fetched successfully.");

                const pdf = await pdfjsLib.getDocument({ data: pdfData }).promise;
                console.log(`PDF loaded successfully. Number of pages: ${pdf.numPages}`);

                let textContent = '';

                for (let i = 1; i <= pdf.numPages; i++) {
                    const page = await pdf.getPage(i);
                    console.log(`Processing page ${i}...`);

                    const text = await page.getTextContent();
                    console.log(`Raw text content from page ${i}:`, text);

                    const extractedText = text.items.map(item => item.str).join(' ');
                    console.log(`Extracted text from page ${i}:`, extractedText);

                    textContent += extractedText + '\n'; // Adding newline for readability
                }

                console.log("Final extracted text:", textContent);
                return textContent;
            } catch (error) {
                console.error("Error extracting text from PDF:", error);
                return '';
            }
        }

        async function generateQuizQuestions(textContent) {
            try {
                console.log("Sending request to Gemini API...");

                const response = await fetch(
                    ``,
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            contents: [
                                {
                                    parts: [
                                        {
                                            text: `Generate 5 multiple-choice quiz questions based on the following text:\n${textContent}\nFormat: Q1) Question text? \nA) Option1 \nB) Option2 \nC) Option3 \nD) Option4 \nAnswer: A`,
                                        },
                                    ],
                                },
                            ],
                        }),
                    }
                );

                console.log("Received response from Gemini:", response);

                if (response.ok) {
                    const data = await response.json();
                    console.log("API response data:", data);

                    // Extract the generated text from the response
                    const generatedText = data.candidates[0].content.parts[0].text;
                    console.log("Generated Quiz Text:", generatedText);

                    // Split the response into individual questions
                    const questionsArray = generatedText.split("\n").filter((line) => line.trim() !== "");

                    // Remove bold formatting (**) from the questions
                    const cleanedQuestions = questionsArray.map(line => line.replace(/\*\*/g, ''));

                    return cleanedQuestions; // Return formatted questions
                } else {
                    const errorData = await response.json();
                    console.error("Error from Gemini API:", errorData);
                    throw new Error(`API Error: ${errorData.error.message}`);
                }
            } catch (error) {
                console.error("Error generating quiz questions:", error);
                return [];
            }
        }

        function displayQuiz(questions) {
            const quizContent = document.getElementById("quizContent");
            if (!quizContent) {
                console.error("Quiz content element not found.");
                return;
            }

            // Clear previous content
            quizContent.innerHTML = "";

            // Log the questions for debugging
            console.log("Questions to display:", questions);

            let currentQuestion = "";
            let options = [];
            let correctAnswers = {}; // Store correct answers for validation

            // Process each line in the questions array
            questions.forEach(line => {
                // Remove bold formatting (**) from the line
                line = line.replace(/\*\*/g, '');

                if (line.startsWith("Q")) {
                    // If we already have a question, display it before moving to the next one
                    if (currentQuestion) {
                        quizContent.innerHTML += `
                            <div class="quiz-question mb-4">
                                <h6>${currentQuestion}</h6>
                                ${options.map(option => `
                                    <label class="d-block">
                                        <input type="radio" name="${currentQuestion}" value="${currentQuestion} ${option}"> ${option}
                                    </label>
                                `).join("")}
                            </div>
                        `;
                    }
                    // Start new question
                    currentQuestion = line;
                    options = [];
                } else if (line.match(/^[A-D]\)/)) {
                    // Add options (A, B, C, D)
                    options.push(line);
                } else if (line.startsWith("Answer:")) {
                    // Store the correct answer for this question
                    correctAnswers[currentQuestion] = line.replace("Answer: ", "");
                } else {
                    console.warn("Unexpected line format:", line);
                }
            });

            // Display the last question
            if (currentQuestion) {
                quizContent.innerHTML += `
                    <div class="quiz-question mb-4">
                        <h6>${currentQuestion}</h6>
                        ${options.map(option => `
                            <label class="d-block">
                                <input type="radio" name="${currentQuestion}" value="${currentQuestion} ${option}"> ${option}
                            </label>
                        `).join("")}
                    </div>
                `;
            }

            // Store correct answers in localStorage for validation
            localStorage.setItem("correctAnswers", JSON.stringify(correctAnswers));

            // Show the quiz modal
            const quizModal = new bootstrap.Modal(document.getElementById('quizModal'));
            quizModal.show();
        }

        async function checkQuizProgress(materialId) {
            try {
                const response = await fetch(`fetchQuizProgress.php?material_id=${materialId}`);
                const progress = await response.json();
                console.log("Quiz progress for material", materialId, ":", progress);
                return progress.status === 'passed';
            } catch (error) {
                console.error("Error fetching quiz progress:", error);
                return false;
            }
        }

        async function submitQuiz() {
            const quizContent = document.getElementById("quizContent");
            const answers = [];
            quizContent.querySelectorAll('.quiz-question input').forEach(input => {
                if (input.checked) {
                    answers.push(input.value);
                }
            });

            // Retrieve correct answers from localStorage
            const correctAnswers = JSON.parse(localStorage.getItem("correctAnswers"));

            // Log correct answers and student's answers for debugging
            console.log("Correct answers:", correctAnswers);
            console.log("Student's answers:", answers);

            // Validate answers
            let score = 0;
            answers.forEach(answer => {
                // Split the answer into question and selected option
                const [question, selectedOption] = answer.split(/ (?=[A-D]\))/);

                // Extract the letter (e.g., "B") from the selected option
                const selectedLetter = selectedOption.trim().charAt(0);

                // Log the question and selected letter for debugging
                console.log("Checking answer:", selectedLetter, "for question:", question);

                // Compare the selected letter with the correct answer
                if (correctAnswers[question] === selectedLetter) {
                    console.log("Answer is correct!");
                    score++;
                } else {
                    console.log("Answer is incorrect.");
                }
            });

            // Log the final score for debugging
            console.log("Final score:", score);

            // Calculate the pass/fail result
            const totalQuestions = Object.keys(correctAnswers).length;
            const passThreshold = Math.ceil(totalQuestions * 0.7); // 70% threshold to pass
            const passed = score >= passThreshold;

            // Display the result
            if (passed) {
                alert(`Quiz passed! You scored ${score}/${totalQuestions}.`);
                await updateQuizProgress(currentMaterialId, "passed"); // Update progress on the server
            } else {
                alert(`Quiz failed. You scored ${score}/${totalQuestions}. Please review the material and try again.`);
                await updateQuizProgress(currentMaterialId, "failed"); // Update progress on the server
            }
        }


        async function updateQuizProgress(materialId, status) {
            try {
                const response = await fetch('updateQuizProgress.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ material_id: materialId, status: status }),
                });
                const result = await response.json();
                console.log("Update quiz progress result:", result);
                return result.success;
            } catch (error) {
                console.error("Error updating quiz progress:", error);
                return false;
            }
        }

        function validateAnswers(answers) {
            return answers.filter(answer => answer.trim() !== "").length;
        }
    </script>

</body>
</html>
