<?php
require_once '../_base.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    redirect('admin_panel.php?page=products');
}

// ✅ 1. 先删 orders_item（如果有）
$stmt = $_db->prepare("DELETE FROM orders_item WHERE product_id = ?");
$stmt->execute([$id]);

// ✅ 2. 再删 cart_item
$stmt = $_db->prepare("DELETE FROM cart_item WHERE product_id = ?");
$stmt->execute([$id]);

// ✅ 3. 最后删 product（重点）
$stmt = $_db->prepare("DELETE FROM product WHERE product_id = ?");
$stmt->execute([$id]);

// ✅ flash message（你之前学的）
temp('info', 'Product deleted successfully!');

// ✅ redirect
redirect('admin_panel.php?page=products');
?>