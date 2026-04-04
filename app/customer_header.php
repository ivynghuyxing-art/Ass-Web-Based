<?php
require'_base.php';
?>

<!DOCTYPE html>
<html lang ="en">
<head>
    <meta charset ="UTF-8">
    <meta name="viewport" content= "width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Untitled' ?></title>
    <link rel = "shortcut icon" href="/images/favicon.png">
    <link rel = "stylesheet" href="/css/app.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/js/app.js"></script>
</head>
<body>
    <div id="info"><?= temp('info') ?></div>
    <header>
        <h1><a href="/">Cozy Hub</a></h1>
        
        <div class="auth">
        <?php 
            if(!isset($_SESSION['user'])): 
        ?>
            <a href="/login.php">Login</a>
            <span>|</span>
            <a href="/register.php">Register</a>
        <?php 
            else: 
        ?>
            <a href="/logout.php" onclick="return confirm('Do you want to logout')">Logout</a>
        <?php endif; ?>
    </div>
    </header>


    <nav class="navbar"> 

        <div class="menu"> 
                <a href="/customer/home.php"><b>Home</b></a> 
                <a href="/product/viewproduct.php"><b>Product</b></a>
                <a href="/customer/category.php"><b>Categories</b></a>
                <a href="/customer/contact.php"><b>Contact</b></a> 
        </div> 

        <div class="right">
            <div class ="search-bar">
                <form action="customer/search.php" method="get" class="search-form">
                    <input type="search" id="search"name="product_name" placeholder="Search product">
                </form>        
            </div>

            <div class="cart-btn">
            <?php
            $cart_count = 0;
            if (isset($_SESSION['user'])) {
                $stmCount = $_db->prepare("SELECT COALESCE(SUM(ci.quantity),0) FROM cart_item ci JOIN cart c ON ci.cart_id=c.cart_id WHERE c.user_id=?");
                $stmCount->execute([$_SESSION['user']->user_id]);
                $cart_count = $stmCount->fetchColumn();
            }
            ?>
            <a href="/customer/cart.php">🛒 (<?= $cart_count ?>)</a>
        </div>
    </div>
</nav>

<main>
    <h1><?= $_title ?? 'Untitled' ?></h1>