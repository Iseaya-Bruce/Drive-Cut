<?php
include '../includes/auth.php';
include '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    
    if (!empty($_FILES['profile_image']['name'])) {
        $targetDir = "../uploads/profile_images/";
        $fileName = basename($_FILES['profile_image']['name']);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Allow certain file formats
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($fileType), $allowedTypes)) {
            // Upload file to server
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFilePath)) {
                // Update the profile image path in the database
                $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
                $stmt->execute([$fileName, $userId]);

                $_SESSION['success'] = "Profile picture uploaded successfully!";
                header("Location: ../barber/dashboard.php");
            } else {
                $_SESSION['error'] = "Error uploading the image.";
            }
        } else {
            $_SESSION['error'] = "Only JPG, JPEG, PNG, & GIF files are allowed.";
        }
    } else {
        $_SESSION['error'] = "Please select a file to upload.";
    }

    header("Location: dashboard.php");
    exit();
}
?>
