<?php
require_once __DIR__ . '/../_base.php';
$title = 'Admin Panel';

if(!isset($_SESSION['user']) || $_SESSION['user']->role !== 'admin'){
    redirect('/../login.php');
}

$page=$_GET['page'] ?? 'dashboard';  //if no have page, page = dashboard (default)
include '../admin_header.php';

switch($page){
    case 'dashboard':
        include 'dashboard.php';
        break;
    
    case 'add_product':
    case 'product':
    case 'products':
        include '../product/admin_products.php';
        break;

    case 'edit_product':
        include '../product/editproduct.php';
        break;

    case 'delete_product':
        include '../product/deleteproduct.php';
        break;

    case 'users':
        include 'user_account.php';
        break;

    case 'orders_pending':
        echo "<h2>Pending Orders</h2>";
        break;

    case 'orders_shipped':
        echo "<h2>Shipped Orders</h2>";
        break;

    case 'orders_completed':
        echo "<h2>Completed Orders</h2>";
        break;

    case 'logout':
        include'admin_logout.php';
        break;

    default:
    echo "Page not found!";
}?>