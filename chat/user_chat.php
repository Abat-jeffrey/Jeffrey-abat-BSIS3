<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin(['student', 'teacher']);

$staff = db()->query("SELECT id, full_name FROM users WHERE role='staff' LIMIT 1")->fetch();
if (!$staff) {
    die('Staff account missing.');
}

include __DIR__ . '/../includes/header.php';
?>
<h4>Chat with Staff</h4>
<div class="card p-3">
    <div id="chat-messages" class="chat-box mb-3"></div>
    <div class="mb-2 d-flex gap-2">
        <button class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('msg').value='I lost an item'">I lost an item</button>
        <button class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('msg').value='I found an item'">I found an item</button>
    </div>
    <form method="post" action="<?= BASE_URL ?>/chat/send.php" enctype="multipart/form-data" class="d-flex gap-2">
        <input type="hidden" name="receiver_id" value="<?= (int) $staff['id'] ?>">
        <input id="msg" class="form-control" name="message" placeholder="Type a message...">
        <input class="form-control" type="file" name="image" accept="image/*" style="max-width:220px;">
        <button class="btn btn-primary">Send</button>
    </form>
</div>
<script>initAutoRefresh('chat-messages', '<?= BASE_URL ?>/chat/fetch.php?with=<?= (int)$staff['id'] ?>');</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
