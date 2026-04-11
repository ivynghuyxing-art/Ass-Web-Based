<?php
require_once '_base.php';
$navCategories = $_db->query('SELECT * FROM category ORDER BY category_name')->fetchAll();
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
            
            <?php if ($_user): ?>
                <div class="user-photo-dropdown">
                    <img src="/photo/<?= ($_user->profile_photo) ?>" alt="Profile Photo">

                    <div class="dropdown-content">
                        <a href="/customer/customer_profile.php">Profile</a>
                        <a href="/customer/order_history.php">Orders History</a>
                        <a href="/customer/reset_password.php">Reset Password</a>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    </header>


    <nav class="navbar"> 

        <div class="menu"> 
                <a href="/customer/home.php"><b>Home</b></a> 
                <a href="/product/viewproduct.php"><b>Product</b></a>
                <div class="dropdown nav-dropdown">
                    <button type="button" class="dropdown-toggle"><b>Categories</b></button>
                    <div class="dropdown-content nav-dropdown-content">
                        <a href="/customer/category.php">All</a>
                        <?php foreach ($navCategories as $navCat): ?>
                            <a href="/customer/category.php?category_id=<?= $navCat->category_id ?>"><?= encode($navCat->category_name) ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <a href="/customer/contact.php"><b>Contact</b></a> 
        </div> 

        <div class="right">
             <div class ="search-bar">
                <form action="/customer/search.php" method="get" class="search-form">
                    <input type="search" id="search"name="product_name" placeholder="Search product">
                    <button type="submit">Search</button>
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
    <h1 class ="page-title"><?= $_title ?? 'Untitled' ?></h1>
