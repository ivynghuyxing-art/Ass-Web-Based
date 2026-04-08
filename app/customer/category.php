<?php
$title = 'Category';
$_title = '';
include '../customer_header.php';

$categories = $_db->query('SELECT * FROM category')->fetchAll();
?>

 <div class="category-text">
    <h2>Categories</h2>
 </div>



<!-- Category Card -->
<section class="category-grid">
    <?php foreach ($categories as $c): ?>
        <a href="/product/viewproduct.php?category_id=<?= $c->category_id ?>" class="category-card">
            <img src="/category_img/<?= $c->category_id ?>.jpg" alt="<?= htmlspecialchars($c->category_name) ?>">
            <h3><?= htmlspecialchars($c->category_name) ?></h3>
            <p><?= htmlspecialchars($c->description) ?></p>
        </a>
    <?php endforeach; ?>
</section>

<?php
include '../_foot.php';

