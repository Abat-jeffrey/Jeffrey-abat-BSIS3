<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin(['staff']);

$counts = [
    'lost' => (int) db()->query("SELECT COUNT(*) FROM items WHERE status='lost'")->fetchColumn(),
    'found' => (int) db()->query("SELECT COUNT(*) FROM items WHERE status='found'")->fetchColumn(),
    'claims' => (int) db()->query("SELECT COUNT(*) FROM claims")->fetchColumn(),
];

include __DIR__ . '/../includes/header.php';
?>
<div class="row">
    <div class="col-md-3">
        <div class="sidebar">
            <a class="active" href="<?= BASE_URL ?>/dashboard/staff.php">Dashboard</a>
            <a href="<?= BASE_URL ?>/items/post.php">Post Item</a>
            <a href="<?= BASE_URL ?>/items/claims.php">Claims Management</a>
            <a href="<?= BASE_URL ?>/chat/staff_chat.php">Open Chat</a>
        </div>
    </div>
    <div class="col-md-9">
        <h3 class="mb-3">Staff Dashboard</h3>
        <div class="row g-3 mb-3">
            <div class="col-md-4"><div class="card p-3"><h6>Total Lost Items</h6><h2><?= $counts['lost'] ?></h2></div></div>
            <div class="col-md-4"><div class="card p-3"><h6>Total Found Items</h6><h2><?= $counts['found'] ?></h2></div></div>
            <div class="col-md-4"><div class="card p-3"><h6>Total Claims</h6><h2><?= $counts['claims'] ?></h2></div></div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-primary" href="<?= BASE_URL ?>/items/post.php">Post Item</a>
            <a class="btn btn-dark" href="<?= BASE_URL ?>/chat/staff_chat.php">Open Chat</a>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
