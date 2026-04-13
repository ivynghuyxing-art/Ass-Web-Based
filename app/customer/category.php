<?php
$title = 'Categories';
$_title = '';
$bodyClass = 'category-page';

require '../_base.php';

// 处理 Add to Cart
if (is_post() && req('action') === 'add') {
    auth();
    $product_id = (int)req('product_id');
    $quantity = sanitize_qty(req('quantity', 1));

    $product = $_db->prepare('SELECT * FROM product WHERE product_id = ? AND is_active = 1');
    $product->execute([$product_id]);
    $product = $product->fetch();

    if (!$product) {
        temp('info', 'Product not found');
        redirect('/');
    }

    if ($quantity > $product->stock_quantity) {
        temp('info', 'Quantity exceeds stock');
        redirect('/');
    }

    $user_id = $_SESSION['user']->user_id;
    $cart = $_db->prepare('SELECT * FROM cart WHERE user_id = ?');
    $cart->execute([$user_id]);
    $cart = $cart->fetch();

    if (!$cart) {
        $_db->prepare('INSERT INTO cart (user_id, total_price, total_quantity) VALUES (?,0,0)')->execute([$user_id]);
        $cart_id = $_db->lastInsertId();
    } else {
        $cart_id = $cart->cart_id;
    }

    $item = $_db->prepare('SELECT * FROM cart_item WHERE cart_id = ? AND product_id = ?');
    $item->execute([$cart_id, $product_id]);
    $item = $item->fetch();

    if ($item) {
        $new_qty = $item->quantity + $quantity;
        if ($new_qty > $product->stock_quantity) {
            temp('info', 'Not enough stock available');
            redirect('viewproduct.php');
        }
        $_db->prepare('UPDATE cart_item SET quantity = ?, price = ? WHERE cart_item_id = ?')
            ->execute([$new_qty, $new_qty * $product->price, $item->cart_item_id]);
    } else {
        $_db->prepare('INSERT INTO cart_item (cart_id, product_id, quantity, price) VALUES (?,?,?,?)')
            ->execute([$cart_id, $product_id, $quantity, $quantity * $product->price]);
    }

    // update cart totals
    $_db->prepare('UPDATE cart SET total_quantity = (SELECT COALESCE(SUM(quantity),0) FROM cart_item WHERE cart_id = ?), total_price = (SELECT COALESCE(SUM(price),0) FROM cart_item WHERE cart_id = ?) WHERE cart_id = ?')
        ->execute([$cart_id,$cart_id,$cart_id]);

    temp('info', 'Added to cart');
    redirect('category.php');
}

$selectedCategory = req('category_id');
if ($selectedCategory === null || $selectedCategory === '') {
    $selectedCategory = null;
} else {
    $selectedCategory = (int) $selectedCategory;
    if ($selectedCategory <= 0) {
        $selectedCategory = null;
    }
}

// retrieve all the category
$stmt = $_db->query('SELECT * FROM category');
$categories = $stmt->fetchAll();
require '../customer_header.php';
?>

<?php foreach ($categories as $cat): ?>
    <?php
        if ($selectedCategory !== null && $selectedCategory !== $cat->category_id) {
            continue;
        }

        $stmt2 = $_db->prepare('SELECT * FROM product WHERE category_id = ? AND is_active = 1');
        $stmt2->execute([$cat->category_id]);
        $products = $stmt2->fetchAll();
        $productCount = count($products);

        if (empty($products)) continue; //if this categorty no product,skip this category
    ?>

    <section class="category-section" data-category-id="<?= $cat->category_id ?>">
        <div class="category-header">
            <div>
                <h2><?= encode($cat->category_name) ?></h2>
            </div>
        </div>

        <?php if ($products): ?>
            <div class="product-grid">
    <?php foreach ($products as $index => $p): ?>
        <div class="product-card<?= $index >= 4 ? ' extra-product hidden' : '' ?>">
            <button class="wishlist-btn" onclick="toggleWishlist(this)">&#9825;</button>
            <a href="/product/product_detail.php?product_id=<?= $p->product_id ?>">
                <div class="product-thumb-wrapper">
                    <img src="/product_img/<?= encode($p->image) ?>" alt="<?= encode($p->product_name) ?>">
                </div>
            </a>
            <h3><?= encode($p->product_name) ?></h3>
            <p>RM <?= number_format($p->price, 2) ?></p>

            <?php if ($p->stock_quantity > 0): ?>
                <form method="post" class="add-cart-form">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?= $p->product_id ?>">
                    <input type="number" name="quantity" value="1" min="1" max="<?= $p->stock_quantity ?>">
                    <button type="submit">Add to cart</button>
                </form>
            <?php else: ?>
                <p>Out of Stock</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

            <?php if ($productCount > 4): ?>
                <div class="category-footer">
                    <button type="button" class="view-more-btn">View more <?= $productCount - 4 ?> <?= ($productCount - 4) === 1 ? 'item' : 'items' ?></button>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="no-products">No products found in this category.</div>
        <?php endif; ?>
    </section>
<?php endforeach; ?>

<?php include '../_foot.php'; ?>