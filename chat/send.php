<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin(['student', 'teacher', 'staff']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(roleHome(user()['role']));
}

$senderId = user()['id'];
$receiverId = (int) ($_POST['receiver_id'] ?? 0);
$message = trim($_POST['message'] ?? '');

if (in_array(user()['role'], ['student', 'teacher'], true)) {
    $staff = db()->query("SELECT id FROM users WHERE role='staff' LIMIT 1")->fetch();
    if (!$staff || (int) $staff['id'] !== $receiverId) {
        die('Students and teachers can only message staff.');
    }
}

if (user()['role'] === 'staff') {
    $allowedUser = db()->prepare("SELECT id FROM users WHERE id = ? AND role IN ('student','teacher') LIMIT 1");
    $allowedUser->execute([$receiverId]);
    if (!$allowedUser->fetch()) {
        die('Staff can only message students and teachers.');
    }
}

$image = uploadImage('image', __DIR__ . '/../uploads/chat');

if ($message !== '' || $image) {
    $stmt = db()->prepare('INSERT INTO messages (sender_id, receiver_id, body, image_path, created_at) VALUES (?, ?, ?, ?, NOW())');
    $stmt->execute([$senderId, $receiverId, $message, $image]);
    logActivity($senderId, 'Sent chat message', null, $receiverId);
}

if (user()['role'] === 'staff') {
    redirect('/chat/staff_chat.php?user_id=' . $receiverId);
}
redirect('/chat/user_chat.php');
