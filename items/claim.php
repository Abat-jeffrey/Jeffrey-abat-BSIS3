<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin(['student', 'teacher']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId = (int) ($_POST['item_id'] ?? 0);

    $itemStmt = db()->prepare('SELECT id, claim_status FROM items WHERE id = ? LIMIT 1');
    $itemStmt->execute([$itemId]);
    $item = $itemStmt->fetch();

    if ($item && $item['claim_status'] !== 'claimed') {
        $dup = db()->prepare('SELECT id FROM claims WHERE item_id = ? AND user_id = ? AND status = "pending" LIMIT 1');
        $dup->execute([$itemId, user()['id']]);

        if (!$dup->fetch()) {
            $stmt = db()->prepare('INSERT INTO claims (item_id, user_id, claimant_name, status, created_at) VALUES (?, ?, ?, "pending", NOW())');
            $stmt->execute([$itemId, user()['id'], user()['full_name']]);
            logActivity(user()['id'], 'Created claim request', $itemId);
        }
    }
}

redirect('/dashboard/user.php');
