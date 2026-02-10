<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

function redirect(string $path): void
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

function user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function isLoggedIn(): bool
{
    return user() !== null;
}

function requireLogin(array $roles = []): void
{
    if (!isLoggedIn()) {
        redirect('/auth/login.php');
    }

    if ($roles && !in_array(user()['role'], $roles, true)) {
        http_response_code(403);
        echo 'Forbidden';
        exit;
    }
}

function roleHome(string $role): string
{
    return match ($role) {
        'staff' => '/dashboard/staff.php',
        'admin' => '/dashboard/admin.php',
        default => '/dashboard/user.php',
    };
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function logActivity(int $actorId, string $action, ?int $itemId = null, ?int $targetUserId = null): void
{
    $stmt = db()->prepare('INSERT INTO activity_logs (actor_id, action, item_id, target_user_id, created_at) VALUES (?, ?, ?, ?, NOW())');
    $stmt->execute([$actorId, $action, $itemId, $targetUserId]);
}

function uploadImage(string $field, string $folder): ?string
{
    if (empty($_FILES[$field]['name'])) {
        return null;
    }

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $tmp = $_FILES[$field]['tmp_name'];
    $mime = mime_content_type($tmp);

    if (!isset($allowed[$mime])) {
        return null;
    }

    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $filename = uniqid('img_', true) . '.' . $allowed[$mime];
    $path = rtrim($folder, '/') . '/' . $filename;
    if (!move_uploaded_file($tmp, $path)) {
        return null;
    }

    return $filename;
}
