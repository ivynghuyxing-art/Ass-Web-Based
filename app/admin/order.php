<?php
require '../_base.php';

// ===== 更新 Status =====
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $orders_id = $_POST['orders_id'];
    $status = $_POST['status'];

    $stmt = $_db->prepare("UPDATE orders SET status = ? WHERE orders_id = ?");
    $stmt->execute([$status, $orders_id]);

    header("Location: admin_panel.php?page=orders");
    exit;
}

// ===== Search + Filter =====
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';

$sql = "SELECT * FROM orders WHERE 1";
$params = [];

// Search
if ($search != '') {
    $sql .= " AND orders_id LIKE ?";
    $params[] = "%$search%";
}

// Filter
if ($status_filter != '' && $status_filter != 'All') {
    $sql .= " AND status = ?";
    $params[] = $status_filter;
}

$sql .= " ORDER BY orders_id DESC";

$stmt = $_db->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>

<h2>All Orders</h2>

<!-- 🔍 Search + Filter -->
<form method="GET">
    <input type="hidden" name="page" value="orders">

    <input type="text" name="search" placeholder="Search Order ID"
           value="<?= htmlspecialchars($search) ?>">

    <select name="status">
        <option>All</option>
        <option <?= $status_filter=='Pending'?'selected':'' ?>>Pending</option>
        <option <?= $status_filter=='Paid'?'selected':'' ?>>Paid</option>
    </select>

    <button type="submit">Filter</button>
</form>

<br>

<table border="1" cellpadding="10">
<tr>
    <th>Order ID</th>
    <th>User ID</th>
    <th>Total</th>
    <th>Status</th>
    <th>Date</th>
    <th>Action</th>
</tr>

<?php foreach ($orders as $row): ?>
<tr>
    <td><?= $row->orders_id ?></td>
    <td><?= $row->user_id ?></td>
    <td>RM <?= number_format($row->total_price, 2) ?></td>

    <!-- 🔥 Status Update -->
    <td>
        <form method="POST">
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
        <a href="view_order.php?id=<?= $row->orders_id ?>">View</a>
    </td>
</tr>
<?php endforeach; ?>
</table>