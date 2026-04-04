<?php
$title = 'Category';
$_title = 'Category';
include '../customer_header.php';
?>

<!-- Search Bar Design -->
 <style>
    .center-search {
        text-align: center;
        margin: 30px 0;
    }

    .center-search input {
        width: 400px;
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

<!-- ===== Search Bar ===== -->
<div class="center-search">
    <form action="/customer/search.php" method="get">
        <input type="search" name="product_name" placeholder="Search product">
        <button type="submit">Search</button>
    </form>
</div>

<?php
include '../_foot.php';

