<?php
require_once __DIR__ . '/../_base.php';
$title = 'Add Product';
$_title = 'Add Product';

// Handle form submission
if (is_post()) {
    $product_name = req('product_name');
    $price = req('price');
    $stock_quantity = req('stock_quantity');
    $category_id = req('category_id');
    $description = req('description');
    $f = get_file('image');

    // Validate product name
    if (!$product_name) {
        $_err['product_name'] = 'Required';
    } else if (strlen($product_name) > 255) {
        $_err['product_name'] = 'Maximum 255 characters only';
    }

    // Validate price
    if (!$price) {
        $_err['price'] = 'Required';
    } else if ($price < 0) {
        $_err['price'] = 'Price cannot be negative';
    }

    // Validate stock quantity
    if ($stock_quantity === '' || $stock_quantity === null) {
        $_err['stock_quantity'] = 'Required';
    } else if ($stock_quantity < 0) {
        $_err['stock_quantity'] = 'Stock cannot be negative';
    }

    // Validate category
    if (!$category_id) {
        $_err['category_id'] = 'Required';
    } else if (!is_exists($category_id, 'category', 'category_id')) {
        $_err['category_id'] = 'Invalid category';
    }

    // Validate image
    if (!$f) {
        $_err['image'] = 'Required';
    } else if (!str_starts_with($f->type, 'image/')) {
        $_err['image'] = 'Must be an image';
    } else if ($f->size > 5 * 1024 * 1024) {
        $_err['image'] = 'Maximum 5MB';
    }

    // If no errors, save the product
    if (!$_err) {
        $photo = save_photo($f, '../product_img');

        $stm = $_db->prepare('INSERT INTO product (product_name, price, stock_quantity, category_id, description, image) VALUES (?, ?, ?, ?, ?, ?)');
        $stm->execute([$product_name, $price, $stock_quantity, $category_id, $description, $photo]);

        temp('info', 'Product added successfully!');
        redirect('../admin/admin_panel.php?page=add_product');
    }
}

$categories = $_db->query('SELECT * FROM category ORDER BY category_name')->fetchAll();

if (empty($categories)) {
    echo '<div class="error-message">Please add categories first before adding products.</div>';
}
?>

<!-- Flash Message -->
<div id="info"><?= temp('info') ?></div>

<!-- Add Product Form -->
<div class="add-product-form">
    <h2>Add New Product</h2>

    <?php if (!empty($categories)): ?>
    <form action="" method="POST" enctype="multipart/form-data">
        
        <label>Product Name *</label>
        <input type="text" name="product_name" placeholder="Enter product name" value="<?= encode($product_name ?? '') ?>" required>
        <?= err('product_name') ?>

        <label>Price (RM) *</label>
        <input type="number" name="price" min="0" step="0.01" placeholder="0.00" value="<?= encode($price ?? '') ?>" required>
        <?= err('price') ?>

        <label>Stock Quantity *</label>
        <input type="number" name="stock_quantity" min="0" value="<?= encode($stock_quantity ?? '0') ?>" required>
        <?= err('stock_quantity') ?>

        <label>Category *</label>
        <select name="category_id" required>
            <option value="">-- Choose Category --</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c->category_id ?>" <?= (($category_id ?? '') == $c->category_id) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c->category_name) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?= err('category_id') ?>

        <label>Product Image *</label>
        <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp" required>
        <small>Accepted formats: JPG, PNG, GIF, WEBP</small>
        <?= err('image') ?>

        <label>Description</label>
        <textarea name="description" rows="5" placeholder="Enter product details here..."><?= encode($description ?? '') ?></textarea>

        <div class="form-buttons">
            <button type="submit">Add Product</button>
            <button type="reset" class="reset-btn">Reset</button>
        </div>
    </form>
    <?php endif; ?>
</div>
