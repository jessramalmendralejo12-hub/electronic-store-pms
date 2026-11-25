<?php
// Expects $pageTitle and $activePage to be set before including this file.
$pageTitle    = $pageTitle    ?? 'Dashboard';
$pageSubtitle = $pageSubtitle ?? '';
$pageActions  = $pageActions  ?? '';
$activePage   = $activePage   ?? '';
$flash        = get_flash();
$navGroups = [
    'Overview' => ['dashboard'  => ['Dashboard',  'bi-speedometer2', 'dashboard.php']],
    'Catalog'  => ['products'   => ['Products',   'bi-cpu',          'products/index.php'],
                   'categories' => ['Categories', 'bi-tags',         'categories/index.php']],
    'Stock'    => ['inventory'  => ['Inventory',  'bi-boxes',        'inventory.php']],
];
$userName = $_SESSION['full_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | TechMart Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>

<!-- Sidebar (becomes a slide-in menu on small screens) -->
<div class="offcanvas-lg offcanvas-start sidebar" tabindex="-1" id="sidebar">
    <div class="offcanvas-header d-lg-none">
        <span class="text-white fw-bold">Menu</span>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#sidebar"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-0">
        <a href="<?= url('dashboard.php') ?>" class="sidebar-brand">
            <span class="brand-logo"><i class="bi bi-motherboard"></i></span>
            <span>
                <div class="brand-name">TechMart</div>
                <div class="brand-sub">Admin Console</div>
            </span>
        </a>

        <nav class="sidebar-nav flex-grow-1">
            <?php foreach ($navGroups as $navGroup => $navGroupItems): ?>
                <div class="nav-label"><?= e($navGroup) ?></div>
                <?php foreach ($navGroupItems as $navKey => $navItem): ?>
                    <a class="nav-link <?= $activePage === $navKey ? 'active' : '' ?>" href="<?= url($navItem[2]) ?>">
                        <i class="bi <?= $navItem[1] ?>"></i> <?= $navItem[0] ?>
                    </a>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </nav>

        <div class="sidebar-user">
            <div class="avatar"><?= e(strtoupper(substr($userName, 0, 1))) ?></div>
            <div>
                <div class="name"><?= e($userName) ?></div>
                <div class="role">Administrator</div>
            </div>
            <a class="logout" href="<?= url('logout.php') ?>" title="Logout"><i class="bi bi-box-arrow-right"></i></a>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="main-content">
    <nav class="topbar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-label="Open menu">
                <i class="bi bi-list"></i>
            </button>
            <span class="text-muted small"><i class="bi bi-house-door"></i> TechMart &rsaquo; <span class="text-dark fw-semibold"><?= e($pageTitle) ?></span></span>
        </div>
        <span class="text-muted small d-none d-sm-inline"><i class="bi bi-calendar3"></i> <?= date('l, F j, Y') ?></span>
    </nav>

    <main class="p-3 p-md-4">
        <div class="page-header">
            <div>
                <h1><?= e($pageTitle) ?></h1>
                <?php if ($pageSubtitle !== ''): ?><p><?= e($pageSubtitle) ?></p><?php endif; ?>
            </div>
            <?php if ($pageActions !== ''): ?><div><?= $pageActions ?></div><?php endif; ?>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show auto-dismiss" role="alert">
                <?= e($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
