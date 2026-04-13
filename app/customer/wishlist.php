<?php
$title = 'My Wishlist';
$_title = '';
include '../customer_header.php';

if (!isset($_SESSION['user'])) {
    temp('info', 'Please login to view your wishlist');
    redirect('/login.php');
}

$user_id = $_SESSION['user']->user_id;

if (is_post()) {
    $action     = req('action');
    $product_id = (int)req('product_id');

    if ($action === 'remove') {
        $_db->prepare('DELETE FROM wishlist WHERE user_id = ? AND product_id = ?')
            ->execute([$user_id, $product_id]);
        temp('info', 'Removed from wishlist.');
        redirect('/customer/wishlist.php');
    }

    if ($action === 'add_to_cart') {
        $cart = ensureCart($user_id);

        $existing = $_db->prepare('SELECT * FROM cart_item WHERE cart_id = ? AND product_id = ?');
        $existing->execute([$cart->cart_id, $product_id]);
        $existing = $existing->fetch();

        if ($existing) {
            $_db->prepare('UPDATE cart_item SET quantity = quantity + 1, price = price + (SELECT price FROM product WHERE product_id = ?) WHERE cart_item_id = ?')
                ->execute([$product_id, $existing->cart_item_id]);
        } else {
            $price = $_db->prepare('SELECT price FROM product WHERE product_id = ?');
            $price->execute([$product_id]);
            $price = $price->fetchColumn();
            $_db->prepare('INSERT INTO cart_item (cart_id, product_id, quantity, price) VALUES (?,?,?,?)')
                ->execute([$cart->cart_id, $product_id, 1, $price]);
        }

        recalcCart($cart->cart_id);
        temp('info', 'Added to cart!');
        redirect('/customer/wishlist.php');
    }
}

$items = $_db->prepare('
    SELECT w.*, p.product_name, p.price, p.image, p.stock_quantity, p.product_id
    FROM wishlist w
    JOIN product p ON w.product_id = p.product_id
    WHERE w.user_id = ?
    ORDER BY w.wishlist_id DESC
');
$items->execute([$user_id]);
$items = $items->fetchAll();
?>

<div class="title">
    <h2>My Wishlist</h2>

</div>

    <?php if ($items): ?>
        <div class="product-grid">
            <?php foreach ($items as $item): ?>
                <div class="wishlist-card">
                    <a href="../product/product_detail.php?product_id=<?= $item->product_id ?>">
                    <img src="../product_img/<?= htmlspecialchars($item->image) ?>"
                         alt="<?= htmlspecialchars($item->product_name) ?>">
                    </a>
                    <div class="wishlist-info">
                        <div class="wishlist-name"><?= htmlspecialchars($item->product_name) ?></div>
                        <div class="wishlist-price">RM <?= number_format($item->price, 2) ?></div>
                        <?php if ($item->stock_quantity > 0): ?>
                            <span class="wishlist-stock in-stock">In Stock</span>
                        <?php else: ?>
                            <span class="wishlist-stock out-of-stock">Out of Stock</span>
                        <?php endif; ?>
                    </div>
                    <div class="wishlist-actions">
                        <?php if ($item->stock_quantity > 0): ?>
                            <form method="post">
                                <input type="hidden" name="action" value="add_to_cart">
                                <input type="hidden" name="product_id" value="<?= $item->product_id ?>">
                                <button type="submit" class="wishlist-add">Add to Cart</button>
                            </form>
                        <?php endif; ?>
                        <form method="post">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="product_id" value="<?= $item->product_id ?>">
                            <button type="submit" class="wishlist-remove" onclick="return confirm('Remove from wishlist?')">Remove</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-cart">
            <p>Your wishlist is empty.</p>
            <a href="/product/viewproduct.php">Browse products</a>
        </div>
    <?php endif; ?>
</section>

<?php include '../_foot.php'; ?>