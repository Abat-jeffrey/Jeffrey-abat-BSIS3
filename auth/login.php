<?php
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    redirect(roleHome(user()['role']));
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $account = $stmt->fetch();

    if ($account && password_verify($password, $account['password_hash'])) {
        $_SESSION['user'] = [
            'id' => (int) $account['id'],
            'full_name' => $account['full_name'],
            'email' => $account['email'],
            'role' => $account['role'],
        ];
        logActivity((int) $account['id'], 'Logged in');
        redirect(roleHome($account['role']));
    }

    $error = 'Invalid credentials.';
}

include __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card p-4 shadow-sm">
            <h3 class="mb-3">Login</h3>
            <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
            <form method="post">
                <div class="mb-3"><label>Email</label><input class="form-control" type="email" name="email" required></div>
                <div class="mb-3"><label>Password</label><input class="form-control" type="password" name="password" required></div>
                <button class="btn btn-primary w-100">Login</button>
            </form>
            <div class="mt-3 d-flex justify-content-between">
                <a href="<?= BASE_URL ?>/auth/register.php">Register</a>
                <a href="<?= BASE_URL ?>/auth/forgot_password.php">Forgot Password</a>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
