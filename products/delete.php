<?php
require_once __DIR__ . '/../includes/init.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('products/index.php');
}
csrf_check();

$id      = (int) ($_POST['id'] ?? 0);
$product = db_one($conn, 'SELECT name, image FROM products WHERE id = ?', 'i', [$id]);

if ($product) {
    db_execute($conn, 'DELETE FROM products WHERE id = ?', 'i', [$id]);
    delete_image($product['image']);
    set_flash('success', 'Product "' . $product['name'] . '" was deleted.');
} else {
    set_flash('danger', 'Product not found.');
}
redirect('products/index.php');
