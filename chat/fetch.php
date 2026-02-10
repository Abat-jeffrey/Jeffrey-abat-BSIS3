<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin(['student', 'teacher', 'staff']);

$with = (int) ($_GET['with'] ?? 0);
$uid = user()['id'];

if (in_array(user()['role'], ['student', 'teacher'], true)) {
    $staff = db()->query("SELECT id FROM users WHERE role='staff' LIMIT 1")->fetch();
    if (!$staff || (int) $staff['id'] !== $with) {
        die('Invalid target.');
    }
}

$stmt = db()->prepare('SELECT * FROM messages WHERE (sender_id=? AND receiver_id=?) OR (sender_id=? AND receiver_id=?) ORDER BY created_at ASC');
$stmt->execute([$uid, $with, $with, $uid]);
$messages = $stmt->fetchAll();

foreach ($messages as $m) {
    $mine = (int) $m['sender_id'] === $uid;
    echo '<div class="chat-bubble ' . ($mine ? 'chat-me' : 'chat-other') . '">';
    if ($m['body']) {
        echo '<div>' . e($m['body']) . '</div>';
    }
    if ($m['image_path']) {
        $img = BASE_URL . '/uploads/chat/' . e($m['image_path']);
        echo '<div><a href="' . $img . '" download>Download image</a><br><img src="' . $img . '" style="max-width:180px;border-radius:8px;"></div>';
    }
    echo '<small>' . e($m['created_at']) . '</small></div>';
}
