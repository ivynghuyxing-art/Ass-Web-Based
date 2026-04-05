<?php
$title = 'Cart';
$_title = 'My Cart';
include '../customer_header.php';

if (!isset($_SESSION['user'])) {
    temp('info', 'Please login to view your cart');
    redirect('/login.php');
}

$user_id = $_SESSION['user']->user_id;



$cart = ensureCart($user_id);

if (is_post()) {
    $action = req('action');

    if ($action === 'update') {
        foreach (req('quantity', []) as $id => $qty) {
            $qty = (int)$qty;
            if ($qty < 1) $qty = 1;
            $item = $_db->prepare('SELECT ci.*, p.stock_quantity, p.price AS product_price FROM cart_item ci JOIN product p ON ci.product_id = p.product_id WHERE ci.cart_item_id = ? AND ci.cart_id = ?');
            $item->execute([$id, $cart->cart_id]);
            $item = $item->fetch();
            if (!$item) continue;
            if ($qty > $item->stock_quantity) {
                $qty = $item->stock_quantity;
            }
            $_db->prepare('UPDATE cart_item SET quantity = ?, price = ? WHERE cart_item_id = ?')->execute([$qty, $qty * $item->product_price, $id]);
        }
        recalcCart($cart->cart_id);
        temp('info', 'Cart updated');
        redirect('/customer/cart.php');
    }

    if ($action === 'remove') {
        $cart_item_id = (int)req('cart_item_id');
        $_db->prepare('DELETE FROM cart_item WHERE cart_item_id = ? AND cart_id = ?')->execute([$cart_item_id, $cart->cart_id]);
        recalcCart($cart->cart_id);
        temp('info', 'Item removed');
        redirect('/customer/cart.php');
    }

    if ($action === 'clear') {
        $_db->prepare('DELETE ci FROM cart_item ci WHERE ci.cart_id = ?')->execute([$cart->cart_id]);
        recalcCart($cart->cart_id);
        temp('info', 'Cart cleared');
        redirect('/customer/cart.php');
    }
}

$items = $_db->prepare('SELECT ci.cart_item_id, ci.quantity, ci.price, p.product_name, p.image, p.price AS unit_price, p.stock_quantity FROM cart_item ci JOIN product p ON ci.product_id = p.product_id WHERE ci.cart_id = ?');
$items->execute([$cart->cart_id]);
$items = $items->fetchAll();
?>

<section class="cart-page">
    <?php if ($items): ?>
        <form method="post">
            <input type="hidden" name="action" value="update">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Product</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <input type="checkbox" class="select-item" data-price="<?= $item->price ?>" data-unit-price="<?= $item->unit_price ?>" data-cart-item-id="<?= $item->cart_item_id ?>">
                            </td>
                            <td>
                                <img src="/product_img/<?= ($item->image) ?>" alt="<?= ($item->product_name) ?>" width="70" style="margin-right:10px;vertical-align:middle;">
                                <?=($item->product_name) ?>
                            </td>
                            <td>RM <?= number_format($item->unit_price,2) ?></td>
                            <td><input type="number" name="quantity[<?= $item->cart_item_id ?>]" value="<?= $item->quantity ?>" min="1" max="<?= $item->stock_quantity ?>"></td>
                            <td>RM <?= number_format($item->price,2) ?></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="cart_item_id" value="<?= $item->cart_item_id ?>">
                                    <button type="submit" class="btn-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="cart-actions">
                <button type="submit" name="action" value="clear" class="btn-warning" onclick="return confirm('Clear your cart?')">Clear cart</button>
                <a href="/customer/checkout.php" class="btn-success">Proceed to checkout</a>
            </div>
        </form>

        <div class="cart-summary">
            <h3>Summary</h3>
            <p>Selected items: <strong id="selected-count">0</strong></p>
            <p>Selected total: <strong>RM <span id="selected-total">0.00</span></strong></p>
            <hr>
            <p>Total items: <?= $cart->total_quantity ?></p>
            <p>Total price: RM <?= number_format($cart->total_price,2) ?></p>
        </div>

    <?php else: ?>
        <div class="empty-cart">
            <p>Your cart is empty. </p>
                <a href="../product/viewproduct.php">Browse products</a>
        </div>
    <?php endif; ?>
</section>

<?php include '../_foot.php'; ?>