<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotAdmin();

// 🔧 Add this block:
$stmt = $pdo->query("SELECT * FROM users ORDER BY full_name");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .role-column {
            margin-bottom: 40px;
        }
        .role-column h4 {
            margin-bottom: 20px;
        }
        .user-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .user-info i {
            margin-right: 8px;
            color: #007bff;
        }
        .actions a {
            margin-right: 10px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="dashboard-container">
    <h2><i class="fas fa-users-cog"></i> Admin Dashboard - User Management</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"> <?= $_SESSION['error'] ?> </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"> <?= $_SESSION['success'] ?> </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="row">
        <?php
        $roles = ['student', 'instructor', 'barber'];
        $icons = [
            'student' => 'fa-user-graduate',
            'instructor' => 'fa-chalkboard-teacher',
            'barber' => 'fa-scissors'
        ];

        foreach ($roles as $role): ?>
            <div class="col-md-4 role-column">
                <h4><i class="fas <?= $icons[$role] ?>"></i> <?= ucfirst($role) ?>s</h4>
                <?php foreach ($users as $user): ?>
                    <?php if ($user['role'] === $role): ?>
                        <div class="user-card">
                            <div class="user-info">
                                <p><i class="fas fa-user"></i> <?= htmlspecialchars($user['full_name']) ?></p>
                                <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($user['username']) ?></p>
                                <p><i class="fas fa-circle"></i> Status: <strong><?= $user['status'] === 'active' ? 'Active' : 'Inactive' ?></strong></p>
                            </div>
                            <div class="actions">
                                <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <a href="toggle_status.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-warning">Toggle</a>
                                <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this user?')">Delete</a>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
</body>
</html>
