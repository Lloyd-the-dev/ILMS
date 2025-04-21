const navbarMenu = document.querySelector(".navbar .links");
const hamburgerBtn = document.querySelector(".hamburger-btn");
const hideMenuBtn = navbarMenu.querySelector(".close-btn");
const showPopupBtn = document.querySelector(".login-btn");
const formPopup = document.querySelector(".form-popup");
const hidePopupBtn = formPopup.querySelector(".close-btn");
const blurOverlay = document.querySelector(".blur-bg-overlay");
const signupLoginLink = formPopup.querySelectorAll(".bottom-link a");
const coursesLink = document.querySelector(".courses-link");

// Mobile menu toggle
hamburgerBtn.addEventListener("click", () => {
    navbarMenu.classList.toggle("show-menu");
});

hideMenuBtn.addEventListener("click", () => hamburgerBtn.click());

// Function to show login popup
function showLoginPopup() {
    document.body.classList.add("show-popup"); // Note: matches your CSS class
    document.querySelector(".form-box.login").classList.add("active");
    document.querySelector(".form-box.signup").classList.remove("active");
}

// Show login popup for both login button and courses link
showPopupBtn.addEventListener("click", (e) => {
    e.preventDefault();
    showLoginPopup();
});

coursesLink.addEventListener("click", (e) => {
    e.preventDefault();
    showLoginPopup();
});

// Hide popup
function hidePopup() {
    document.body.classList.remove("show-popup");
}

hidePopupBtn.addEventListener("click", hidePopup);
blurOverlay.addEventListener("click", hidePopup);

// Toggle between login and signup forms
signupLoginLink.forEach(link => {
    link.addEventListener("click", (e) => {
        e.preventDefault();
        if (link.id === 'signup-link') {
            formPopup.classList.add("show-signup");
        } else {
            formPopup.classList.remove("show-signup");
        }
    });
});