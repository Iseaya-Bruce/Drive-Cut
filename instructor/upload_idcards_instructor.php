<?php
include '../includes/auth.php';
include '../includes/config.php';

if (!isInstructor()) {
    $_SESSION['error'] = "Instructor access required.";
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];

    if (!empty($_FILES['id_card_image']['name']) && $_FILES['id_card_image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../uploads/id_cards/";

        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $fileExtension = strtolower(pathinfo($_FILES['id_card_image']['name'], PATHINFO_EXTENSION));
        $newFileName = 'id_card_' . $userId . '_' . time() . '.' . $fileExtension;
        $targetFilePath = $targetDir . $newFileName;

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExtension, $allowedTypes)) {
            $check = getimagesize($_FILES['id_card_image']['tmp_name']);
            if ($check !== false) {
                if (move_uploaded_file($_FILES['id_card_image']['tmp_name'], $targetFilePath)) {
                    $stmt = $pdo->prepare("UPDATE users SET id_card_image = ? WHERE id = ?");
                    $stmt->execute([$newFileName, $userId]);

                    $_SESSION['success'] = "ID card uploaded successfully!";
                } else {
                    $_SESSION['error'] = "Error uploading file.";
                }
            } else {
                $_SESSION['error'] = "File is not a valid image.";
            }
        } else {
            $_SESSION['error'] = "Invalid file format. Only JPG, PNG, and GIF allowed.";
        }
    } else {
        $_SESSION['error'] = "No file selected or upload error.";
    }

    header("Location: dashboard.php");
    exit();
}
?>
