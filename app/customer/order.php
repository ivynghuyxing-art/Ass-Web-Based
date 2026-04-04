<?php
$title = 'My Orders';
$_title = 'My Orders';
include '../customer_header.php';

if (!isset($_SESSION['user'])) {
    temp('info', 'Please login to view your order');
    redirect('/login.php');
}

$user_id = $_SESSION['user']->user_id;

$orders = $_db->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC');
$orders->execute([$user_id]);
$orders = $orders->fetchAll();
?>

<section class="orders-page">
    <h2>My Orders</h2>
    <?php if ($orders): ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $order->orders_id ?></td>
                        <td><?= $order->order_date ?></td>
                        <td><?= encode($order->status) ?></td>
                        <td>RM <?= number_format($order->total_price,2) ?></td>
                        <td><a href="/customer/order_detail.php?order_id=<?= $order->orders_id ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No orders yet. <a href="/product/viewproduct.php">Shop now</a>.</p>
    <?php endif; ?>
</section>

<?php include '../_foot.php'; ?>
