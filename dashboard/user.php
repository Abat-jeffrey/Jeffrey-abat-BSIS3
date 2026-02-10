<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin(['student', 'teacher']);

$q = trim($_GET['q'] ?? '');
$status = $_GET['status'] ?? '';
$params = [];
$sql = "SELECT * FROM items WHERE 1=1";
if (in_array($status, ['lost', 'found'], true)) {
    $sql .= ' AND status = ?';
    $params[] = $status;
}
if ($q !== '') {
    $sql .= ' AND (item_name LIKE ? OR category LIKE ? OR location LIKE ?)';
    $like = "%{$q}%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}
$sql .= ' ORDER BY created_at DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>User Dashboard</h3>
    <a class="btn btn-outline-primary" href="<?= BASE_URL ?>/dashboard/profile.php">My Profile</a>
</div>
<div class="card p-3 mb-3">
    <div class="d-flex gap-2 mb-2">
        <a class="btn btn-sm btn-<?= $status === 'lost' ? 'primary' : 'outline-primary' ?>" href="?status=lost">Lost Items</a>
        <a class="btn btn-sm btn-<?= $status === 'found' ? 'primary' : 'outline-primary' ?>" href="?status=found">Found Items</a>
        <a class="btn btn-sm btn-outline-secondary" href="<?= BASE_URL ?>/dashboard/user.php">All</a>
    </div>
    <form class="d-flex gap-2" method="get">
        <input type="hidden" name="status" value="<?= e($status) ?>">
        <input class="form-control" name="q" placeholder="Search item/category/location" value="<?= e($q) ?>">
        <button class="btn btn-primary">Search</button>
    </form>
</div>
<div class="card p-3">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Image</th><th>Item</th><th>Category</th><th>Location</th><th>Date</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php if ($item['image_path']): ?><img class="listing-image" src="<?= BASE_URL ?>/uploads/items/<?= e($item['image_path']) ?>"><?php endif; ?></td>
                    <td><?= e($item['item_name']) ?><br><small><?= e($item['description']) ?></small></td>
                    <td><?= e($item['category']) ?></td>
                    <td><?= e($item['location']) ?></td>
                    <td><?= e($item['created_at']) ?></td>
                    <td><span class="badge bg-<?= $item['claim_status'] === 'claimed' ? 'success' : 'warning text-dark' ?>"><?= e(ucfirst($item['claim_status'])) ?></span></td>
                    <td>
                        <?php if ($item['claim_status'] !== 'claimed'): ?>
                            <form method="post" action="<?= BASE_URL ?>/items/claim.php">
                                <input type="hidden" name="item_id" value="<?= (int)$item['id'] ?>">
                                <button class="btn btn-sm btn-primary">Claim</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<a class="chat-float" href="<?= BASE_URL ?>/chat/user_chat.php" title="Chat with Staff">💬</a>
<?php include __DIR__ . '/../includes/footer.php'; ?>
