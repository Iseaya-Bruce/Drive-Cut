<?php
/**
 * General Utility Functions
 * Contains helper functions used throughout the application
 */

/**
 * Database Connection Singleton
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . 'localhost' . ';dbname=' . 'driving-school' . ';charset=utf8mb4',
                "root", 
                "",
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            error_log('Database connection error: ' . $e->getMessage());
            die('Database connection failed. Please try again later.');
        }
    }
    
    return $pdo;
}

/**
 * Input sanitization
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Date/time validation
 */
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

function validateTime($time) {
    return preg_match('/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $time);
}

/**
 * Formatting helpers
 */
function formatDate($date, $format = 'F j, Y') {
    return (new DateTime($date))->format($format);
}

function formatDateTime($datetime, $format = 'F j, Y g:i A') {
    return (new DateTime($datetime))->format($format);
}

/**
 * Flash message handling
 */
function flashMessage() {
    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
        unset($_SESSION['error']);
    }
}

/**
 * Time slot generation
 */
function generateTimeOptions($interval = 30, $start = '08:00', $end = '20:00') {
    $options = '';
    $start = new DateTime($start);
    $end = new DateTime($end);
    $interval = new DateInterval("PT{$interval}M");
    
    $period = new DatePeriod($start, $interval, $end);
    
    foreach ($period as $time) {
        $value = $time->format('H:i');
        $label = $time->format('g:i A');
        $options .= "<option value=\"{$value}\">{$label}</option>";
    }
    
    return $options;
}

/**
 * CSRF Protection
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * User-related functions
 */
function getUserFullName($user_id) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn() ?: 'Unknown User';
}

/**
 * Booking availability check
 */
function isSlotAvailable($slot_id) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT status FROM availability WHERE id = ? AND status = 'available'");
    $stmt->execute([$slot_id]);
    return $stmt->fetchColumn() === 'available';
}
?>