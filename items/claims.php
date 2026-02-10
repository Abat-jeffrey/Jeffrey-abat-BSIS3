<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin(['staff']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $claimId = (int) $_POST['claim_id'];
    $itemId = (int) $_POST['item_id'];
    $claimantName = trim($_POST['claimant_name']);

    $up1 = db()->prepare('UPDATE claims SET claimant_name = ?, status = "approved", approved_at = NOW(), approved_by = ? WHERE id = ?');
    $up1->execute([$claimantName, user()['id'], $claimId]);

    $up2 = db()->prepare('UPDATE items SET claim_status = "claimed" WHERE id = ?');
    $up2->execute([$itemId]);

    logActivity(user()['id'], 'Approved claim', $itemId);
}

$claims = db()->query('SELECT c.*, i.item_name, i.status AS item_status FROM claims c JOIN items i ON i.id = c.item_id ORDER BY c.created_at DESC')->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="row">
    <div class="col-md-3"><div class="sidebar"><a href="<?= BASE_URL ?>/dashboard/staff.php">Dashboard</a><a href="<?= BASE_URL ?>/items/post.php">Post Item</a><a class="active" href="#">Claims Management</a></div></div>
    <div class="col-md-9">
        <div class="card p-3">
            <h4>Claims Management</h4>
            <div class="table-responsive"><table class="table"><thead><tr><th>Item</th><th>Type</th><th>Claimant</th><th>Status</th><th>Requested</th><th>Action</th></tr></thead><tbody>
                <?php foreach ($claims as $c): ?>
                    <tr>
                        <td><?= e($c['item_name']) ?></td>
                        <td><?= e(ucfirst($c['item_status'])) ?></td>
                        <td><?= e($c['claimant_name']) ?></td>
                        <td><?= e($c['status']) ?></td>
                        <td><?= e($c['created_at']) ?></td>
                        <td>
                            <?php if ($c['status'] === 'pending'): ?>
                            <form method="post" class="d-flex gap-2">
                                <input type="hidden" name="claim_id" value="<?= (int)$c['id'] ?>">
                                <input type="hidden" name="item_id" value="<?= (int)$c['item_id'] ?>">
                                <input class="form-control form-control-sm" name="claimant_name" value="<?= e($c['claimant_name']) ?>" required>
                                <button class="btn btn-sm btn-success">Approve</button>
                            </form>
                            <?php else: ?>Approved<?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody></table></div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
