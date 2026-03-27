<!DOCTYPE html>
<html lang ="en">
<head>
    <meta charset ="UFT-8">
    <meta name="viewport" content= "width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Untitled' ?></title>
    <link rel = "shortcut icon" href="/images/favicon.png">
    <link rel = "stylesheet" href="/css/app.css">
</head>
<body>
    <header>
        <h1><a href="/">Cozy Hub</a></h1>
        
        <div class="auth">
           <a href ="/login.php"> Login</a>
            <a href = "/register.php">Register</a>

        </div>
    </header>

    <nav class="navbar"> 

        <div class="menu"> 
                <a href="/"><b>Home</b></a> 
                <a href="/page/product.php"><b>Product</b></a>
                <a href="/page/category.php"><b>Categories</b></a>
                <a href="/page/contact.php"><b>Contact</b></a> 
        </div> 
    </nav>

    <main>
        <h1><?= $_title ?? 'Untitled' ?></h1>