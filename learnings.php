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
        let currentMaterialId = null;
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

            // Store materials globally for reference
            window.currentCourseMaterials = materials;

            for (let i = 0; i < materials.length; i++) {
                const material = materials[i];
                const fileExtension = material.file_path.split('.').pop().toLowerCase();

                // Check if the user can attempt this quiz
                const canAttempt = i === 0 || await checkQuizProgress(materials[i - 1].material_id);

                // Function to determine how to display the content
                const getContentDisplay = (material) => {
                    const fileExtension = material.file_path.split('.').pop().toLowerCase();
                    
                    if (fileExtension === 'pdf') {
                        // For PDFs, use browser's built-in PDF viewer
                        return `
                            <div class="document-viewer-container">
                                <iframe 
                                    src="${material.file_path}" 
                                    width="100%" 
                                    height="500px" 
                                    style="border: none;"
                                    allowfullscreen="true"
                                ></iframe>
                            </div>`;
                    } else if (fileExtension.match(/doc(x)?/) || fileExtension.match(/ppt(x)?/)) {
                        // For Office documents, show a preview card with document info
                        const icon = fileExtension.match(/doc(x)?/) ? '📄' : '📊';
                        return `
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="display-1 mb-3">${icon}</div>
                                    <h5 class="card-title">${material.file_name}</h5>
                                    <p class="card-text text-muted">
                                        ${fileExtension.toUpperCase()} Document
                                    </p>
                                    <div class="btn-group">
                                        <a href="${material.file_path}" 
                                           class="btn btn-primary" 
                                           download="${material.file_name}">
                                            Download to View
                                        </a>
                                        <button class="btn btn-success" 
                                                onclick="markAsRead(${material.material_id})"
                                                id="markAsRead_${material.material_id}">
                                            Mark as Read
                                        </button>
                                    </div>
                                </div>
                            </div>`;
                    } else {
                        // For other files, show download button
                        return `
                            <div class="alert alert-info">
                                This file type cannot be previewed.
                                <br>
                                <a href="${material.file_path}" 
                                   class="btn btn-info mt-2" 
                                   download="${material.file_name}">
                                    Download File
                                </a>
                            </div>`;
                    }
                };

                // Get quiz status for this material
                const quizStatus = await checkQuizProgress(material.material_id);
                const quizStatusText = quizStatus ? 
                    `<span class="badge bg-success ms-2">Passed</span>` : 
                    `<span class="badge bg-warning ms-2">Not Attempted</span>`;

                materialContent.innerHTML += `
                    <div class="material-item" data-material-id="${material.material_id}">
                        <h6>${material.file_name}</h6>
                        ${getContentDisplay(material)}
                        ${fileExtension === "pdf" || fileExtension.match(/doc(x)?/) || fileExtension.match(/ppt(x)?/) ? 
                            `<div class="mt-3">
                                <button class="btn btn-primary" 
                                        onclick="startQuiz(${material.material_id})" 
                                        id="quizBtn_${material.material_id}">
                                    Take Quiz ${quizStatusText}
                                </button>
                            </div>` : 
                            ''
                        }
                    </div>
                `;
            }

            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('courseMaterialModal'));
            modal.show();
        }

        async function startQuiz(materialId) {
            try {
                // Clear old correct answers from localStorage
                localStorage.removeItem("correctAnswers");
                
                // Set the current material ID
                currentMaterialId = materialId;
                console.log("Starting quiz for material ID:", currentMaterialId);
                
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

                // Fetch the material data
                const response = await fetch(`fetchSingleMaterial.php?material_id=${materialId}`);
                if (!response.ok) {
                    throw new Error(`Failed to fetch material: ${response.status} ${response.statusText}`);
                }
                
                const material = await response.json();
                const fileExtension = material.file_path.split('.').pop().toLowerCase();

                if (fileExtension === 'pdf') {
                    const pdfText = await extractTextFromPDF(material.file_path);
                    if (!pdfText) {
                        throw new Error('Failed to extract text from PDF');
                    }
                    const questions = await generateQuizQuestions(pdfText);
                    if (!questions || questions.length === 0) {
                        throw new Error('Failed to generate quiz questions');
                    }
                    displayQuiz(questions);
                } else if (fileExtension.match(/doc(x)?/) || fileExtension.match(/ppt(x)?/)) {
                    // For Office documents, use the document title and type
                    const documentInfo = {
                        title: material.file_name,
                        type: fileExtension.match(/doc(x)?/) ? 'Word Document' : 'PowerPoint Presentation'
                    };

                    const questions = await generateQuizQuestions(
                        `This is a ${documentInfo.type} titled "${documentInfo.title}". ` +
                        `Please generate questions based on common concepts and topics that would be covered in this type of document.`
                    );
                    
                    if (!questions || questions.length === 0) {
                        throw new Error('Failed to generate quiz questions');
                    }
                    
                    displayQuiz(questions);
                } else {
                    quizContent.innerHTML = "<p class='text-danger'>Quizzes can only be generated for PDF, DOC, and PowerPoint materials.</p>";
                }
            } catch (err) {
                console.error("Error during quiz generation:", err);
                const quizContent = document.getElementById("quizContent");
                quizContent.innerHTML = `
                    <div class="alert alert-danger">
                        <h5>Failed to generate quiz</h5>
                        <p>We encountered an error while preparing the quiz. You can:</p>
                        <ul>
                            <li>Try refreshing the page</li>
                            <li>Check your internet connection</li>
                            <li>Contact support if the issue persists</li>
                        </ul>
                        <small class="text-muted">Error details: ${err.message}</small>
                    </div>
                `;
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
                console.log("Starting quiz generation with text content:", textContent.substring(0, 100) + "...");

                // First, try to generate questions using the Gemini API
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

                if (!response.ok) {
                    throw new Error(`API Error: ${response.status} ${response.statusText}`);
                }

                const data = await response.json();
                console.log("API response data:", data);

                // Extract the generated text from the response
                const generatedText = data.candidates[0].content.parts[0].text;
                console.log("Generated Quiz Text:", generatedText);

                // Split the response into individual questions
                const questionsArray = generatedText.split("\n").filter((line) => line.trim() !== "");

                // Remove bold formatting (**) from the questions
                const cleanedQuestions = questionsArray.map(line => line.replace(/\*\*/g, ''));

                return cleanedQuestions;
            } catch (error) {
                console.error("Error generating quiz questions:", error);
                
                // Fallback to generating simple questions if API fails
                console.log("Using fallback question generation");
                return generateFallbackQuestions(textContent);
            }
        }

        // Fallback function to generate questions if API fails
        function generateFallbackQuestions(textContent) {
            // Extract key sentences from the text
            const sentences = textContent.split(/[.!?]+/).filter(s => s.trim().length > 20);
            
            // Take first 5 sentences or less if not enough
            const selectedSentences = sentences.slice(0, Math.min(5, sentences.length));
            
            // Generate questions based on these sentences
            return selectedSentences.map((sentence, index) => {
                const words = sentence.trim().split(' ');
                // Remove some words to create a question
                const questionWords = words.filter((_, i) => i % 3 !== 0).join(' ');
                return [
                    `Q${index + 1}) ${questionWords}?`,
                    'A) True',
                    'B) False',
                    'C) Maybe',
                    'D) Not sure',
                    'Answer: A'
                ].join('\n');
            });
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
                
                // Add more detailed logging
                if (progress.error) {
                    console.error("Error from fetchQuizProgress.php:", progress.error);
                    return false;
                }
                
                
                if (typeof progress === 'object' && progress.status) {
                    return progress.status === 'passed';
                } else if (typeof progress === 'string') {
                    return progress === 'passed';
                }
                
                return false;
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

            const correctAnswers = JSON.parse(localStorage.getItem("correctAnswers"));
            let score = 0;

            answers.forEach(answer => {
                const [question, selectedOption] = answer.split(/ (?=[A-D]\))/);
                const selectedLetter = selectedOption.trim().charAt(0);
                if (correctAnswers[question] === selectedLetter) {
                    score++;
                }
            });

            const totalQuestions = Object.keys(correctAnswers).length;
            const passed = score >= Math.ceil(totalQuestions * 0.7);

            if (passed) {
                const updateSuccess = await updateQuizProgress(currentMaterialId, "passed");
                
                if (updateSuccess) {
                    // Update the quiz button to show passed status
                    const quizButton = document.getElementById(`quizBtn_${currentMaterialId}`);
                    if (quizButton) {
                        quizButton.innerHTML = `Take Quiz <span class="badge bg-success ms-2">Passed</span>`;
                    }
                    
                    // Then check if we should unlock next material
                    const courseId = await getCurrentCourseId(); 
                    const canProgress = await checkCourseProgression(courseId);
                    if (canProgress) {
                        alert("Success! You can now access the next material.");
                        // Refresh the materials display
                        accessCourse(courseId); 
                    } else {
                        alert(`Quiz passed! You scored ${score}/${totalQuestions}.`);
                    }
                }
            } else {
                alert(`Quiz failed. You scored ${score}/${totalQuestions}. Please try again.`);
                await updateQuizProgress(currentMaterialId, "failed");
                
                // Update the quiz button to show failed status
                const quizButton = document.getElementById(`quizBtn_${currentMaterialId}`);
                if (quizButton) {
                    quizButton.innerHTML = `Take Quiz <span class="badge bg-danger ms-2">Failed</span>`;
                }
            }
        }


    
        async function getCurrentCourseId() {

            const modal = document.getElementById('courseMaterialModal');
            return modal ? parseInt(modal.dataset.courseId) : null;
        }

            
        function testQuizProgress() {
            if (!currentMaterialId) {
                console.error("Cannot test quiz progress - no current material selected");
                return;
            }
            
            console.log("===== QUIZ PROGRESS TESTING =====");
            console.log(`Testing with current material ID: ${currentMaterialId}`);
            
            updateQuizProgress(currentMaterialId, "passed")
                .then(result => {
                    console.log(`Update result for material ${currentMaterialId}: ${result}`);
                    return checkQuizProgress(currentMaterialId);
                })
                .then(isPassed => {
                    console.log(`Check result for material ${currentMaterialId}: ${isPassed ? "Passed" : "Not passed"}`);
                    if (isPassed) {
                        console.log("✅ Quiz progress system is working correctly!");
                    } else {
                        console.log("❌ Problem detected: Progress was updated but check returned false");
                    }
                })
                .catch(error => {
                    console.error("Error during test:", error);
                });
        }

        async function checkCourseProgression(courseId) {
            try {
                // Fetch all materials for this course
                const response = await fetch(`fetchCourseMaterials.php?course_id=${courseId}`);
                const materials = await response.json();
                
                // Check if all previous materials are passed
                for (let i = 0; i < materials.length; i++) {
                    const material = materials[i];
                    
                    // Don't check materials after the current one
                    if (material.material_id >= currentMaterialId) break;
                    
                    const isPassed = await checkQuizProgress(material.material_id);
                    if (!isPassed) {
                        console.log(`Blocking progression - material ${material.material_id} not passed`);
                        return false;
                    }
                }
                return true;
            } catch (error) {
                console.error("Progression check failed:", error);
                return false;
            }
        }

        async function updateQuizProgress(materialId, status) {
            console.log(`Updating quiz progress: Material ID ${materialId}, Status: ${status}`);
            
            try {
                // Validate inputs before sending
                if (!materialId || !status) {
                    console.error("Invalid parameters:", { materialId, status });
                    return false;
                }
                
                // Ensure materialId is a number and status is a string
                const validMaterialId = parseInt(materialId, 10);
                const validStatus = String(status);
                
                if (isNaN(validMaterialId) || validMaterialId <= 0) {
                    console.error("Invalid material ID:", materialId);
                    return false;
                }
                
                if (validStatus !== "passed" && validStatus !== "failed") {
                    console.error("Invalid status:", status);
                    return false;
                }
                
                // Create the data object
                const data = {
                    material_id: validMaterialId,
                    status: validStatus
                };
                
                console.log("Sending data to updateQuizProgress.php:", data);
                
                // Send the request
                const response = await fetch('updateQuizProgress.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data),
                });
                
                console.log(`updateQuizProgress response status: ${response.status}`);
                
                // Read response as text first for debugging
                const responseText = await response.text();
                console.log(`Raw response from updateQuizProgress.php: ${responseText}`);
                
                // Try to parse JSON
                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (e) {
                    console.error("Failed to parse response as JSON:", e);
                    console.error("Response text was:", responseText);
                    return false;
                }
                
                // Check for errors
                if (result.error) {
                    console.error("Error from server:", result.error);
                    return false;
                }
                
                console.log("Quiz progress updated successfully:", result);
                return result.success === true;
            } catch (error) {
                console.error("Error updating quiz progress:", error);
                return false;
            }
        }

        function validateAnswers(answers) {
            return answers.filter(answer => answer.trim() !== "").length;
        }

        // Add this new function for marking documents as read
        async function markAsRead(materialId) {
            const button = document.getElementById(`markAsRead_${materialId}`);
            const quizButton = document.getElementById(`quizBtn_${materialId}`);
            
            try {
                button.innerHTML = '✓ Marked as Read';
                button.classList.replace('btn-success', 'btn-secondary');
                button.disabled = true;
                
                // Enable quiz button if it exists
                if (quizButton) {
                    quizButton.disabled = false;
                }
                
                // You can add an actual API call here to record this in your database
                // await fetch('mark_as_read.php', {
                //     method: 'POST',
                //     body: JSON.stringify({ material_id: materialId }),
                //     headers: { 'Content-Type': 'application/json' }
                // });
            } catch (error) {
                console.error('Error marking as read:', error);
                alert('Failed to mark as read. Please try again.');
            }
        }
    </script>

</body>
</html>
