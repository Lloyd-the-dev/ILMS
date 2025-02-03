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

        require('dotenv').config();
        const apiKey = process.env.OPENAI_API_KEY;

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

        function accessCourse(courseId) {
            // Fetch the course materials based on the courseId
            fetch(`fetchCourseMaterials.php?course_id=${courseId}`)
                .then(response => response.json())
                .then(materials => {
                    const materialContent = document.getElementById("courseMaterialContent");
                    materialContent.innerHTML = ""; // Clear previous content

                    if (materials.length === 0) {
                        materialContent.innerHTML = "<p>No materials available for this course.</p>";
                        return;
                    }

                    materials.forEach(material => {
                        const fileExtension = material.file_path.split('.').pop().toLowerCase();

                        if (fileExtension === "pdf") {
                            // Embed PDF using an iframe
                            materialContent.innerHTML += `
                                <div class="material-item">
                                    <h6>${material.file_name}</h6>
                                    <iframe src="${material.file_path}" width="100%" height="500px" style="border: none;"></iframe>
                                    <button id="quizButton" class="btn btn-primary" onclick="startQuiz('${courseId}')">Take Quiz</button>
                                </div>
                            `;
                        } else if (fileExtension === "ppt" || fileExtension === "pptx") {
                            // Convert PowerPoint to PDF or use an online viewer
                            materialContent.innerHTML += `
                                <div class="material-item">
                                    <h6>${material.file_name}</h6>
                                    <p>PowerPoint files cannot be displayed directly. <a href="${material.file_path}" target="_blank">Download</a></p>
                                    <button id="quizButton" class="btn btn-primary" onclick="startQuiz('${courseId}')">Take Quiz</button>
                                </div>
                            `;
                        } else {
                            // For other file types, provide a download link
                            materialContent.innerHTML += `
                                <div class="material-item">
                                    <h6>${material.file_name}</h6>
                                    <a href="${material.file_path}" target="_blank" class="btn btn-info">Download</a>
                                </div>
                            `;
                        }
                    });

                    // Show the modal
                    const modal = new bootstrap.Modal(document.getElementById('courseMaterialModal'));
                    modal.show();
                })
                .catch(error => console.error("Error fetching course materials:", error));
        }

        async function startQuiz(courseId) {
        // Fetch the course materials
        const response = await fetch(`fetchCourseMaterials.php?course_id=${courseId}`);
        const materials = await response.json();

        // Extract text from PDF materials
        let textContent = '';
        for (const material of materials) {
            if (material.file_path.endsWith('.pdf')) {
                const pdfText = await extractTextFromPDF(material.file_path);
                textContent += pdfText;
            }
        }

        // Generate quiz questions using ChatGPT API
        const quizQuestions = await generateQuizQuestions(textContent);

        // Display quiz questions
        displayQuiz(quizQuestions);
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
        console.log("Sending request to OpenAI API...");

        const response = await fetch('https://api.openai.com/v1/chat/completions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${process.env.OPENAI_API_KEY}`
            },
            body: JSON.stringify({
                model: "gpt-3.5-turbo", // or "gpt-3.5-turbo"
                messages: [
                    {
                        role: "system",
                        content: "You are a helpful assistant that generates quiz questions based on the provided text."
                    },
                    {
                        role: "user",
                        content: `Generate 5 quiz questions based on the following text: ${textContent}`
                    }
                ]
            })
        });

        console.log("Received response from OpenAI:", response);

        // Check if the response is okay
        if (response.ok) {
            const data = await response.json();
            console.log("API response data:", data);

            // Assuming the API returns the questions in a structured format
            return data.choices[0].message.content; // This is where your quiz questions should be
        } else {
            const errorData = await response.json();
            console.error("Error from OpenAI API:", errorData);
            throw new Error(`API Error: ${errorData.error.message}`);
        }
    } catch (error) {
        console.error("Error generating quiz questions:", error);
        return [];
    }
}


    function displayQuiz(questions) {
        const quizContent = document.getElementById("quizContent");
        quizContent.innerHTML = ""; // Clear previous content

        questions.forEach((question, index) => {
            quizContent.innerHTML += `
                <div class="quiz-question">
                    <h6>Question ${index + 1}: ${question}</h6>
                    <input type="text" id="answer${index}" placeholder="Your answer">
                </div>
            `;
        });

        // Show the quiz modal
        const quizModal = new bootstrap.Modal(document.getElementById('quizModal'));
        quizModal.show();
    }

    function submitQuiz() {
        const quizContent = document.getElementById("quizContent");
        const answers = [];
        quizContent.querySelectorAll('.quiz-question input').forEach(input => {
            answers.push(input.value);
        });

        // Validate answers (this is a simple example, you might want to send answers to the server for validation)
        const correctAnswers = validateAnswers(answers);

        if (correctAnswers >= 3) { // Example threshold
            alert("Quiz passed! You can proceed to the next material.");
            // Enable next material
        } else {
            alert("Quiz failed. Please review the material and try again.");
        }
    }

    function validateAnswers(answers) {
        // Implement your validation logic here
        // This could involve sending answers to the server for validation
        return answers.filter(answer => answer.trim() !== "").length; // Example: count non-empty answers
    }
    
    </script>
</body>
</html>
