<?php
require '../_base.php';

$id = $_GET['id'] ?? null;

if ($id) {

    // Delete photo
    $stmt = $_db->prepare("SELECT image FROM product WHERE product_id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product && $product->image) {
        $file = "../uploads/" . $product->image;
        if (file_exists($file)) {
            unlink($file);
        }
    }

    // Delete product
    $stmt = $_db->prepare("DELETE FROM product WHERE product_id = ?");
    $stmt->execute([$id]);
}

redirect('admin_panel.php');