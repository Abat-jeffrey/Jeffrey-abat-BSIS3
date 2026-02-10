<?php
require_once __DIR__ . '/functions.php';
$currentUser = user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/findit.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#"><?= e(APP_NAME) ?></a>
        <?php if ($currentUser): ?>
            <div class="d-flex align-items-center gap-2 text-white">
                <span><?= e($currentUser['full_name']) ?> (<?= strtoupper(e($currentUser['role'])) ?>)</span>
                <a class="btn btn-sm btn-light" href="<?= BASE_URL ?>/auth/logout.php">Logout</a>
            </div>
        <?php endif; ?>
    </div>
</nav>
<div class="container-fluid py-4">
