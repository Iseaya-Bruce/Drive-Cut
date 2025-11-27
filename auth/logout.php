<?php 
    // incude constants.php voor SITEURL
    include('../includes/config.php');
    require_once '../includes/functions.php';
    //1. destroy session
    session_destroy(); //unset $_SESSION['user']

    //2. redirect to login
    header('location: ../auth/login.php')
?>