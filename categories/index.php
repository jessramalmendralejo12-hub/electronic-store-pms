<?php
require_once __DIR__ . '/../includes/init.php';
require_login();

$errors = [];
$name = '';
$description = '';

// ---- Add a new category ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($name === '' || strlen($name) > 100) {
        $errors[] = 'Category name is required (max 100 characters).';
    } elseif (db_one($conn, 'SELECT id FROM categories WHERE name = ?', 's', [$name])) {
        $errors[] = 'That category already exists.';
    }
    if (strlen($description) > 255) {
        $errors[] = 'Description must be 255 characters or less.';
    }

    if (!$errors) {
        db_execute($conn, 'INSERT INTO categories (name, description) VALUES (?, ?)', 'ss', [$name, $description]);
        set_flash('success', 'Category "' . $name . '" was added.');
        redirect('categories/index.php');
    }
}

$categories = db_select($conn,
    'SELECT c.*, COUNT(p.id) AS product_count
     FROM categories c LEFT JOIN products p ON p.category_id = c.id
     GROUP BY c.id ORDER BY c.name');

$pageSubtitle = 'Organize your products into categories.';
$pageTitle  = 'Categories';
$activePage = 'categories';
require __DIR__ . '/../includes/header.php';
?>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header panel-header"><span><i class="bi bi-plus-circle"></i> Add Category</span></div>
            <div class="card-body">
                <?php if ($errors): ?>
                    <div class="alert alert-danger py-2">
                        <ul class="mb-0 ps-3"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
                    </div>
                <?php endif; ?>
                <form method="post" novalidate>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label" for="name">Category Name *</label>
                        <input type="text" class="form-control" id="name" name="name" maxlength="100" required value="<?= e($name) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" maxlength="255"><?= e($description) ?></textarea>
                    </div>
                    <button class="btn btn-primary w-100"><i class="bi bi-check-lg"></i> Save Category</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header panel-header"><span><i class="bi bi-tags"></i> <?= count($categories) ?> categorie(s)</span></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>#</th><th>Name</th><th>Description</th><th class="text-center">Products</th><th class="text-end">Actions</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($categories as $c): ?>
                        <tr>
                            <td><?= (int) $c['id'] ?></td>
                            <td class="fw-semibold"><?= e($c['name']) ?></td>
                            <td class="text-muted"><?= e($c['description']) ?></td>
                            <td class="text-center"><span class="count-badge"><?= (int) $c['product_count'] ?></span></td>
                            <td class="text-end text-nowrap">
                                <a href="<?= url('categories/edit.php?id=' . (int) $c['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                        data-action="<?= url('categories/delete.php') ?>"
                                        data-id="<?= (int) $c['id'] ?>"
                                        data-name="<?= e($c['name']) ?>"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$categories): ?>
                        <tr><td colspan="5" class="text-center text-muted py-5">No categories yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
