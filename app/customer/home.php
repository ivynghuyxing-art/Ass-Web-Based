<?php
require '../_base.php';
$title = 'Home';
$_title = '';
include '../customer_header.php';
?>

<!-- Banner Slider -->
<section class="banner-slider">
    <div class="slider-container">
        <div class="slide active" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        </div>
        <div class="slide" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            
        </div>
        <div class="slide" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
        </div>
    </div>
    <div class="slider-nav">
        <button class="prev-btn">&lt;</button>
        <div class="dots">
            <span class="dot active" data-slide="0"></span>
            <span class="dot" data-slide="1"></span>
            <span class="dot" data-slide="2"></span>
        </div>
        <button class="next-btn">&gt;</button>
    </div>
</section>

<!-- page content -->
<section class="welcome">
    <h2>Welcome to Stationary Hub</h2>
    <p>Your one-stop shop for all stationary needs. Browse our wide range of products and find everything you need for school, office, or home.</p>
</section>

<section class="featured-products">
    <h2>Featured Products</h2>
    <div class="product-grid">
        <?php
        $stm = $_db->query("SELECT * FROM product LIMIT 6");
        $products = $stm->fetchAll();
        foreach ($products as $p) {
            echo "<div class='product-card'>";
            echo "<img src='/product_img/{$p->image}' alt='{$p->product_name}'>";
            echo "<h3>{$p->product_name}</h3>";
            echo "<p>RM {$p->price}</p>";
            echo "<a href='/customer/product.php?id={$p->product_id}' class='btn'>View Details</a>";
            echo "</div>";
        }
        ?>
    </div>
</section>

<?php include '../_foot.php'; ?>