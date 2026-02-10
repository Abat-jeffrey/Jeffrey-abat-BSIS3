<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin(['staff']);

$users = db()->query("SELECT id, full_name, role FROM users WHERE role IN ('student','teacher') ORDER BY full_name ASC")->fetchAll();
$selected = isset($_GET['user_id']) ? (int) $_GET['user_id'] : ($users[0]['id'] ?? 0);

include __DIR__ . '/../includes/header.php';
?>
<div class="row">
    <div class="col-md-4">
        <div class="card p-3">
            <h5>Students / Teachers</h5>
            <?php foreach ($users as $u): ?>
                <a class="d-block p-2 <?= $selected === (int)$u['id'] ? 'bg-light' : '' ?>" href="?user_id=<?= (int)$u['id'] ?>"><?= e($u['full_name']) ?> (<?= e($u['role']) ?>)</a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card p-3">
            <h5>Conversation</h5>
            <?php if ($selected): ?>
                <div id="chat-messages" class="chat-box mb-3"></div>
                <form method="post" action="<?= BASE_URL ?>/chat/send.php" enctype="multipart/form-data" class="d-flex gap-2">
                    <input type="hidden" name="receiver_id" value="<?= $selected ?>">
                    <input class="form-control" name="message" placeholder="Reply...">
                    <input class="form-control" type="file" name="image" accept="image/*" style="max-width:220px;">
                    <button class="btn btn-primary">Send</button>
                </form>
                <script>initAutoRefresh('chat-messages', '<?= BASE_URL ?>/chat/fetch.php?with=<?= $selected ?>');</script>
            <?php else: ?>
                <p>No users registered yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
