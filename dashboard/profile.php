<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin(['student', 'teacher']);

$userId = user()['id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $stmt = db()->prepare('UPDATE users SET full_name = ?, email = ? WHERE id = ?');
        $stmt->execute([$fullName, $email, $userId]);
        $_SESSION['user']['full_name'] = $fullName;
        $_SESSION['user']['email'] = $email;
        logActivity($userId, 'Updated profile');
        $success = 'Profile updated.';
    }

    if (isset($_POST['change_password'])) {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $check = db()->prepare('SELECT password_hash FROM users WHERE id = ?');
        $check->execute([$userId]);
        $row = $check->fetch();
        if ($row && password_verify($current, $row['password_hash'])) {
            $up = db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
            $up->execute([password_hash($new, PASSWORD_DEFAULT), $userId]);
            logActivity($userId, 'Changed password');
            $success = 'Password updated.';
        } else {
            $error = 'Current password is incorrect.';
        }
    }
}

$stmt = db()->prepare('SELECT full_name, email, role FROM users WHERE id = ?');
$stmt->execute([$userId]);
$profile = $stmt->fetch();

include __DIR__ . '/../includes/header.php';
?>
<h3 class="mb-3">My Profile</h3>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<div class="row g-3">
    <div class="col-md-6">
        <div class="card p-3">
            <h5>Edit Profile</h5>
            <form method="post">
                <input type="hidden" name="update_profile" value="1">
                <div class="mb-2"><label>Full Name</label><input class="form-control" name="full_name" value="<?= e($profile['full_name']) ?>" required></div>
                <div class="mb-2"><label>Email</label><input class="form-control" type="email" name="email" value="<?= e($profile['email']) ?>" required></div>
                <div class="mb-2"><label>Role</label><input class="form-control" value="<?= e(ucfirst($profile['role'])) ?>" disabled></div>
                <button class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-3">
            <h5>Change Password</h5>
            <form method="post">
                <input type="hidden" name="change_password" value="1">
                <div class="mb-2"><label>Current Password</label><input class="form-control" type="password" name="current_password" required></div>
                <div class="mb-2"><label>New Password</label><input class="form-control" type="password" name="new_password" required></div>
                <button class="btn btn-dark">Change Password</button>
            </form>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
