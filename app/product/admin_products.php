<?php
$title = 'Products';
$_title = 'Products';

$categories = $_db->query('SELECT * FROM category ORDER BY category_name')->fetchAll();
$products = $_db->query('SELECT p.*, c.category_name FROM product p LEFT JOIN category c ON p.category_id = c.category_id ORDER BY p.product_name')->fetchAll();

if (is_post() && req('action') === 'add_product') {
    $product_name = req('product_name');
    $price = req('price');
    $stock_quantity = req('stock_quantity');
    $category_id = req('category_id');
    $description = req('description');
    $f = get_file('image');

    if (!$product_name) {
        $_err['product_name'] = 'Required';
    } elseif (strlen($product_name) > 255) {
        $_err['product_name'] = 'Maximum 255 characters only';
    }

    if (!$price) {
        $_err['price'] = 'Required';
    } elseif ($price < 0) {
        $_err['price'] = 'Price cannot be negative';
    }

    if ($stock_quantity === '' || $stock_quantity === null) {
        $_err['stock_quantity'] = 'Required';
    } elseif ($stock_quantity < 0) {
        $_err['stock_quantity'] = 'Stock cannot be negative';
    }

    if (!$category_id) {
        $_err['category_id'] = 'Required';
    } elseif (!is_exists($category_id, 'category', 'category_id')) {
        $_err['category_id'] = 'Invalid category';
    }

    if (!$f) {
        $_err['image'] = 'Required';
    } elseif (!str_starts_with($f->type, 'image/')) {
        $_err['image'] = 'Must be an image';
    } elseif ($f->size > 5 * 1024 * 1024) {
        $_err['image'] = 'Maximum 5MB';
    }

    if (!$_err) {
        $photo = save_photo($f, '../product_img');

        $stm = $_db->prepare('INSERT INTO product (product_name, price, stock_quantity, category_id, description, image) VALUES (?, ?, ?, ?, ?, ?)');
        $stm->execute([$product_name, $price, $stock_quantity, $category_id, $description, $photo]);

        temp('info', 'Product added successfully!');
        redirect('admin_panel.php?page=products');
    }
}
?>

<div class="product-management">
    <div class="product-management__panel">
        <div class="add-product-form">
            <h2>Add New Product</h2>

            <?php if (!empty($categories)): ?>
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_product">

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
            <?php else: ?>
                <div class="error-message">Please add categories first before adding products.</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="product-management__panel">
        <div class="product-list-card">
            <h2>Existing Products</h2>
            <div id="info"><?= temp('info') ?></div>

            <?php if (count($products) > 0): ?>
                <table class="product-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td><img src="../product_img/<?= encode($p->image) ?>" alt="<?= encode($p->product_name) ?>" class="product-thumb"></td>
                                <td><?= encode($p->product_name) ?></td>
                                <td><?= encode($p->category_name ?: 'Uncategorized') ?></td>
                                <td>RM <?= number_format($p->price, 2) ?></td>
                                <td><?= $p->stock_quantity ?></td>
                                <td>
                                    <a href="admin_panel.php?page=edit_product&id=<?= $p->product_id ?>" class="action-button edit">Edit</a>
                                    <a href="admin_panel.php?page=delete_product&id=<?= $p->product_id ?>" class="action-button delete">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="empty">No products yet. Use the add form to create a new product.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
