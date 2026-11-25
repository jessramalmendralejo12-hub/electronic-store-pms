<?php
// Reads the product form from $_POST, validates it, and returns [$data, $errors].
function read_product_form(mysqli $conn): array
{
    $data = [
        'name'           => trim($_POST['name'] ?? ''),
        'category_id'    => (int) ($_POST['category_id'] ?? 0),
        'brand'          => trim($_POST['brand'] ?? ''),
        'model_number'   => trim($_POST['model_number'] ?? ''),
        'description'    => trim($_POST['description'] ?? ''),
        'purchase_price' => trim($_POST['purchase_price'] ?? ''),
        'selling_price'  => trim($_POST['selling_price'] ?? ''),
        'stock_quantity' => trim($_POST['stock_quantity'] ?? ''),
        'image'          => null,
    ];
    $errors = [];

    if ($data['name'] === '' || strlen($data['name']) > 150) {
        $errors[] = 'Product name is required (max 150 characters).';
    }
    if ($data['category_id'] <= 0 || !db_one($conn, 'SELECT id FROM categories WHERE id = ?', 'i', [$data['category_id']])) {
        $errors[] = 'Please choose a valid category.';
    }
    if ($data['brand'] === '' || strlen($data['brand']) > 100) {
        $errors[] = 'Brand is required (max 100 characters).';
    }
    if ($data['model_number'] === '' || strlen($data['model_number']) > 100) {
        $errors[] = 'Model number is required (max 100 characters).';
    }
    if (strlen($data['description']) > 1000) {
        $errors[] = 'Description must be 1000 characters or less.';
    }
    if (!is_numeric($data['purchase_price']) || (float) $data['purchase_price'] < 0) {
        $errors[] = 'Purchase price must be a number of 0 or more.';
    }
    if (!is_numeric($data['selling_price']) || (float) $data['selling_price'] < 0) {
        $errors[] = 'Selling price must be a number of 0 or more.';
    }
    if (filter_var($data['stock_quantity'], FILTER_VALIDATE_INT) === false || (int) $data['stock_quantity'] < 0) {
        $errors[] = 'Stock quantity must be a whole number of 0 or more.';
    }
    return [$data, $errors];
}
