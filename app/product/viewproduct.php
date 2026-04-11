<?php

$title = 'Products';
$_title = '';
include '../customer_header.php';


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
    redirect('viewproduct.php');
}

// show product list
$products = $_db->query('SELECT * FROM product WHERE is_active = 1')->fetchAll();
?>

<div class="title">
    <h2>Products</h2>
</div>
    <div class="product-grid">
        <?php foreach ($products as $p): ?>
            <div class="product-card">
                <a href="../product/product_detail.php?product_id=<?= $p->product_id ?>">
                    <img src="../product_img/<?= encode($p->image) ?>" alt="<?= encode($p->product_name) ?>">
                </a>
                <h3><?= encode($p->product_name) ?></h3>
                <p>RM <?= number_format($p->price,2) ?></p>

                 <?php if ($p->stock_quantity >0): ?> 
                <form method="post" class="add-cart-form">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?= $p->product_id ?>">
                    <input type="number" name="quantity" value="1" min="1" max="<?= $p->stock_quantity ?>">
                    <button type="submit">Add to cart</button>
                </form>
                <?php else:?>
                    <p>Out of Stock</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php include '../_foot.php'; ?>