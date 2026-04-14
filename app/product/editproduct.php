<?php
require_once __DIR__ . '/../_base.php';

$title = 'Edit Product';
$_title = 'Edit Product';

$id = $_GET['id'] ?? null;

if (!$id) redirect('admin_panel.php?page=product');

// Get product data
$stmt = $_db->prepare("SELECT * FROM product WHERE product_id = ?");
$stmt->execute([$id]);
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
$stock_quantity = intval($_POST['stock_quantity']); 
$product = $stmt->fetch();

$categories = $_db->query("SELECT * FROM category ORDER BY category_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = trim($_POST['product_name']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $stock_quantity = intval($_POST['stock_quantity']); 
    $description = trim($_POST['description']);


    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];

        move_uploaded_file($_FILES['image']['tmp_name'], "../product_img/$image");

        $stmt = $_db->prepare("UPDATE product SET product_name=?, price=?, category_id=?, stock_quantity=?, description=?, image=? WHERE product_id=?");
        $stmt->execute([$name, $price, $category_id, $stock_quantity, $description, $image, $id]);

    } else {

        $stmt = $_db->prepare("UPDATE product SET product_name=?, price=?, category_id=?, stock_quantity=?, description=? WHERE product_id=?");
        $stmt->execute([$name, $price, $category_id, $stock_quantity, $description, $id]);
    }

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

        <label>Stock Quantity *</label>
        <input type="number" name="stock_quantity" min="0" value="<?= $product->stock_quantity ?>" required>

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
