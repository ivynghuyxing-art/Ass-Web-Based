<?php
require '_base.php';

// 获取搜索参数
$name = req('name');
$product_id = req('product_id');

// 构建 SQL 查询
$sql = "SELECT * FROM product WHERE 1";
$params = [];

if ($name) {
    $sql .= " AND name LIKE ?";
    $params[] = "%$name%";
}

if ($program_id) {
    $sql .= " AND program_id = ?";
    $params[] = $program_id;
}

// 执行查询
$stm = $_db->prepare($sql);
$stm->execute($params);
$products = $stm->fetchAll();
?>