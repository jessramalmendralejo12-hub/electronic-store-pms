<?php
require_once __DIR__ . '/../includes/init.php';
require_login();

// ---- Search & filter inputs (GET) ----
$q      = trim($_GET['q'] ?? '');
$cat    = (int) ($_GET['category'] ?? 0);
$status = $_GET['status'] ?? '';

$where  = [];
$types  = '';
$params = [];
$limit  = (int) LOW_STOCK_LIMIT;

if ($q !== '') {
    $where[] = '(p.name LIKE ? OR p.brand LIKE ? OR p.model_number LIKE ? OR c.name LIKE ?)';
    $like    = '%' . $q . '%';
    $types  .= 'ssss';
    array_push($params, $like, $like, $like, $like);
}
if ($cat > 0) {
    $where[] = 'p.category_id = ?';
    $types  .= 'i';
    $params[] = $cat;
}
if ($status === 'in')  { $where[] = "p.stock_quantity > $limit"; }
if ($status === 'low') { $where[] = "p.stock_quantity BETWEEN 1 AND $limit"; }
if ($status === 'out') { $where[] = 'p.stock_quantity = 0'; }

$sql = 'SELECT p.*, c.name AS category_name
        FROM products p JOIN categories c ON c.id = p.category_id'
     . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
     . ' ORDER BY p.date_added DESC, p.id DESC';

$products   = db_select($conn, $sql, $types, $params);
$categories = db_select($conn, 'SELECT id, name FROM categories ORDER BY name');

$pageTitle    = 'Products';
$pageSubtitle = 'Manage your electronic products, prices and stock.';
$pageActions  = '<a href="' . url('products/add.php') . '" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Product</a>';
$activePage   = 'products';
require __DIR__ . '/../includes/header.php';
?>

<div class="card mb-3">
    <div class="card-body">
        <form method="get" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label small mb-1">Search</label>
                <input type="text" name="q" class="form-control" placeholder="Name, brand, model or category" value="<?= e($q) ?>">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small mb-1">Category</label>
                <select name="category" class="form-select">
                    <option value="0">All categories</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int) $c['id'] ?>" <?= $cat === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1">Stock</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="in"  <?= $status === 'in'  ? 'selected' : '' ?>>In Stock</option>
                    <option value="low" <?= $status === 'low' ? 'selected' : '' ?>>Low Stock</option>
                    <option value="out" <?= $status === 'out' ? 'selected' : '' ?>>Out of Stock</option>
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button class="btn btn-primary flex-fill"><i class="bi bi-search"></i> Search</button>
                <a href="<?= url('products/index.php') ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header panel-header">
        <span><i class="bi bi-cpu"></i> <?= count($products) ?> product(s) found</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th><th>Image</th><th>Product</th><th>Category</th>
                    <th class="text-end">Purchase</th><th class="text-end">Selling</th>
                    <th class="text-center">Stock</th><th>Status</th><th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $p): $s = stock_status((int) $p['stock_quantity']); ?>
                <tr>
                    <td><?= (int) $p['id'] ?></td>
                    <td>
                        <?php if ($p['image']): ?>
                            <img src="<?= url('uploads/' . rawurlencode($p['image'])) ?>" class="product-thumb" alt="">
                        <?php else: ?>
                            <div class="product-thumb placeholder"><i class="bi bi-image"></i></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="fw-semibold"><?= e($p['name']) ?></div>
                        <div class="small text-muted"><?= e($p['brand']) ?> &middot; <?= e($p['model_number']) ?></div>
                    </td>
                    <td><?= e($p['category_name']) ?></td>
                    <td class="text-end"><?= money($p['purchase_price']) ?></td>
                    <td class="text-end"><?= money($p['selling_price']) ?></td>
                    <td class="text-center"><?= (int) $p['stock_quantity'] ?></td>
                    <td><span class="status-badge status-<?= $s[1] ?>"><?= $s[0] ?></span></td>
                    <td class="text-end text-nowrap">
                        <a href="<?= url('products/edit.php?id=' . (int) $p['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                data-bs-toggle="modal" data-bs-target="#deleteModal"
                                data-action="<?= url('products/delete.php') ?>"
                                data-id="<?= (int) $p['id'] ?>"
                                data-name="<?= e($p['name']) ?>"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$products): ?>
                <tr><td colspan="9" class="text-center text-muted py-5">No products found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
