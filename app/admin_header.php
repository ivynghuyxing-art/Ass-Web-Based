<?php
 

if (!isset($_SESSION['admin_id'])) {
    redirect('/admin/admin_login.php');
}

$admin_id = $_SESSION['admin_id'];
$select_profile = $_db->prepare("SELECT * FROM admin WHERE admin_id=?");
$select_profile->execute([$admin_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
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
        <a href="dashboard.php">Dashboard</a>
        <a href="add_product.php">Add Product</a>
        <a href ="product.php">View Product</a>
    </div>
    </div>
<div class="main-content">