<?php
include '../includes/auth.php';
include '../includes/config.php';



// ✅ Only require that the user is logged in (any role)
if (!isLoggedIn()) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];

    if (!empty($_FILES['id_card_image']['name']) && $_FILES['id_card_image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../uploads/id_cards/";

        // Create directory if it doesn't exist
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Generate unique filename
        $fileExtension = strtolower(pathinfo($_FILES['id_card_image']['name'], PATHINFO_EXTENSION));
        $newFileName = 'id_card_' . $userId . '_' . time() . '.' . $fileExtension;
        $targetFilePath = $targetDir . $newFileName;

        // Allow certain file formats
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExtension, $allowedTypes)) {
            $check = getimagesize($_FILES['id_card_image']['tmp_name']);
            if ($check !== false) {
                if (move_uploaded_file($_FILES['id_card_image']['tmp_name'], $targetFilePath)) {
                    $stmt = $pdo->prepare("UPDATE users SET id_card_image = ? WHERE id = ?");
                    $stmt->execute([$newFileName, $userId]);

                    $_SESSION['success'] = "ID card uploaded successfully!";
                } else {
                    $_SESSION['error'] = "Error uploading your file.";
                    error_log("Upload error: Could not move file.");
                }
            } else {
                $_SESSION['error'] = "The file is not a valid image.";
            }
        } else {
            $_SESSION['error'] = "Only JPG, JPEG, PNG, & GIF files are allowed.";
        }
    } else {
        $_SESSION['error'] = "Please select a valid file to upload.";
    }

    // Redirect to dashboard based on role
    if (isStudent()) {
        header("Location: dashboard.php");
    } elseif (isInstructor()) {
        header("Location: ../instructor/dashboard.php");
    } elseif (isBarber()) {
        header("Location: ../barber/dashboard.php");
    } else {
        header("Location: ../index.php");
    }
    exit();
}
?>
