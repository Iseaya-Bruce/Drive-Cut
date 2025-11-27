<?php
$host = "localhost";
$dbname = "driving_school";
$username = "root";
$password = "";

define('PROFILE_IMAGE_DIR',  '../uploads/profile_images/');
define('PROFILE_IMAGE_URL', '/driving_school/uploads/profile_images/');

define('ID_CARD_IMAGE_DIR',  '../uploads/id_cards/');
define('ID_CARD_IMAGE_URL', '/driving_school/uploads/id_cards/');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>