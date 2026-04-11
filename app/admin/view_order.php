<?php
require_once '../_base.php';

$title = 'View Orders';
$_title = 'View Orders';


$orders = [];


$orders = $_db->query("
    SELECT 
        orders_id AS id,
        total_price AS total_amount,
        order_date AS created_at,
        user_id,
        status
    FROM orders
    ORDER BY order_date DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>



<h2>All Orders</h2>

<table border="1" cellpadding="10" cellspacing="0">
    <tr>
        <th>Order ID</th>
        <th>User ID</th>
        <th>Total</th>
        <th>Status</th>
        <th>Date</th>
        <th>Action</th>
    </tr>

    <?php if (!empty($orders)): ?>
        <?php foreach ($orders as $o): ?>
        <tr>
            <td><?= $o['id'] ?></td>
            <td><?= $o['user_id'] ?></td>
            <td>RM <?= number_format($o['total_amount'], 2) ?></td>
            <td>
                <span class="status <?= $o['status'] ?>">
                    <?= $o['status'] ?>
                </span>
            </td>
            <td><?= $o['created_at'] ?></td>
            <td>
                <a href="order.php">View</a>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="6">No orders found</td>
        </tr>
    <?php endif; ?>
</table>