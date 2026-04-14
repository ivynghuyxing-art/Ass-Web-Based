<?php
$title = 'Order Detail';
$_title = 'Order Detail';
include '../customer_header.php';

if (!isset($_SESSION['user'])) {
    temp('info', 'Please login to view your order');
    redirect('/login.php');
}

if (!req('order_id')) {
    redirect('/customer/order.php');
}

$order_id = (int)req('order_id');

$order = $_db->prepare('SELECT * FROM orders WHERE orders_id = ? AND user_id = ?');
$order->execute([$order_id, $_SESSION['user']->user_id]);
$order = $order->fetch();


if (!$order) {
    temp('info', 'Order not found');
    redirect('/customer/order.php');
}

$order_id = (int)req('order_id');

$order = $_db->prepare('SELECT * FROM orders WHERE orders_id = ? AND user_id = ?');
$order->execute([$order_id, $_SESSION['user']->user_id]);
$order = $order->fetch();

if (!$order) {
    temp('info', 'Order not found');
    redirect('/customer/order.php');
}

$items = $_db->prepare('SELECT oi.*, p.product_name FROM orders_item oi JOIN product p ON oi.product_id = p.product_id WHERE oi.orders_id = ?');
$items->execute([$order_id]);
$items = $items->fetchAll();
?>

<section class="order-detail-page">
    <h2>Order #<?= $order->orders_id ?></h2>
    <p>Date: <?= $order->order_date ?> | Status: <?= encode($order->status) ?></p>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Product</th>
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
                    <td>RM <?= number_format($item->price,2) ?></td>
                    <td>RM <?= number_format($item->price * $item->quantity,2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p><strong>Shipping:</strong> RM <?= number_format($order->shipping_fee,2) ?></p>
    <p><strong>Total:</strong> RM <?= number_format($order->total_price,2) ?></p>
    <a href="/customer/order.php">Back to orders</a>
</section>

<?php include '../_foot.php'; ?>