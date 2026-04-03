<?php

$title = 'Products';
$_title = 'All Products';
include '../customer_header.php';

if (!isset($_SESSION['user'])) {
    temp('info', 'Please login to manage the cart');
    redirect('/login.php');
}

if (is_post() && req('action') === 'add') {
    $product_id = (int)req('product_id');
    $quantity = sanitize_qty(req('quantity', 1));

    $product = $_db->prepare('SELECT * FROM product WHERE product_id = ?');
    $product->execute([$product_id]);
    $product = $product->fetch();

    if (!$product) {
        temp('info', 'Product not found');
        redirect('/customer/product.php');
    }

    if ($quantity > $product->stock_quantity) {
        temp('info', 'Quantity exceeds stock');
        redirect('/customer/product.php');
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
            redirect('/customer/product.php');
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
    redirect('/customer/cart.php');
}

// show product list
$products = $_db->query('SELECT * FROM product')->fetchAll();
?>

<section class="featured-products">
    <h2>Products</h2>
    <div class="product-grid">
        <?php foreach ($products as $p): ?>
            <div class="product-card">
                <img src="/product_img/<?= encode($p->image) ?>" alt="<?= encode($p->product_name) ?>">
                <h3><?= encode($p->product_name) ?></h3>
                <p>RM <?= number_format($p->price,2) ?></p>
                <p>Stock: <?= $p->stock_quantity ?></p>
                <form method="post" class="add-cart-form">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?= $p->product_id ?>">
                    <input type="number" name="quantity" value="1" min="1" max="<?= $p->stock_quantity ?>">
                    <button type="submit">Add to cart</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php include '../_foot.php'; ?>