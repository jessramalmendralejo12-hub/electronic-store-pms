<?php
require_once __DIR__ . '/../includes/init.php';
require_login();
require_once __DIR__ . '/validate.php';

$categories = db_select($conn, 'SELECT id, name FROM categories ORDER BY name');
$errors     = [];
$product    = ['name' => '', 'category_id' => 0, 'brand' => '', 'model_number' => '', 'description' => '',
               'purchase_price' => '', 'selling_price' => '', 'stock_quantity' => '0', 'image' => null];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    [$product, $errors] = read_product_form($conn);

    if (!$errors) {
        $image = upload_image($_FILES['image'] ?? ['error' => UPLOAD_ERR_NO_FILE], $errors);
    }
    if (!$errors) {
        db_execute($conn,
            'INSERT INTO products (name, category_id, brand, model_number, description, purchase_price, selling_price, stock_quantity, image)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            'sisssddis',
            [$product['name'], $product['category_id'], $product['brand'], $product['model_number'], $product['description'],
             (float) $product['purchase_price'], (float) $product['selling_price'], (int) $product['stock_quantity'], $image]);
        set_flash('success', 'Product "' . $product['name'] . '" was added successfully.');
        redirect('products/index.php');
    }
}

$pageSubtitle = 'Fill in the details of the new product.';
$pageTitle  = 'Add Product';
$activePage = 'products';
$submitLabel = 'Save Product';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/_form.php';
require __DIR__ . '/../includes/footer.php';
