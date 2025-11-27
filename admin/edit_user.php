<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Edit User</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .edit-container {
            max-width: 700px;
            margin: 50px auto;
            padding: 30px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 25px;
            text-align: center;
        }
        label {
            font-weight: 600;
            margin-top: 15px;
        }
    </style>
</head>
<body>
<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotAdmin();

$id = $_GET['id'] ?? null;
if (!$id) {
    $_SESSION['error'] = "User ID missing.";
    header('Location: dashboard.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = "User not found.";
    header('Location: dashboard.php');
    exit();
}
?>

<?php include '../includes/header.php'; ?>
<div class="edit-container">
    <h2><i class="fas fa-user-edit"></i> Edit User</h2>
    <form method="post" action="update_user.php">
        <input type="hidden" name="id" value="<?= $user['id'] ?>">

        <div class="form-group mb-3">
            <label for="full_name"><i class="fas fa-user"></i> Full Name</label>
            <input type="text" id="full_name" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" required>
        </div>

        <div class="form-group mb-3">
            <label for="username"><i class="fas fa-user"></i> Username</label>
            <input type="text" id="username" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>

        <div class="form-group mb-3">
            <label for="phone"><i class="fas fa-phone"></i> Phone</label>
            <input type="text" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>">
        </div>

        <div class="form-group mb-3">
            <label for="address"><i class="fas fa-map-marker-alt"></i> Address</label>
            <input type="text" id="address" name="address" class="form-control" value="<?= htmlspecialchars($user['address']) ?>">
        </div>

        <div class="form-group mb-3">
            <label for="district"><i class="fas fa-city"></i> District</label>
            <select name="district" required>
                <option value="">Select District</option>
                <option value="Paramaribo">Paramaribo</option>
                <option value="Wanica">Wanica</option>
                <option value="Commewijne">Commewijne</option>
                <option value="Nickerie">Nickerie</option>
                <option value="Saramacca">Saramacca</option>
                <option value="Coronie">Coronie</option>
                <option value="Marowijne">Marowijne</option>
                <option value="Brokopondo">Brokopondo</option>
                <option value="Sipaliwini">Sipaliwini</option>
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="role"><i class="fas fa-user-tag"></i> Role</label>
            <select id="role" name="role" class="form-select" onchange="toggleRoleFields()">
                <option value="student" <?= $user['role'] === 'student' ? 'selected' : '' ?>>Student</option>
                <option value="instructor" <?= $user['role'] === 'instructor' ? 'selected' : '' ?>>Instructor</option>
                <option value="barber" <?= $user['role'] === 'barber' ? 'selected' : '' ?>>Barber</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>

        <div class="form-group mb-3" id="drivingSchoolField" style="display:none;">
            <label for="driving_school"><i class="fas fa-car"></i> Driving School</label>
            <input type="text" id="driving_school" name="driving_school" class="form-control" value="<?= htmlspecialchars($user['driving_school']) ?>">
        </div>

        <div class="form-group mb-3" id="barberShopField" style="display:none;">
            <label for="barbershop_name"><i class="fas fa-cut"></i> Barbershop Name</label>
            <input type="text" id="barbershop_name" name="barbershop_name" class="form-control" value="<?= htmlspecialchars($user['barbershop_name']) ?>">
        </div>

        <button type="submit" class="btn btn-success w-100"><i class="fas fa-save"></i> Update User</button>
    </form>
</div>

<script>
    function toggleRoleFields() {
        const role = document.getElementById('role').value;
        document.getElementById('drivingSchoolField').style.display = role === 'instructor' ? 'block' : 'none';
        document.getElementById('barberShopField').style.display = role === 'barber' ? 'block' : 'none';
    }
    // Trigger on page load
    toggleRoleFields();
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>
