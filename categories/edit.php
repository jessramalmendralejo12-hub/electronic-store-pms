<?php
require_once __DIR__ . '/../includes/init.php';
require_login();

$id       = (int) ($_GET['id'] ?? 0);
$category = db_one($conn, 'SELECT * FROM categories WHERE id = ?', 'i', [$id]);
if (!$category) {
    set_flash('danger', 'Category not found.');
    redirect('categories/index.php');
}

$errors      = [];
$name        = $category['name'];
$description = $category['description'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($name === '' || strlen($name) > 100) {
        $errors[] = 'Category name is required (max 100 characters).';
    } elseif (db_one($conn, 'SELECT id FROM categories WHERE name = ? AND id <> ?', 'si', [$name, $id])) {
        $errors[] = 'Another category already uses that name.';
    }
    if (strlen($description) > 255) {
        $errors[] = 'Description must be 255 characters or less.';
    }

    if (!$errors) {
        db_execute($conn, 'UPDATE categories SET name = ?, description = ? WHERE id = ?', 'ssi', [$name, $description, $id]);
        set_flash('success', 'Category "' . $name . '" was updated.');
        redirect('categories/index.php');
    }
}

$pageSubtitle = 'Rename the category or update its description.';
$pageTitle  = 'Edit Category';
$activePage = 'categories';
require __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
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
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="<?= url('categories/index.php') ?>" class="btn btn-light">Cancel</a>
                        <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
