<?php
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    redirect(roleHome(user()['role']));
}

$msg = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!in_array($role, ['student', 'teacher'], true)) {
        $error = 'Invalid role selected.';
    } else {
        $exists = db()->prepare('SELECT id FROM users WHERE email = ?');
        $exists->execute([$email]);

        if ($exists->fetch()) {
            $error = 'Email already registered.';
        } else {
            $stmt = db()->prepare('INSERT INTO users (full_name, email, role, password_hash, created_at) VALUES (?, ?, ?, ?, NOW())');
            $stmt->execute([$fullName, $email, $role, password_hash($password, PASSWORD_DEFAULT)]);
            $uid = (int) db()->lastInsertId();
            logActivity($uid, 'Registered account');
            $msg = 'Registration successful. You may now login.';
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card p-4 shadow-sm">
            <h3 class="mb-3">Student/Teacher Registration</h3>
            <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
            <?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
            <form method="post">
                <div class="mb-2"><label>Full Name</label><input class="form-control" name="full_name" required></div>
                <div class="mb-2"><label>Email</label><input class="form-control" type="email" name="email" required></div>
                <div class="mb-2"><label>Role</label>
                    <select class="form-select" name="role" required>
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                    </select>
                </div>
                <div class="mb-3"><label>Password</label><input class="form-control" type="password" name="password" required></div>
                <button class="btn btn-primary w-100">Create Account</button>
            </form>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
