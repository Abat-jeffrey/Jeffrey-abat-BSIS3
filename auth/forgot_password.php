<?php
require_once __DIR__ . '/../includes/functions.php';

$msg = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';

    $stmt = db()->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $account = $stmt->fetch();

    if (!$account) {
        $error = 'No account found for that email.';
    } else {
        $update = db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $update->execute([password_hash($newPassword, PASSWORD_DEFAULT), $account['id']]);
        logActivity((int) $account['id'], 'Password reset through forgot password');
        $msg = 'Password reset successful. You can now login.';
    }
}

include __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card p-4 shadow-sm">
            <h3>Forgot Password</h3>
            <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
            <?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
            <form method="post">
                <div class="mb-3"><label>Registered Email</label><input class="form-control" type="email" name="email" required></div>
                <div class="mb-3"><label>New Password</label><input class="form-control" type="password" name="new_password" required></div>
                <button class="btn btn-primary w-100">Reset Password</button>
            </form>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
