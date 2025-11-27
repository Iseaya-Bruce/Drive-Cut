<?php
/**
 * Authentication and Authorization Functions
 * Handles all user authentication and role checking
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['error'])) {
    echo "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
    unset($_SESSION['error']);
}

function redirectIfNotAdmin() {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header("Location: ../auth/login.php");
        exit();
    }
}


/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is a student
 */
function isStudent() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'student';
}

/**
 * Check if user is an instructor
 */
function isInstructor() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'instructor';
}

/**
 * Check if user is an admin
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Check if user is an barber
 */
function isBarber() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'barber';
}


/**
 * Redirect to login if not logged in
 */
function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header("Location: /driving_school/auth/login.php");
        exit();
    }
}

/**
 * Redirect to login if not a student
 */
function redirectIfNotStudent() {
    if (!isStudent()) {
        $_SESSION['error'] = "Student access required";
        header("Location: /driving_school/auth/login.php");
        exit();
    }
}

/**
 * Redirect to login if not an instructor
 */
function redirectIfNotInstructor() {
    if (!isInstructor()) {
        $_SESSION['error'] = "Instructor access required";
        header("Location: /driving_school/auth/login.php");
        exit();
    }
}

function redirectIfNotBarber() {
    if (!isLoggedIn() || $_SESSION['role'] !== 'barber') {
        header("Location: /driving_school/auth/login.php");
        exit();
    }
}

?>