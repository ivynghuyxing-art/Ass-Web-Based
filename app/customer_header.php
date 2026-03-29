<!DOCTYPE html>
<html lang ="en">
<head>
    <meta charset ="UFT-8">
    <meta name="viewport" content= "width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Untitled' ?></title>
    <link rel = "shortcut icon" href="/images/favicon.png">
    <link rel = "stylesheet" href="/css/user.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/js/app.js"></script>
</head>
<body>
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
                <a href="/"><b>Home</b></a> 
                <a href="/customer/product.php"><b>Product</b></a>
                <a href="/customer/category.php"><b>Categories</b></a>
                <a href="/customer/contact.php"><b>Contact</b></a> 
        </div> 

        <div class ="search-bar">
            <form action="customer/search.php" method="get" class="search-form">
                <input type="search" name="product_name" placeholder="Search product">

            </form>        
        </div>
    </nav>

    <main>
        <h1><?= $_title ?? 'Untitled' ?></h1>