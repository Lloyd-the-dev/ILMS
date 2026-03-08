document.addEventListener("DOMContentLoaded", () => {
    const navbarMenu = document.querySelector(".navbar .links");
    const hamburgerBtn = document.querySelector(".hamburger-btn");
    const hideMenuBtn = navbarMenu.querySelector(".close-btn");
    const showPopupBtn = document.querySelector(".login-btn");
    const formPopup = document.querySelector(".form-popup");
    const hidePopupBtn = formPopup.querySelector(".close-btn");
    const blurOverlay = document.querySelector(".blur-bg-overlay");
    const loginLink = document.querySelector("#login-link");
    const signupLink = document.querySelector("#signup-link");

    // Mobile menu toggle
    hamburgerBtn.addEventListener("click", () => {
        navbarMenu.classList.toggle("show-menu");
    });

    hideMenuBtn.addEventListener("click", () => hamburgerBtn.click());

    // Function to show login popup
    function showLoginPopup() {
        document.body.classList.add("show-popup");
    }

    // Function to hide popup
    function hidePopup() {
        document.body.classList.remove("show-popup");
    }

    // Show popup when login button is clicked
    showPopupBtn.addEventListener("click", showLoginPopup);

    // Hide popup when close button or overlay is clicked
    if (hidePopupBtn) {
        hidePopupBtn.addEventListener("click", hidePopup);
    }
    
    if (blurOverlay) {
        blurOverlay.addEventListener("click", hidePopup);
    }

    // Switch to signup form
    if (signupLink) {
        signupLink.addEventListener("click", (e) => {
            e.preventDefault();
            console.log("Signup clicked");
            formPopup.classList.add("show-signup");
        });
    }

    // Switch to login form
    if (loginLink) {
        loginLink.addEventListener("click", (e) => {
            e.preventDefault();
            console.log("Login clicked");
            formPopup.classList.remove("show-signup");
        });
    }

    // Handle department dropdown and level field visibility
    const levelField = document.getElementById("levelField");
    const levelDropdown = document.getElementById("level");
    const accTypeDropdown = document.getElementById("accType");
    const departmentDropdown = document.getElementById('department');

    // Function to toggle visibility of the Level field
    function toggleLevelField() {
        if (accTypeDropdown && accTypeDropdown.value === "Lecturer") {
            levelField.style.display = "none"; // Hide the Level field
            levelDropdown.disabled = true; // Disable the Level dropdown
        } else {
            levelField.style.display = "block"; // Show the Level field
            levelDropdown.disabled = false; 
        }
    }

    // Initial check for level field
    if (accTypeDropdown) {
        toggleLevelField();
        accTypeDropdown.addEventListener("change", toggleLevelField);
    }

    // Populate the Department Dropdown
    if (departmentDropdown) {
        fetch('./fetchDepartment.php')
            .then(response => response.json())
            .then(data => {
                departmentDropdown.innerHTML = '';
                data.forEach(departmentName => {
                    const option = document.createElement('option');
                    option.value = departmentName.department_name;
                    option.textContent = departmentName.department_name;
                    departmentDropdown.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching department names:', error));
    }
});