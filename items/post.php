<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin(['staff']);

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image = uploadImage('image', __DIR__ . '/../uploads/items');
    $stmt = db()->prepare('INSERT INTO items (item_name, description, category, location, status, image_path, claim_status, posted_by, created_at) VALUES (?, ?, ?, ?, ?, ?, "open", ?, NOW())');
    $stmt->execute([
        trim($_POST['item_name']),
        trim($_POST['description']),
        trim($_POST['category']),
        trim($_POST['location']),
        $_POST['status'] === 'found' ? 'found' : 'lost',
        $image,
        user()['id'],
    ]);
    $itemId = (int) db()->lastInsertId();
    logActivity(user()['id'], 'Posted item', $itemId);
    $msg = 'Item posted successfully.';
}

include __DIR__ . '/../includes/header.php';
?>
<div class="row">
    <div class="col-md-3"><div class="sidebar"><a href="<?= BASE_URL ?>/dashboard/staff.php">Dashboard</a><a class="active" href="#">Post Item</a><a href="<?= BASE_URL ?>/items/claims.php">Claims Management</a></div></div>
    <div class="col-md-9">
        <div class="card p-3">
            <h4>Post Lost / Found Item</h4>
            <?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <div class="row g-2">
                    <div class="col-md-6"><label>Item Name</label><input class="form-control" name="item_name" required></div>
                    <div class="col-md-6"><label>Category</label><input class="form-control" name="category" required></div>
                    <div class="col-md-6"><label>Location</label><input class="form-control" name="location" required></div>
                    <div class="col-md-6"><label>Status</label><select class="form-select" name="status"><option value="lost">Lost</option><option value="found">Found</option></select></div>
                    <div class="col-md-12"><label>Description</label><textarea class="form-control" name="description" required></textarea></div>
                    <div class="col-md-12"><label>Image</label><input class="form-control" type="file" name="image" accept="image/*"></div>
                    <div class="col-md-12"><button class="btn btn-primary">Save Item</button></div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
