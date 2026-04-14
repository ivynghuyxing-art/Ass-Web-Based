<?php
require_once __DIR__ . '/../_base.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['success'])): ?>
<div id="popup-message" class="popup">
    <?= $_SESSION['success'] ?>
</div>

<script>
    const popup = document.getElementById("popup-message");

    popup.style.display = "block";

    setTimeout(() => {
        popup.style.opacity = "0";
        setTimeout(() => popup.remove(), 500);
    }, 2000);
</script>
<?php unset($_SESSION['success']); endif; ?>

<?php

$sort = $_GET['sort'] ?? 'DESC';

// view order detail
if (isset($_GET['id'])) {

    $id = $_GET['id'];

    // take order
    $stmt = $_db->prepare("SELECT * FROM orders WHERE orders_id = ?");
    $stmt->execute([$id]);
    $order = $stmt->fetch();

    if (!$order) {
        echo "Order not found";
        exit;
    }

    // take product contain photo
    $stmt = $_db->prepare("
        SELECT p.product_name, p.image, oi.price, oi.quantity
        FROM orders_item oi
        JOIN product p ON oi.product_id = p.product_id
        WHERE oi.orders_id = ?
    ");
    $stmt->execute([$id]);
    $items = $stmt->fetchAll();
?>

<h2>Order Details</h2>

<table class="admin-table">
<tr>
    <th>Image</th>
    <th>Product</th>
    <th>Price</th>
    <th>Qty</th>
    <th>Subtotal</th>
</tr>

<?php 
$total = 0;
foreach ($items as $item): 
    $subtotal = $item->price * $item->quantity;
    $total += $subtotal;
?>
<tr>
    <td>
        <img src="../product_img/<?= $item->image ?>" 
             width="60"
             onerror="this.src='../product_img/default.png'">
    </td>

    <td><?= $item->product_name ?></td>

    <td>RM <?= number_format($item->price, 2) ?></td>

    <td><?= $item->quantity ?></td>

    <td>RM <?= number_format($subtotal, 2) ?></td>
</tr>
<?php endforeach; ?>

<tr>
    <td colspan="4"><strong>Subtotal</strong></td>
    <td><strong>RM <?= number_format($total, 2) ?></strong></td>
</tr>

<tr>
    <td colspan="4"><strong>Shipping Fee</strong></td>
    <td><strong>RM <?= number_format($order->shipping_fee, 2) ?></strong></td>
</tr>

<tr>
    <td colspan="4"><strong>Total</strong></td>
    <td><strong>RM <?= number_format($order->total_price, 2) ?></strong></td>
</tr>

</table>

<br>
<a href="admin_panel.php?page=orders&sort=<?= $sort ?>">← Back</a>

<?php
    return;
}

// =====================
// 👉 UPDATE STATUS
// =====================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $orders_id = $_POST['orders_id'];
    $status = $_POST['status'];

    $stmt = $_db->prepare("UPDATE orders SET status = ? WHERE orders_id = ?");
    $stmt->execute([$status, $orders_id]);

    $_SESSION['success'] = "Update successful!";

    header("Location: admin_panel.php?page=orders&sort=" . ($_GET['sort'] ?? 'DESC'));
    exit;
}

// =====================
// 👉 SEARCH + FILTER
// =====================
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';

$sql = "SELECT * FROM orders WHERE 1";
$params = [];

if ($search != '') {
    $sql .= " AND orders_id LIKE ?";
    $params[] = "%$search%";
}

if ($status_filter != '' && $status_filter != 'All') {
    $sql .= " AND status = ?";
    $params[] = $status_filter;
}

$sql .= " ORDER BY orders_id " . ($sort == 'ASC' ? 'ASC' : 'DESC');

$stmt = $_db->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>

<h2>All Orders 📦</h2>

<form method="GET">
    <input type="hidden" name="page" value="orders">

    <input type="text" name="search" placeholder="Search ID"
           value="<?= htmlspecialchars($search) ?>">

    <select name="status">
        <option>All</option>
        <option <?= $status_filter=='Pending'?'selected':'' ?>>Pending</option>
        <option <?= $status_filter=='Paid'?'selected':'' ?>>Paid</option>
    </select>

    <button type="submit">Filter</button>

    <a href="admin_panel.php?page=orders&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&sort=<?= $sort == 'ASC' ? 'DESC' : 'ASC' ?>">
        Sort: <?= $sort == 'ASC' ? 'Ascending ↑' : 'Descending ↓' ?>
    </a>
</form>

<br>

<table class="admin-table">
<tr>
    <th>ID</th>
    <th>Customer</th>
    <th>Phone</th>
    <th>Address</th>
    <th>Total</th>
    <th>Status</th>
    <th>Date</th>
    <th>Action</th>
</tr>

<?php foreach ($orders as $row): ?>
<tr>
    <td><?= $row->orders_id ?></td>

    <td><?= $row->recipient_name ?></td>

    <td><?= $row->phone ?></td>

    <td>
        <?= $row->address_line1 ?><br>
        <?= $row->address_line2 ?><br>
        <?= $row->postal_code ?> <?= $row->city ?>
    </td>

    <td>RM <?= number_format($row->total_price, 2) ?></td>

    <td>
        <form method="POST" style="display:flex; gap:5px;">
            <input type="hidden" name="orders_id" value="<?= $row->orders_id ?>">

            <select name="status">
                <option value="Pending" <?= $row->status=='Pending'?'selected':'' ?>>Pending</option>
                <option value="Paid" <?= $row->status=='Paid'?'selected':'' ?>>Paid</option>
            </select>

            <button name="update_status">Update</button>
        </form>
    </td>

    <td><?= $row->order_date ?></td>

    <td>
        <a href="admin_panel.php?page=orders&id=<?= $row->orders_id ?>&sort=<?= $sort ?>">
            View
        </a>
    </td>
</tr>
<?php endforeach; ?>

</table>