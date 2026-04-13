    <?php
    $title = 'Home';
    $_title = '';

    include '../customer_header.php';
    ?>

    <!-- Banner Slider -->
    <section class="banner-slider">
        <div class="slider-container">
            <div class="slide active"><img src="/images/banner2.jpg" style="width:100%">
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

    <?php 
$stm = $_db->prepare("SELECT * FROM product WHERE is_active=1 AND created_at >= NOW() - INTERVAL 3 DAY ORDER BY created_at DESC LIMIT 8");
$stm->execute();
$products = $stm->fetchAll();

if (!empty($products)): ?>
    <section class="featured-products">
        <h2>New Arrival</h2>
        <div class="product-grid">
            <?php foreach ($products as $p): ?>
                <div class="product-card">
                   
                    <img src="../product_img/<?= encode($p->image) ?>" alt="<?= encode($p->product_name) ?>">
                    <h3><?= encode($p->product_name) ?></h3>
                    <p>RM <?= number_format($p->price, 2) ?></p>

                    <?php if ($p->stock_quantity > 0): ?> 
                        <form method="post" class="add-cart-form">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?= $p->product_id ?>">
                            <input type="number" name="quantity" value="1" min="1" max="<?= $p->stock_quantity ?>">
                            <a href='/product/product_detail.php?product_id=<?= $p->product_id ?>' class='btn'>View Details</a>
                        </form>
                    <?php else: ?>
                        <p>Out of Stock</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

    <section class="featured-products">
        <h2>Featured Products</h2>
        <div class="product-grid">
            <?php
            $stm = $_db->prepare("SELECT * FROM product WHERE is_active=1 LIMIT 10");
            $stm->execute();
            $products = $stm->fetchAll();
            ?>
            <?php foreach ($products as $p) :?>
                <div class="product-card">
                    <img src="../product_img/<?= encode($p->image) ?>" alt="<?= encode($p->product_name) ?>">

                    <h3><?= encode($p->product_name) ?></h3>
                    <p>RM <?= number_format($p->price, 2) ?></p>

                    <?php if ($p->stock_quantity > 0): ?> 
                        <form method="post" class="add-cart-form">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?= $p->product_id ?>">
                        <input type="number" name="quantity" value="1" min="1" max="<?= $p->stock_quantity ?>">
                        <a href='/product/product_detail.php?product_id=<?= $p->product_id ?>' class='btn'>View Details</a>
                    </form>
                    <?php else: ?>
                        <p>Out of Stock</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php include '../_foot.php'; ?>
