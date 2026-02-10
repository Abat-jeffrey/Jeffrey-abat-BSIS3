<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin(['student', 'teacher']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId = (int) ($_POST['item_id'] ?? 0);
    $stmt = db()->prepare('INSERT INTO claims (item_id, user_id, claimant_name, status, created_at) VALUES (?, ?, ?, "pending", NOW())');
    $stmt->execute([$itemId, user()['id'], user()['full_name']]);
    logActivity(user()['id'], 'Created claim request', $itemId);
}

redirect('/dashboard/user.php');
