<?php
require_once __DIR__ . '/includes/init.php';
require_login();

$limit = LOW_STOCK_LIMIT;

$totalProducts   = (int) db_value($conn, 'SELECT COUNT(*) FROM products');
$totalCategories = (int) db_value($conn, 'SELECT COUNT(*) FROM categories');
$totalStock      = (int) db_value($conn, 'SELECT COALESCE(SUM(stock_quantity), 0) FROM products');
$lowStock        = (int) db_value($conn, 'SELECT COUNT(*) FROM products WHERE stock_quantity > 0 AND stock_quantity <= ?', 'i', [$limit]);
$outOfStock      = (int) db_value($conn, 'SELECT COUNT(*) FROM products WHERE stock_quantity = 0');

$needsRestock = db_select($conn,
    'SELECT p.id, p.name, p.brand, p.stock_quantity, c.name AS category
     FROM products p JOIN categories c ON c.id = p.category_id
     WHERE p.stock_quantity <= ?
     ORDER BY p.stock_quantity ASC, p.name ASC LIMIT 6', 'i', [$limit]);

$recent = db_select($conn,
    'SELECT p.id, p.name, p.brand, p.selling_price, p.date_added, c.name AS category
     FROM products p JOIN categories c ON c.id = p.category_id
     ORDER BY p.date_added DESC, p.id DESC LIMIT 5');

$cards = [
    ['Total Products',   $totalProducts,   'bi-cpu',                  'primary', 'Items in your catalog',   'products/index.php'],
    ['Categories',       $totalCategories, 'bi-tags',                 'info',    'Product groups',          'categories/index.php'],
    ['Available Stocks', $totalStock,      'bi-boxes',                'success', 'Units on hand',           'inventory.php'],
    ['Low Stock',        $lowStock,        'bi-exclamation-triangle', 'warning', 'Running low, reorder',   'products/index.php?status=low'],
    ['Out of Stock',     $outOfStock,      'bi-x-octagon',            'danger',  'Unavailable right now',  'products/index.php?status=out'],
];

$firstName    = explode(' ', trim($_SESSION['full_name'] ?? 'Admin'))[0];
$pageTitle    = 'Dashboard';
$pageSubtitle = 'Welcome back, ' . $firstName . '. Here is your store at a glance.';
$pageActions  = '<a href="' . url('products/add.php') . '" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Product</a>';
$activePage   = 'dashboard';
require __DIR__ . '/includes/header.php';
?>

<div class="row g-3 mb-4">
    <?php foreach ($cards as $c): ?>
        <div class="col-6 col-md-4 col-xl">
            <a href="<?= url($c[5]) ?>" class="card stat-card">
                <div class="card-body">
                    <div>
                        <div class="stat-label"><?= e($c[0]) ?></div>
                        <div class="stat-number"><?= number_format($c[1]) ?></div>
                        <div class="stat-hint"><?= e($c[4]) ?></div>
                    </div>
                    <div class="stat-icon <?= $c[3] ?>"><i class="bi <?= $c[2] ?>"></i></div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header panel-header">
                <span><i class="bi bi-exclamation-triangle"></i> Needs Restocking</span>
                <a href="<?= url('inventory.php') ?>">View inventory &rarr;</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Product</th><th>Category</th><th class="text-center">Stock</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($needsRestock as $p): $s = stock_status((int) $p['stock_quantity']); ?>
                        <tr>
                            <td><?= e($p['name']) ?><div class="small text-muted"><?= e($p['brand']) ?></div></td>
                            <td><?= e($p['category']) ?></td>
                            <td class="text-center"><?= (int) $p['stock_quantity'] ?></td>
                            <td><span class="status-badge status-<?= $s[1] ?>"><?= $s[0] ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$needsRestock): ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">All products are well stocked.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header panel-header">
                <span><i class="bi bi-clock-history"></i> Recently Added</span>
                <a href="<?= url('products/index.php') ?>">View all &rarr;</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Product</th><th>Category</th><th class="text-end">Price</th><th>Added</th></tr></thead>
                    <tbody>
                    <?php foreach ($recent as $p): ?>
                        <tr>
                            <td><?= e($p['name']) ?><div class="small text-muted"><?= e($p['brand']) ?></div></td>
                            <td><?= e($p['category']) ?></td>
                            <td class="text-end"><?= money($p['selling_price']) ?></td>
                            <td><?= e(date('M d, Y', strtotime($p['date_added']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$recent): ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">No products yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
