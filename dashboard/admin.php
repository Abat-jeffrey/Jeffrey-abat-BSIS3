<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin(['admin']);

$logs = db()->query('SELECT l.*, u.full_name AS actor_name FROM activity_logs l LEFT JOIN users u ON u.id = l.actor_id ORDER BY l.created_at DESC LIMIT 50')->fetchAll();
$activity = db()->query("SELECT i.item_name, i.status, i.created_at AS date_posted, c.approved_at AS date_claimed, claimant_name FROM items i LEFT JOIN claims c ON c.item_id=i.id AND c.status='approved' ORDER BY i.created_at DESC LIMIT 50")->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="row">
    <div class="col-md-3">
        <div class="sidebar">
            <a class="active" href="<?= BASE_URL ?>/dashboard/admin.php">Dashboard</a>
            <a href="<?= BASE_URL ?>/reports/admin_reports.php">Reports & Analytics</a>
        </div>
    </div>
    <div class="col-md-9">
        <h3>Admin Monitoring Dashboard</h3>
        <div class="card p-3 mb-3">
            <h5>Staff/System Activities</h5>
            <div class="table-responsive"><table class="table"><thead><tr><th>Actor</th><th>Action</th><th>Date</th></tr></thead><tbody>
                <?php foreach ($logs as $row): ?><tr><td><?= e($row['actor_name'] ?? 'System') ?></td><td><?= e($row['action']) ?></td><td><?= e($row['created_at']) ?></td></tr><?php endforeach; ?>
            </tbody></table></div>
        </div>
        <div class="card p-3">
            <h5>Items + Claim Timeline</h5>
            <div class="table-responsive"><table class="table"><thead><tr><th>Item</th><th>Status</th><th>Claimant</th><th>Date Posted</th><th>Date Claimed</th></tr></thead><tbody>
                <?php foreach ($activity as $row): ?><tr><td><?= e($row['item_name']) ?></td><td><?= e($row['status']) ?></td><td><?= e($row['claimant_name'] ?? '-') ?></td><td><?= e($row['date_posted']) ?></td><td><?= e($row['date_claimed'] ?? '-') ?></td></tr><?php endforeach; ?>
            </tbody></table></div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
