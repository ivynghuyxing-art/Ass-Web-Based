<?php
$title = 'Categories';
$_title = 'Categories';
$bodyClass = 'category-page';

require '../_base.php';

$selectedCategory = req('category_id');
if ($selectedCategory === null || $selectedCategory === '') {
    $selectedCategory = null;
} else {
    $selectedCategory = (int) $selectedCategory;
    if ($selectedCategory <= 0) {
        $selectedCategory = null;
    }
}

// retrieve all the category
$stmt = $_db->query('SELECT * FROM category');
$categories = $stmt->fetchAll();
require '../customer_header.php';
?>

<?php foreach ($categories as $cat): ?>
    <?php
        if ($selectedCategory !== null && $selectedCategory !== $cat->category_id) {
            continue;
        }

        $stmt2 = $_db->prepare('SELECT * FROM product WHERE category_id = ? AND is_active = 1');
        $stmt2->execute([$cat->category_id]);
        $products = $stmt2->fetchAll();
        $productCount = count($products);
    ?>

    <section class="category-section" data-category-id="<?= $cat->category_id ?>">
        <div class="category-header">
            <div>
                <h2><?= encode($cat->category_name) ?></h2>
                <span class="category-badge"><?= $productCount ?> <?= $productCount === 1 ? 'item' : 'items' ?></span>
            </div>
        </div>

        <?php if ($products): ?>
            <div class="product-grid">
                <?php foreach ($products as $index => $p): ?>
                    <a href="/product/product_detail.php?product_id=<?= $p->product_id ?>" class="product-link<?= $index >= 4 ? ' extra-product hidden' : '' ?>">
                        <div class="product-card">
                            <div class="product-thumb-wrapper">
                                <img src="/product_img/<?= encode($p->image) ?>" alt="<?= encode($p->product_name) ?>">
                            </div>
                            <h3><?= encode($p->product_name) ?></h3>
                            <p>RM <?= number_format($p->price, 2) ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if ($productCount > 4): ?>
                <div class="category-footer">
                    <button type="button" class="view-more-btn">View more <?= $productCount - 4 ?> <?= ($productCount - 4) === 1 ? 'item' : 'items' ?></button>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="no-products">No products found in this category.</div>
        <?php endif; ?>
    </section>
<?php endforeach; ?>

<?php include '../_foot.php'; ?>