<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin(['admin']);

$year = (int) ($_GET['year'] ?? date('Y'));
$month = (int) ($_GET['month'] ?? 0);
$filterSql = $month > 0 ? ' AND MONTH(created_at) = :month' : '';

$stmt1 = db()->prepare("SELECT MONTH(created_at) m, COUNT(*) c FROM items WHERE status='lost' AND YEAR(created_at)=:year {$filterSql} GROUP BY MONTH(created_at)");
$stmt1->bindValue(':year', $year, PDO::PARAM_INT);
if ($month > 0) $stmt1->bindValue(':month', $month, PDO::PARAM_INT);
$stmt1->execute();
$lost = array_fill(1, 12, 0);
foreach ($stmt1->fetchAll() as $row) $lost[(int)$row['m']] = (int)$row['c'];

$stmt2 = db()->prepare("SELECT MONTH(created_at) m, COUNT(*) c FROM items WHERE status='found' AND YEAR(created_at)=:year {$filterSql} GROUP BY MONTH(created_at)");
$stmt2->bindValue(':year', $year, PDO::PARAM_INT);
if ($month > 0) $stmt2->bindValue(':month', $month, PDO::PARAM_INT);
$stmt2->execute();
$found = array_fill(1, 12, 0);
foreach ($stmt2->fetchAll() as $row) $found[(int)$row['m']] = (int)$row['c'];

include __DIR__ . '/../includes/header.php';
?>
<div class="row">
    <div class="col-md-3"><div class="sidebar"><a href="<?= BASE_URL ?>/dashboard/admin.php">Dashboard</a><a class="active" href="#">Reports & Analytics</a></div></div>
    <div class="col-md-9">
        <div class="card p-3 mb-3">
            <h4>Monthly Reports</h4>
            <form method="get" class="d-flex gap-2">
                <input class="form-control" type="number" name="year" value="<?= $year ?>">
                <select class="form-select" name="month">
                    <option value="0">All Months</option>
                    <?php for ($m = 1; $m <= 12; $m++): ?><option value="<?= $m ?>" <?= $month === $m ? 'selected' : '' ?>><?= $m ?></option><?php endfor; ?>
                </select>
                <button class="btn btn-primary">Filter</button>
            </form>
        </div>
        <div class="card p-3"><canvas id="itemsChart"></canvas></div>
    </div>
</div>
<script>
const labels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
new Chart(document.getElementById('itemsChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            { label: 'Lost Items', backgroundColor: '#f59e0b', data: <?= json_encode(array_values($lost)) ?> },
            { label: 'Found Items', backgroundColor: '#10b981', data: <?= json_encode(array_values($found)) ?> }
        ]
    }
});
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
