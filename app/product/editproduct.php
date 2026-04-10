<?php
require_once __DIR__ . '/../_base.php';

$title = 'Edit Product';
$_title = 'Edit Product';

$id = $_GET['id'] ?? null;

if (!$id) redirect('admin_panel.php?page=product');

// Get product data
$stmt = $_db->prepare("SELECT * FROM product WHERE product_id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

$categories = $_db->query("SELECT * FROM category ORDER BY category_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = trim($_POST['product_name']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $description = trim($_POST['description']);

    $stmt = $_db->prepare("UPDATE product SET product_name=?, price=?, category_id=?, description=? WHERE product_id=?");
    $stmt->execute([$name, $price, $category_id, $description, $id]);

    temp('info', 'Product updated successfully!', 'success');
    redirect('admin_panel.php?page=product');
}
?>

<!-- Update Product Form -->
<div class="update-product-form">
    <h2>Update Product</h2>

    <form method="post" enctype="multipart/form-data">
        
        <label>Product Name *</label>
        <input type="text" name="product_name" value="<?= htmlspecialchars($product->product_name) ?>" placeholder="Enter product name" required>

        <label>Price (RM) *</label>
        <input type="number" name="price" min="0" step="0.01" value="<?= $product->price ?>" placeholder="0.00" required>

        <label>Category *</label>
        <select name="category_id" required>
            <option value="">-- Choose Category --</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c->category_id ?>"
                    <?= $c->category_id == $product->category_id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c->category_name) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Product Image</label>
        <?php if ($product->image): ?>
            <div class="current-image">
                <img src="../product_img/<?= htmlspecialchars($product->image) ?>" alt="Current product image" style="max-width: 200px;">
                <p><small>Current image</small></p>
            </div>
        <?php endif; ?>
        <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
        <small>Leave empty to keep current image. Accepted formats: JPG, PNG, GIF, WEBP</small>

        <label>Description</label>
        <textarea name="description" rows="5" placeholder="Enter product details here"><?= htmlspecialchars($product->description) ?></textarea>

        <div class="form-buttons">
            <button type="submit">Update Product</button>
            <button type="reset" class="reset-btn">Reset</button>
            <a href="admin_panel.php?page=product" class="cancel-btn">Cancel</a>
        </div>
    </form>
</div>

<!-- Design of Update Product Form -->
<style>
.update-product-form {
    max-width: 600px;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.update-product-form h2 {
    margin-bottom: 20px;
    color: #333;
}

.update-product-form label {
    display: block;
    margin: 15px 0 5px;
    font-weight: bold;
    color: #555;
}

.update-product-form input,
.update-product-form select,
.update-product-form textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
}

.update-product-form input:focus,
.update-product-form select:focus,
.update-product-form textarea:focus {
    outline: none;
    border-color: #4CAF50;
    box-shadow: 0 0 5px rgba(76,175,80,0.3);
}

.update-product-form textarea {
    resize: vertical;
}

.update-product-form small {
    display: block;
    margin-top: 5px;
    color: #888;
    font-size: 12px;
}

.current-image {
    margin: 10px 0;
    text-align: center;
}

.current-image img {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 5px;
}

.form-buttons {
    margin-top: 25px;
    display: flex;
    gap: 10px;
}

.update-product-form button[type="submit"] {
    background: #4CAF50;
    color: white;
    border: none;
    padding: 12px 25px;
    cursor: pointer;
    border-radius: 4px;
    font-size: 16px;
    font-weight: bold;
}

.update-product-form button[type="submit"]:hover {
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

.cancel-btn {
    background: #888;
    color: white;
    border: none;
    padding: 12px 25px;
    cursor: pointer;
    border-radius: 4px;
    font-size: 16px;
    text-decoration: none;
    display: inline-block;
    text-align: center;
}

.cancel-btn:hover {
    background: #666;
}
</style>