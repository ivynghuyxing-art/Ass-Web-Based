<?php
$title = 'Categories';
$_title = '';
require '../customer_header.php';

// retrieve all the category
$stmt = $_db->query('SELECT * FROM category');
$categories = $stmt->fetchAll();
?>
    <?php foreach ($categories as $cat): ?>

        <div class="title">
            <h2><?= $cat->category_name ?></h2>
        </div>

        <div class="product-grid">
            <?php
            $stmt2 = $_db->prepare('SELECT * FROM product WHERE category_id = ?');
            $stmt2->execute([$cat->category_id]);
            $products = $stmt2->fetchAll();
            ?>

            <?php if ($products): ?>
                <?php foreach ($products as $p): ?>
                <a href="/product/product_detail.php?product_id=<?= $p->product_id ?>" class="product-link">
                    <div class="product-card">
                        <img src="/product_img/<?= $p->image ?>" alt="<?= $p->product_name ?>">
                        <h3><?= $p->product_name ?></h3>
                        <p>RM <?= number_format($p->price, 2) ?></p>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No products found.</p>
            <?php endif; ?>
        </div>

    <?php endforeach; ?>

</div>

<?php include '../_foot.php'; ?>