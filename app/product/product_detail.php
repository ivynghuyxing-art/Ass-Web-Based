<?php

$title = 'Product Detail';
$_title = '';
include '../customer_header.php';

// Get product_id from URL
$product_id = (int)req('product_id');

if (!$product_id) {
    temp('error', 'Product not found');
    redirect('/customer/home.php');
}

// Fetch product details
$product = $_db->prepare('SELECT p.*, c.category_name FROM product p JOIN category c ON p.category_id = c.category_id WHERE p.product_id = ? AND p.is_active = 1');
$product->execute([$product_id]);
$product = $product->fetch();

if (!$product) {
    temp('error', 'Product not found');
    redirect('/customer/home.php');
}

// Handle add to cart and buy now
if (is_post() && (req('add') || req('buy_now') )) {
    if (!isset($_SESSION['user'])) {
        if(req('buy_now')){
        temp('info', 'Please login to buy');
        }else{
            temp('info','Please login to add to cart');
        }
        redirect('../login.php');
    }

    $quantity = sanitize_qty(req('quantity', 1));

    if ($quantity > $product->stock_quantity) {
        temp('info', 'Quantity exceeds stock');
        redirect('product_detail.php?product_id=' . $product_id);
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
            redirect('product_detail.php?product_id=' . $product_id);
        }
        $_db->prepare('UPDATE cart_item SET quantity = ?, price = ? WHERE cart_item_id = ?')
            ->execute([$new_qty, $new_qty * $product->price, $item->cart_item_id]);
    } else {
        $_db->prepare('INSERT INTO cart_item (cart_id, product_id, quantity, price) VALUES (?,?,?,?)')
            ->execute([$cart_id, $product_id, $quantity, $quantity * $product->price]);
    }

    // Update cart totals
    $_db->prepare('UPDATE cart SET total_quantity = (SELECT COALESCE(SUM(quantity),0) FROM cart_item WHERE cart_id = ?), total_price = (SELECT COALESCE(SUM(price),0) FROM cart_item WHERE cart_id = ?) WHERE cart_id = ?')
        ->execute([$cart_id, $cart_id, $cart_id]);

    if (req('buy_now')) {

    $_SESSION['buy_now'] = [
        'product_id' => $product_id,
        'quantity'   => $quantity
    ];
        redirect('../customer/checkout.php');
    }else{
        temp('info', 'Added to cart');
        redirect('product_detail.php?product_id=' . $product_id);
    }

}
?>

<section class="product-detail">
    <div class="product-detail-container">
        
        <div class="product-image-section">
            <img src="../product_img/<?= encode($product->image) ?>" alt="<?= encode($product->product_name) ?>" class="product-detail-image">
        </div>

        <div class="product-info-section">
            <div class="product-meta">
                <p class="category">
                    <h1><?= ($product->product_name) ?></h1>
                </p>
                <p class="stock-status">
                    <strong>Stock:</strong> 
                    <span class="<?= $product->stock_quantity > 0 ? 'in-stock' : 'out-of-stock' ?>">
                        <?= $product->stock_quantity > 0 ? $product->stock_quantity . ' units available' : 'Out of Stock' ?>
                    </span>
                </p>
                <?php if(isset($_user)) :?>
                        <?php
                            $inwishlist=$_db->prepare('SELECT 1 FROM wishlist WHERE user_id=? AND product_id=?');
                            $inwishlist->execute([$_user->user_id,$product_id]);
                            $inwishlist = $inwishlist->fetchColumn();
                        ?>

                        <form method ="post" action="/customer/wishlist_toggle.php">
                            <input type=hidden name="product_id" value= "<?= $product_id ?>">
                            <button type=submit class="btn-wishlist">
                                <?= $inwishlist ? '♥' : '♡' ?>
                            </button>
                        </form>
                    <?php endif ?>
            </div>

            <div class="product-description">
                <h3>Description</h3>
                <p><?= encode($product->description) ?></p>
            </div>

            <div class="product-price">
                <h2>RM <?= number_format($product->price, 2) ?></h2>
            </div>

            <?php if ($product->stock_quantity > 0): ?>
                <div class ="top-row">
                    <form method="post" class="add-to-cart-form">
                        <div class="quantity-selector">
                            <label for="quantity">Quantity:</label>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?= $product->stock_quantity ?>">
                        </div>
                        <div class="product-button-group">
                            <button type="submit" name="add" value="1" class="btn-add-cart">Add to Cart</button>
                            <button type="submit" name="buy_now" value="1" class="btn-buy-now">Buy Now</button>
                        </div>
                    </form>
                
                    <?php else: ?>
                            <p class="out-of-stock-message">This product is currently out of stock</p>
                    <?php endif; ?>

                </div>

            <a href="viewproduct.php" class="btn-back">← Back to Products</a>

        </div>
    </div>
</section>

<?php include '../_foot.php'; ?>
