<?php
$title = 'Checkout';
$_title = 'Checkout';
include '../customer_header.php';

if (!isset($_SESSION['user'])) {
    temp('info', 'Please login to checkout');
    redirect('/login.php');
}

$user_id = $_SESSION['user']->user_id;

$cart = $_db->prepare('SELECT c.*, COALESCE(SUM(ci.quantity),0) AS item_qty FROM cart c LEFT JOIN cart_item ci ON c.cart_id = ci.cart_id WHERE c.user_id = ? GROUP BY c.cart_id');
$cart->execute([$user_id]);
$cart = $cart->fetch();

$items = [];
if ($cart && $cart->item_qty > 0) {
    $items = $_db->prepare('SELECT ci.*, p.product_name, p.price AS unit_price FROM cart_item ci JOIN product p ON ci.product_id = p.product_id WHERE ci.cart_id = ?');
    $items->execute([$cart->cart_id]);
    $items = $items->fetchAll();
}

if (!$cart || $cart->item_qty == 0) {
    temp('info', 'Your cart is empty. Please add products before checkout.');
    redirect('/product/viewproduct.php');
}

$shipping_fee = 5.00;
$grand_total = $cart->total_price + $shipping_fee;

if (is_post()) {
    $orders_id = $_db->query('SELECT COALESCE(MAX(orders_id),0) + 1 FROM orders')->fetchColumn();
    $orders_item_id = $_db->query('SELECT COALESCE(MAX(orders_item_id),0) + 1 FROM orders_item')->fetchColumn();

    $_db->prepare('INSERT INTO orders (orders_id, user_id, total_price, order_date, status, shipping_fee) VALUES (?,?,?,?,?,?)')
        ->execute([$orders_id, $user_id, $grand_total, date('Y-m-d'), 'Pending', $shipping_fee]);

    foreach ($items as $item) {
        $_db->prepare('INSERT INTO orders_item (orders_item_id, orders_id, product_id, price, quantity) VALUES (?,?,?,?,?)')
            ->execute([$orders_item_id++, $orders_id, $item->product_id, $item->unit_price, $item->quantity]);

        // adjust stock
        $_db->prepare('UPDATE product SET stock_quantity = stock_quantity - ? WHERE product_id = ?')
            ->execute([$item->quantity, $item->product_id]);
    }

    // clear cart
    $_db->prepare('DELETE FROM cart_item WHERE cart_id = ?')->execute([$cart->cart_id]);
    $_db->prepare('UPDATE cart SET total_price=0, total_quantity=0 WHERE cart_id = ?')->execute([$cart->cart_id]);

    temp('info', 'Checkout successful. Order placed.');
    redirect('/customer/order.php');
}
?>

<section class="checkout-page">
    <h2>Order Summary</h2>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Unit</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= encode($item->product_name) ?></td>
                    <td><?= $item->quantity ?></td>
                    <td>RM <?= number_format($item->unit_price,2) ?></td>
                    <td>RM <?= number_format($item->unit_price * $item->quantity,2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="checkout-summary">
        <p>Subtotal: RM <?= number_format($cart->total_price,2) ?></p>
        <p>Shipping: RM <?= number_format($shipping_fee,2) ?></p>
        <p><strong>Grand total: RM <?= number_format($grand_total,2) ?></strong></p>
    </div>

    <form method="post">
        <button type="submit" class="btn-success" onclick="return confirm('Place the order now?')">Place Order</button>
        <a href="/customer/cart.php" class="btn-secondary">Back to cart</a>
    </form>
</section>

<?php include '../_foot.php'; ?>
