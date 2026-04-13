<?php
include '../_base.php';

if (!isset($_SESSION['user'])) {
    redirect('/login.php');
}

$user_id    = $_SESSION['user']->user_id;
$product_id = (int)req('product_id');

if (!$product_id) {
    redirect('/product/viewproduct.php');
}

$exists = $_db->prepare('SELECT 1 FROM wishlist WHERE user_id = ? AND product_id = ?');
$exists->execute([$user_id, $product_id]);

if ($exists->fetchColumn()) {
    $_db->prepare('DELETE FROM wishlist WHERE user_id = ? AND product_id = ?')
        ->execute([$user_id, $product_id]);
    temp('info', 'Removed from wishlist.');
} else {
    $_db->prepare('INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)')
        ->execute([$user_id, $product_id]);
    temp('info', 'Added to wishlist!');
}

$_SERVER['HTTP_REFERER']
    ? redirect($_SERVER['HTTP_REFERER'])
    : redirect('/product/viewproduct.php');