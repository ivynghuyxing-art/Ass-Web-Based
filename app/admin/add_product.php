<?php
require '../_base.php';
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

<!-- Design of Add Product Form -->
<style>
.add-product-form {
    max-width: 600px;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.add-product-form h2 {
    margin-bottom: 20px;
    color: #333;
}

.add-product-form label {
    display: block;
    margin: 15px 0 5px;
    font-weight: bold;
    color: #555;
}

.add-product-form input,
.add-product-form select,
.add-product-form textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
}

.add-product-form input:focus,
.add-product-form select:focus,
.add-product-form textarea:focus {
    outline: none;
    border-color: #4CAF50;
    box-shadow: 0 0 5px rgba(76,175,80,0.3);
}

.add-product-form textarea {
    resize: vertical;
}

.add-product-form small {
    display: block;
    margin-top: 5px;
    color: #888;
    font-size: 12px;
}

.form-buttons {
    margin-top: 25px;
    display: flex;
    gap: 10px;
}

.add-product-form button[type="submit"] {
    background: #4CAF50;
    color: white;
    border: none;
    padding: 12px 25px;
    cursor: pointer;
    border-radius: 4px;
    font-size: 16px;
    font-weight: bold;
}

.add-product-form button[type="submit"]:hover {
    background: #45a049;
}

.reset-btn {
    background: #f44336;
    color: white;
    border: none;
    padding: 12px 25px;
    cursor: pointer;
    border-radius: 4px;
    font-size: 16px;
}

.reset-btn:hover {
    background: #da190b;
}

.error-message {
    background: #f8d7da;
    color: #721c24;
    padding: 15px;
    border-radius: 4px;
    border: 1px solid #f5c6cb;
    text-align: center;
}
</style>