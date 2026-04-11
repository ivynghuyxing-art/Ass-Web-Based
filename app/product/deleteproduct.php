<?php
require_once __DIR__ . '/../_base.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $_db->prepare("UPDATE product SET is_active = 0 WHERE product_id = ?");
    $stmt->execute([$id]);

    temp('info', 'Product deleted successfully!');
}

redirect('admin_panel.php?page=products');