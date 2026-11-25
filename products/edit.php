<?php
require_once __DIR__ . '/../includes/init.php';
require_login();
require_once __DIR__ . '/validate.php';

$id      = (int) ($_GET['id'] ?? 0);
$current = db_one($conn, 'SELECT * FROM products WHERE id = ?', 'i', [$id]);
if (!$current) {
    set_flash('danger', 'Product not found.');
    redirect('products/index.php');
}

$categories = db_select($conn, 'SELECT id, name FROM categories ORDER BY name');
$errors     = [];
$product    = $current;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    [$product, $errors] = read_product_form($conn);
    $product['image'] = $current['image'];

    $newImage = null;
    if (!$errors) {
        $newImage = upload_image($_FILES['image'] ?? ['error' => UPLOAD_ERR_NO_FILE], $errors);
    }
    if (!$errors) {
        $imageToSave = $newImage ?? $current['image'];
        db_execute($conn,
            'UPDATE products SET name = ?, category_id = ?, brand = ?, model_number = ?, description = ?,
                    purchase_price = ?, selling_price = ?, stock_quantity = ?, image = ?
             WHERE id = ?',
            'sisssddisi',
            [$product['name'], $product['category_id'], $product['brand'], $product['model_number'], $product['description'],
             (float) $product['purchase_price'], (float) $product['selling_price'], (int) $product['stock_quantity'], $imageToSave, $id]);

        if ($newImage) {
            delete_image($current['image']); // remove the old picture
        }
        set_flash('success', 'Product "' . $product['name'] . '" was updated.');
        redirect('products/index.php');
    }
}

$pageSubtitle = 'Update the product details below.';
$pageTitle   = 'Edit Product';
$activePage  = 'products';
$submitLabel = 'Update Product';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/_form.php';
require __DIR__ . '/../includes/footer.php';
