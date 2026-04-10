<?php
// Fetch summary data
$totalProducts = $_db->query("SELECT COUNT(*) FROM product")->fetchColumn();
$totalUsers = $_db->query("SELECT COUNT(*) FROM user")->fetchColumn();
$totalOrders = $_db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalSales = $_db->query("SELECT COALESCE(SUM(total_price), 0) FROM orders")->fetchColumn();
$pendingOrders = $_db->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$lowStock = $_db->query("SELECT COUNT(*) FROM product WHERE stock_quantity <= 5")->fetchColumn();
?>

<div class="dashboard-page">
    <section class="dashboard-hero">
        <div class="hero-text">
            <h1>Dashboard</h1>
            <p>Monitor product stock, sales performance, customer activity, and order flow in one place. This dashboard is tailored for your stationery store operations.</p>
            <a href="admin_panel.php?page=products" class="hero-button">Manage Products</a>
        </div>

        <div class="hero-metrics">
            <div class="summary-card">
                <span>Total Products</span>
                <strong><?= number_format($totalProducts) ?></strong>
                <small>Active stationery items</small>
            </div>
            <div class="summary-card">
                <span>Total Customers</span>
                <strong><?= number_format($totalUsers) ?></strong>
                <small>Registered shoppers</small>
            </div>
            <div class="summary-card">
                <span>Sales This Month</span>
                <strong>RM <?= number_format($totalSales, 2) ?></strong>
                <small>All completed orders</small>
            </div>
        </div>
    </section>

    <section class="dashboard-grid">
        <div class="metric-card metric-card--blue">
            <div class="metric-card__label">Total Products</div>
            <div class="metric-card__value"><?= number_format($totalProducts) ?></div>
            <div class="metric-card__note">Inventory across all stationery categories.</div>
        </div>
        <div class="metric-card metric-card--green">
            <div class="metric-card__label">Total Users</div>
            <div class="metric-card__value"><?= number_format($totalUsers) ?></div>
            <div class="metric-card__note">Registered buyers on the Stationery Hub.</div>
        </div>
        <div class="metric-card metric-card--orange">
            <div class="metric-card__label">Pending Orders</div>
            <div class="metric-card__value"><?= number_format($pendingOrders) ?></div>
            <div class="metric-card__note">Orders waiting to be processed.</div>
        </div>
        <div class="metric-card metric-card--purple">
            <div class="metric-card__label">Low Stock Alerts</div>
            <div class="metric-card__value"><?= number_format($lowStock) ?></div>
            <div class="metric-card__note">Products with 5 or fewer units left.</div>
        </div>
    </section>

    <section class="dashboard-row">
        <div class="dashboard-panel">
            <h2>Order Summary</h2>
            <div class="status-grid">
                <div class="status-card">
                    <span>Completed Orders</span>
                    <strong><?= number_format($totalOrders - $pendingOrders) ?></strong>
                </div>
                <div class="status-card">
                    <span>Pending Orders</span>
                    <strong><?= number_format($pendingOrders) ?></strong>
                </div>
                <div class="status-card">
                    <span>Low stock products</span>
                    <strong><?= number_format($lowStock) ?></strong>
                </div>
            </div>
        </div>

        <div class="dashboard-panel chart-card">
            <h2>Sales & Visits</h2>
            <div class="chart-placeholder">Sales overview chart will appear here.</div>
            <p class="chart-note">Use this area to show monthly sales trends or popular stationery categories.</p>
        </div>
    </section>
</div>

