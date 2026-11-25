<?php
require_once __DIR__ . '/../includes/init.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('categories/index.php');
}
csrf_check();

$id       = (int) ($_POST['id'] ?? 0);
$category = db_one($conn, 'SELECT name FROM categories WHERE id = ?', 'i', [$id]);

if (!$category) {
    set_flash('danger', 'Category not found.');
} else {
    $used = (int) db_value($conn, 'SELECT COUNT(*) FROM products WHERE category_id = ?', 'i', [$id]);
    if ($used > 0) {
        // Foreign key would block this anyway; we explain it nicely.
        set_flash('danger', 'Cannot delete "' . $category['name'] . '" because it still has ' . $used . ' product(s). Move or delete them first.');
    } else {
        db_execute($conn, 'DELETE FROM categories WHERE id = ?', 'i', [$id]);
        set_flash('success', 'Category "' . $category['name'] . '" was deleted.');
    }
}
redirect('categories/index.php');
