<?php
$title = 'Category';
$_title = 'Categories';
include '../customer_header.php';

$categories = $_db->query('SELECT * FROM category')->fetchAll();
?>

<!-- Search Bar Design -->
 <style>
    .center-search {
        text-align: center;
        margin: 30px 0;
    }

    .center-search input {
        width: 450px;
        padding: 12px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 6px;
    }

    .center-search button {
        padding: 12px 20px;
        font-size: 16px;
        margin-left: 10px;
        background-color: #333;
        color: #fff;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    .center-search button:hover {
        background-color: #555;
    }
</style>

<!-- Search Bar -->
<div class="center-search">
    <form action="/customer/search.php" method="get">
        <input type="search" name="product_name" placeholder="Search product">
        <button type="submit">Search</button>
    </form>
</div>

<!-- Category Card Design-->
 <style>
    .category-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 25px;
    padding: 20px;
    }

    .category-card {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        text-align: center;
        padding: 20px;
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
        text-decoration: none;
        color: #333;
    }

    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 1);
    }
    .category-card img {
        transform: 100%;
        box-shadow: 160px;
        object-fit: cover;
        border-radius: 6px;
        margin-bottom: 15px;
    }

    .category-card h3 {
    margin: 0;
    font-size: 18px;
    }
</style>

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

