<?php
require_once'../_base.php';
$title = 'Add Product';
$_title = 'Add Product';

$categories = $_db->query('SELECT * FROM category ORDER BY category_name')->fetchAll();

if (empty($categories)) {
    echo '<div class="error-message">Please add categories first before adding products.</div>';
}
?>

<!-- Add Product Form -->
<div class="add-product-form">
    <h2>Add New Product</h2>

    <?php if (!empty($categories)): ?>
    <form action="save_product.php" method="POST" enctype="multipart/form-data">
        
        <label>Product Name *</label>
        <input type="text" name="product_name" placeholder="Enter product name" required>

        <label>Price (RM) *</label>
        <input type="number" name="price" min="0" step="0.01" placeholder="0.00" required>

        <label>Stock Quantity *</label>
        <input type="number" name="stock_quantity" min="0" value="0" required>

        <label>Category *</label>
        <select name="category_id" required>
            <option value="">-- Choose Category --</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c->category_id ?>">
                    <?= htmlspecialchars($c->category_name) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Product Image *</label>
        <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp" required>
        <small>Accepted formats: JPG, PNG, GIF, WEBP</small>

        <label>Description</label>
        <textarea name="description" rows="5" placeholder="Enter product details here..."></textarea>

        <div class="form-buttons">
            <button type="submit">Add Product</button>
            <button type="reset" class="reset-btn">Reset</button>
        </div>
    </form>
    <?php endif; ?>
</div>
