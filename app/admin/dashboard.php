<?php

// active product
$totalProducts = $_db->query("
    SELECT COUNT(*) FROM product WHERE is_active = 1
")->fetchColumn();

// have stock
$totalProductsInStock = $_db->query("
    SELECT COUNT(*) FROM product 
    WHERE stock_quantity > 0 AND is_active = 1
")->fetchColumn();

// no stock
$outOfStock = $_db->query("
    SELECT COUNT(*) FROM product 
    WHERE stock_quantity = 0 AND is_active = 1
")->fetchColumn();

// Users
$totalUsers = $_db->query("
    SELECT COUNT(*) 
    FROM user 
    WHERE role = 'customer'
")->fetchColumn();

// Orders
$totalOrders = $_db->query("SELECT COUNT(*) FROM orders")->fetchColumn();

// Completed Orders
$completedOrders = $_db->query("
    SELECT COUNT(*) FROM orders WHERE status = 'Paid'
")->fetchColumn();

// Pending Orders
$pendingOrders = $_db->query("
    SELECT COUNT(*) FROM orders WHERE status = 'Pending'
")->fetchColumn();


$totalSales = $_db->query("
    SELECT COALESCE(SUM(total_price + shipping_fee), 0)
    FROM orders
    WHERE status = 'Paid'
    AND MONTH(order_date) = MONTH(CURDATE())
    AND YEAR(order_date) = YEAR(CURDATE())
")->fetchColumn();

// Stock
$lowStock = $_db->query("
    SELECT COUNT(*) FROM product 
    WHERE stock_quantity <= 5 AND is_active = 1
")->fetchColumn();

// Chart Data
$orderStatusDistribution = $_db->query("
    SELECT status, COUNT(*) as total
    FROM orders
    GROUP BY status
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="dashboard-page">

    <!-- ===== HERO ===== -->
    <section class="dashboard-hero">
        <div class="hero-text">
            <h1>Dashboard</h1>
            <p>Monitor your store performance in one place.</p>
            <a href="admin_panel.php?page=products" class="hero-button">Manage Products</a>
        </div>

        <div class="hero-metrics">

            <div class="summary-card">
                <span>Total Products</span>
                <strong><?= number_format($totalProducts) ?></strong>

                <small>
                    <?= $totalProductsInStock ?> in stock
                </small>

                <?php if ($outOfStock > 0): ?>
                    <small style="color:red;">
                        (<?= $outOfStock ?> sold out)
                    </small>
                <?php endif; ?>
            </div>

            <!-- USERS -->
            <div class="summary-card">
                <span>Total Customers</span>
                <strong><?= number_format($totalUsers) ?></strong>
                <small>Registered users</small>
            </div>

            <!-- SALES -->
            <div class="summary-card">
                <span>Sales This Month</span>
                <strong>RM <?= number_format($totalSales, 2) ?></strong>
                <small>Completed orders only</small>
            </div>

        </div>
    </section>

    <section class="dashboard-grid">

        <div class="metric-card">
            <div>Total Products</div>
            <h2><?= number_format($totalProducts) ?></h2>
        </div>

        <div class="metric-card">
            <div>Total Users</div>
            <h2><?= number_format($totalUsers) ?></h2>
        </div>

        <div class="metric-card">
            <div>Pending Orders</div>
            <h2><?= number_format($pendingOrders) ?></h2>
        </div>

        <div class="metric-card">
            <div>Low Stock</div>
            <h2><?= number_format($lowStock) ?></h2>
        </div>

    </section>
    
    <section class="dashboard-row">

        <!-- ORDER SUMMARY -->
        <div class="dashboard-panel">
            <h2>Order Summary</h2>

            <div class="status-grid">
                <div class="status-card">
                    <span>Completed</span>
                    <strong><?= number_format($completedOrders) ?></strong>
                </div>

                <div class="status-card">
                    <span>Pending</span>
                    <strong><?= number_format($pendingOrders) ?></strong>
                </div>

                <div class="status-card">
                    <span>Low Stock</span>
                    <strong><?= number_format($lowStock) ?></strong>
                </div>
            </div>
        </div>

        <!-- CHART -->
        <div class="dashboard-panel">
            <h2>Order Status</h2>

            <?php if (count($orderStatusDistribution) > 0): ?>
                <div style="height:250px;">
                    <canvas id="salesPieChart"></canvas>
                </div>
            <?php else: ?>
                <p>No data</p>
            <?php endif; ?>

        </div>

    </section>

</div>

<!-- ===== CHART JS ===== -->
<?php if (count($orderStatusDistribution) > 0): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const chartLabels = <?= json_encode(array_column($orderStatusDistribution, 'status')) ?>;
const chartData = <?= json_encode(array_column($orderStatusDistribution, 'total')) ?>;

const colors = [
    '#22c55e', '#f59e0b', '#ef4444', '#3b82f6', '#a855f7'
];

new Chart(document.getElementById('salesPieChart'), {
    type: 'doughnut',
    data: {
        labels: chartLabels,
        datasets: [{
            data: chartData,
            backgroundColor: colors
        }]
    },
    options: {
        cutout: '60%',
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
<?php endif; ?>