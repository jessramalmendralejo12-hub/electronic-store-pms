<?php
require_once __DIR__ . '/includes/init.php';
require_login();

$limit = (int) LOW_STOCK_LIMIT;

// ---- Quick stock update ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id  = (int) ($_POST['product_id'] ?? 0);
    $qty = filter_var($_POST['stock_quantity'] ?? '', FILTER_VALIDATE_INT);

    if ($qty === false || $qty < 0) {
        set_flash('danger', 'Stock quantity must be a whole number of 0 or more.');
    } elseif (!db_one($conn, 'SELECT id FROM products WHERE id = ?', 'i', [$id])) {
        set_flash('danger', 'Product not found.');
    } else {
        db_execute($conn, 'UPDATE products SET stock_quantity = ? WHERE id = ?', 'ii', [$qty, $id]);
        set_flash('success', 'Stock quantity updated.');
    }
    redirect('inventory.php' . (isset($_GET['status']) ? '?status=' . urlencode($_GET['status']) : ''));
}

$status = $_GET['status'] ?? '';
$where  = '';
if ($status === 'in')  { $where = "WHERE p.stock_quantity > $limit"; }
if ($status === 'low') { $where = "WHERE p.stock_quantity BETWEEN 1 AND $limit"; }
if ($status === 'out') { $where = 'WHERE p.stock_quantity = 0'; }

$items = db_select($conn,
    "SELECT p.id, p.name, p.brand, p.model_number, p.stock_quantity, p.purchase_price, c.name AS category_name
     FROM products p JOIN categories c ON c.id = p.category_id
     $where ORDER BY p.stock_quantity ASC, p.name ASC");

$inventoryValue = (float) db_value($conn, 'SELECT COALESCE(SUM(stock_quantity * purchase_price), 0) FROM products');

$tabs = ['' => 'All', 'in' => 'In Stock', 'low' => 'Low Stock', 'out' => 'Out of Stock'];

$pageSubtitle = 'Monitor stock levels and update quantities quickly.';
$pageTitle  = 'Inventory';
$activePage = 'inventory';
require __DIR__ . '/includes/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <ul class="nav nav-pills">
        <?php foreach ($tabs as $key => $label): ?>
            <li class="nav-item">
                <a class="nav-link <?= $status === (string) $key ? 'active' : '' ?>"
                   href="<?= url('inventory.php' . ($key !== '' ? '?status=' . $key : '')) ?>"><?= $label ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
    <div class="text-muted small">Stock value (at purchase price): <strong><?= money($inventoryValue) ?></strong></div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Product</th><th>Category</th><th class="text-center">Quantity</th><th>Status</th><th style="min-width:210px">Update Quantity</th></tr>
            </thead>
            <tbody>
            <?php foreach ($items as $p): $s = stock_status((int) $p['stock_quantity']); ?>
                <tr>
                    <td>
                        <div class="fw-semibold"><?= e($p['name']) ?></div>
                        <div class="small text-muted"><?= e($p['brand']) ?> &middot; <?= e($p['model_number']) ?></div>
                    </td>
                    <td><?= e($p['category_name']) ?></td>
                    <td class="text-center fw-semibold"><?= (int) $p['stock_quantity'] ?></td>
                    <td><span class="status-badge status-<?= $s[1] ?>"><?= $s[0] ?></span></td>
                    <td>
                        <form method="post" class="input-group input-group-sm">
                            <?= csrf_field() ?>
                            <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
                            <input type="number" name="stock_quantity" min="0" step="1" class="form-control" value="<?= (int) $p['stock_quantity'] ?>" required>
                            <button class="btn btn-outline-primary">Save</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$items): ?>
                <tr><td colspan="5" class="text-center text-muted py-5">No products in this list.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
