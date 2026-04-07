<?php
// Fetch summary data
$totalProducts = $_db->query("SELECT COUNT(*) FROM product")->fetchColumn();
$totalUsers = $_db->query("SELECT COUNT(*) FROM user")->fetchColumn();
$totalOrders = $_db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalSales = $_db->query("SELECT COALESCE(SUM(total_price), 0) FROM orders")->fetchColumn();
?>

<h2>Dashboard</h2>

<div class="row">
    <div class="col-md-3">
        <div class="card p-3 bg-primary text-white">
            <h4>Total Products</h4>
            <p><?php echo $totalProducts; ?></p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 bg-success text-white">
            <h4>Total Users</h4>
            <p><?php echo $totalUsers; ?></p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 bg-warning text-white">
            <h4>Total Orders</h4>
            <p><?php echo $totalOrders; ?></p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 bg-info text-white">
            <h4>Total Sales (RM)</h4>
            <p>RM <?php echo number_format($totalSales, 2); ?></p>
        </div>
    </div>
</div>
$totalProducts = $_db->query("SELECT COUNT(*) FROM product")->fetchColumn();
$totalUsers = $_db->query("SELECT COUNT(*) FROM user")->fetchColumn();
$totalOrders = $_db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalSales = $_db->query("SELECT COALESCE(SUM(total_price), 0) FROM orders")->fetchColumn();
?>

<div class="row">
    <div class="col-md-3">
        <div class="card p-3 bg-primary text-white">
            <h4>Total Products</h4>
            <p><?php echo $totalProducts; ?></p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 bg-success text-white">
            <h4>Total Users</h4>
            <p><?php echo $totalUsers; ?></p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 bg-warning text-white">
            <h4>Total Orders</h4>
            <p><?php echo $totalOrders; ?></p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 bg-info text-white">
            <h4>Total Sales (RM)</h4>
            <p>RM <?php echo number_format($totalSales, 2); ?></p>
        </div>
    </div>
</div>

