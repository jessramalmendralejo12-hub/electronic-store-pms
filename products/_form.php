<?php
// Shared form used by add.php and edit.php.
// Needs: $product (array), $categories (array), $errors (array), $submitLabel (string)
?>
<?php if ($errors): ?>
    <div class="alert alert-danger">
        <strong>Please fix the following:</strong>
        <ul class="mb-0">
            <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" novalidate>
    <?= csrf_field() ?>
    <div class="card">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="name">Product Name *</label>
                    <input type="text" class="form-control" id="name" name="name" maxlength="150" required value="<?= e($product['name']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="category_id">Category *</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">-- Select category --</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= (int) $c['id'] ?>" <?= (int) $product['category_id'] === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="brand">Brand *</label>
                    <input type="text" class="form-control" id="brand" name="brand" maxlength="100" required value="<?= e($product['brand']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="model_number">Model Number *</label>
                    <input type="text" class="form-control" id="model_number" name="model_number" maxlength="100" required value="<?= e($product['model_number']) ?>">
                </div>
                <div class="col-12">
                    <label class="form-label" for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" maxlength="1000"><?= e($product['description']) ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="purchase_price">Purchase Price (₱) *</label>
                    <input type="number" step="0.01" min="0" class="form-control" id="purchase_price" name="purchase_price" required value="<?= e($product['purchase_price']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="selling_price">Selling Price (₱) *</label>
                    <input type="number" step="0.01" min="0" class="form-control" id="selling_price" name="selling_price" required value="<?= e($product['selling_price']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="stock_quantity">Stock Quantity *</label>
                    <input type="number" step="1" min="0" class="form-control" id="stock_quantity" name="stock_quantity" required value="<?= e($product['stock_quantity']) ?>">
                </div>
                <div class="col-12">
                    <label class="form-label" for="image">Product Image <span class="text-muted small">(JPG, PNG, GIF or WEBP, max 2MB)</span></label>
                    <div class="d-flex align-items-center gap-3">
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?= url('uploads/' . rawurlencode($product['image'])) ?>" class="product-thumb large" alt="Current image" id="imagePreview">
                        <?php else: ?>
                            <img src="" class="product-thumb large d-none" alt="Preview" id="imagePreview">
                        <?php endif; ?>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    </div>
                    <?php if (!empty($product['image'])): ?>
                        <div class="form-text">Leave empty to keep the current image.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="card-footer panel-footer d-flex gap-2 justify-content-end">
            <a href="<?= url('products/index.php') ?>" class="btn btn-light">Cancel</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> <?= e($submitLabel) ?></button>
        </div>
    </div>
</form>
