<?php
include '../_base.php';

auth();

$user_id    = $_user->user_id;
$product_id = req('product_id');

if (!$product_id) {
    redirect('/product/viewproduct.php');
}

$exists = $_db->prepare('SELECT 1 FROM wishlist WHERE user_id = ? AND product_id = ?');
$exists->execute([$user_id, $product_id]);

if ($exists->fetchColumn()) {
    //if already add to wish list,delete
    $_db->prepare('DELETE FROM wishlist WHERE user_id = ? AND product_id = ?')
        ->execute([$user_id, $product_id]);
    temp('info', 'Removed from wishlist.');
} else {
    //if no wishlist,add
    $_db->prepare('INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)')
        ->execute([$user_id, $product_id]);
    temp('info', 'Added to wishlist!');
}

$_SERVER['HTTP_REFERER']
    ? redirect($_SERVER['HTTP_REFERER'])
    : redirect('/product/viewproduct.php');