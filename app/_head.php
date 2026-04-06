<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Stationary Shop' ?></title>
    <link rel="shortcut icon" href="/images/favicon.png">
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/js/app.js"></script>
</head>
<body>
    <div id="info"><?= isset($temp) ? temp('info') : '' ?></div>
    
    <header>
        <h1><a href="/">Cozy Hub</a></h1>

        <div class="auth">
            <?php if (!isset($_SESSION['user'])): ?>
                <a href="/login.php">Login</a>
                <span>|</span>
                <a href="/register.php">Register</a>
            <?php else: ?>
                <a href="/logout.php" onclick="return confirm('Do you want to logout?')">Logout</a>
            <?php endif; ?>
        </div>
    </header>

    <?php if (isset($_SESSION['user']) && $_SESSION['user']->role === 'customer'): ?>
    <nav class="navbar">
        <div class="menu">
            <a href="/"><b>Home</b></a>
            <a href="/product/viewproduct.php"><b>Product</b></a>
            <a href="/customer/category.php"><b>Categories</b></a>
            <a href="/customer/contact.php"><b>Contact</b></a>
        </div>

        <div class="search-bar">
            <form action="customer/search.php" method="get" class="search-form">
                <input type="search" name="product_name" placeholder="Search product">
            </form>
        </div>
    </nav>
    <?php endif; ?>

    <main>
        <?php if (isset($_SESSION['user']) && $_SESSION['user']->role === 'admin'): ?>
            <aside class="sidebar">
                <h2><i>Admin panel</i></h2>
                <div class="right">
                    <a href="admin_panel.php?page=dashboard">Dashboard</a>
                    <a href="admin_panel.php?page=add_product">Add Product</a>
                    <a href="admin_panel.php?page=product">View Product</a>
                </div>
                <div class="logout-btn">
                    <a href="admin_logout.php">Logout</a>
                </div>
            </aside>
        <?php endif; ?>

        <div class="main-content">
            <h1><?= $_title ?? 'Welcome to Cozy Hub' ?></h1>
            </div>
    </main>

</body>
</html>