<?php
$_title = '';
$title = 'SearchProduct';
include '../customer_header.php';
?>

<?php
$search_term = get('product_name');
$products = [];

if ($search_term) {
    $stm = $_db->prepare("SELECT * FROM product WHERE product_name LIKE ?");
    $stm->execute(['%' . $search_term . '%']);
    $products = $stm->fetchAll();
}
?>

<section class="search-results">
    <?php if ($search_term): ?>
        <h2>Search Results for "<?= ($search_term) ?>"</h2>
        <?php if (count($products) > 0): ?>
            <div class="product-grid">
                <?php foreach ($products as $p): ?>
                    <div class="product-card">
                        <img src="../product_img/<?= ($p->image) ?>">
                        <h3><?=($p->product_name) ?></h3>
                        <p>RM <?= ($p->price) ?></p>
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
        <?php else: ?>
            <p>No products found .</p>
        <?php endif; ?>
    <?php else: ?>
        <p>Please enter a search term.</p>
    <?php endif; ?>
</section>

<?php
include '../_foot.php';