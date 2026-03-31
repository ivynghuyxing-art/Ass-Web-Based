<?php
if (!isset($_SESSION['user']) || $_SESSION['user']->role !== 'admin') {
    redirect('/login.php');
}

$fetch_profile = $_SESSION['user'];  
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Untitled' ?></title>
    <link rel="shortcut icon" href="/images/favicon.png">
    <link rel="stylesheet" href="/css/app.css"> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>

    <div class="sidebar">
    <h2><i>Admin panel</i></h2>

    <div class="right">
        <a href="admin_panel.php?page=dashboard">Dashboard</a>
        <a href="admin_panel.php?page=add_product">Add Product</a>
        <a href="admin_panel.php?page=product">View Product</a>
    </div>
    <div class="logout-btn">
        <a href="admin_logout.php">Logout</a>
    </div>
    </div>
<div class="main-content">