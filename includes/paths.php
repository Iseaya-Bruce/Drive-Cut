<?php
// includes/paths.php
define('ROOT_PATH', dirname(__DIR__));
define('INCLUDES_PATH', ROOT_PATH . '../includes');

require_once INCLUDES_PATH . '/auth.php';
require_once '../includes/functions.php';

// For debugging
 __DIR__; // Shows current directory
echo realpath(__DIR__ . '../includes/auth.php'); // Check if file exists

// Option 1: Relative to document root
require_once $_SERVER['DOCUMENT_ROOT'] . '/driving_school/includes/auth.php';

// Option 2: Use dirname() with levels
require_once dirname(__DIR__, 1) . '../includes/auth.php';
?>