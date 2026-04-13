<?php
require_once __DIR__ . '/../_base.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$sort = $_GET['sort'] ?? 'DESC';

// =====================
// 👉 Toast 提示
// =====================
if (!empty($_SESSION['success'])):
?>
<div id="toast"><?= $_SESSION['success'] ?></div>
<?php unset($_SESSION['success']); endif; ?>

<?php
// =====================
// 👉 VIEW ORDER DETAILS
// =====================
if (isset($_GET['id'])) {

    $id = $_GET['id'];

    $stmt = $_db->prepare("SELECT * FROM orders WHERE orders_id = ?");
    $stmt->execute([$id]);
    $order = $stmt->fetch();

    if (!$order) {
        echo "Order not found";
        exit;
    }
?>

<h2>Order Details</h2>

<?php
$stmt = $_db->prepare("
    SELECT p.product_name, p.image, oi.price, oi.quantity
    FROM orders_item oi
    JOIN product p ON oi.product_id = p.product_id
    WHERE oi.orders_id = ?
");
$stmt->execute([$id]);
$items = $stmt->fetchAll();
?>

<table class="admin-table">
<tr>
    <th>Product Image</th>
    <th>Product</th>
    <th>Price</th>
    <th>Quantity</th>
    <th>Subtotal</th>
</tr>

<?php foreach ($items as $item): 
    $subtotal = $item->price * $item->quantity;
?>
<tr>
    <td><img src="../product_img/<?= $item->image ?>" width="60"></td>
    <td><?= $item->product_name ?></td>
    <td>RM <?= number_format($item->price, 2) ?></td>
    <td><?= $item->quantity ?></td>
    <td>RM <?= number_format($subtotal, 2) ?></td>
</tr>
<?php endforeach; ?>

<tr>
    <td colspan="3"><strong>Total</strong></td>
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

<h2>All Orders</h2>

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

    <!-- 排序按钮 -->
    <a class="sort-btn"
       href="admin_panel.php?page=orders&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&sort=<?= $sort == 'ASC' ? 'DESC' : 'ASC' ?>">
        Sort: <?= $sort == 'ASC' ? 'Ascending ↑' : 'Descending ↓' ?>
    </a>
</form>

<br>

<table class="admin-table">
<tr>
    <th>Order ID</th>
    <th>Total</th>
    <th>Status</th>
    <th>Date</th>
    <th>Action</th>
</tr>

<?php foreach ($orders as $row): ?>
<tr>
    <td><?= $row->orders_id ?></td>

    <td>RM <?= number_format($row->total_price, 2) ?></td>

    <td>
        <form method="POST" class="action-form">
            <input type="hidden" name="orders_id" value="<?= $row->orders_id ?>">
            <input type="hidden" name="sort" value="<?= $sort ?>">

            <select name="status">
                <option value="Pending" <?= $row->status=='Pending'?'selected':'' ?>>Pending</option>
                <option value="Paid" <?= $row->status=='Paid'?'selected':'' ?>>Paid</option>
            </select>

            <button name="update_status">Update</button>
        </form>
    </td>

    <td><?= $row->order_date ?></td>

    <td>
        <a class="view-btn"
           href="admin_panel.php?page=orders&id=<?= $row->orders_id ?>&sort=<?= $sort ?>">
           View
        </a>
    </td>
</tr>
<?php endforeach; ?>

</table>
<script>
const toast = document.getElementById("toast");

if (toast) {
    setTimeout(() => toast.classList.add("show"), 100);
    setTimeout(() => toast.classList.remove("show"), 3000);
}
</script>
