<?php
date_default_timezone_set('Asia/Kuala_Lumpur');
session_start();

// 表单请求函数
function req($key, $default = null) {
    return $_REQUEST[$key] ?? $default;
}

// HTML 辅助函数
function encode($value) {
    return htmlentities($value);
}

function html_search($key, $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='search' id='$key' name='$key' value='$value' $attr>";
}

function html_select($key, $items, $default = '- Select One -', $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<select id='$key' name='$key' $attr>";
    if ($default !== null) {
        echo "<option value=''>$default</option>";
    }
    foreach ($items as $id => $text) {
        $selected = $id == $value ? 'selected' : '';
        echo "<option value='$id' $selected>$text</option>";
    }
    echo "</select>";
}

// 数据库连接
try {
    $_db = new PDO('mysql:host=localhost;dbname=stationary_shop', 'root', '', [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// 全局类别变量
$_product = $_db->query('SELECT product_id, product_name FROM product')->fetchAll(PDO::FETCH_KEY_PAIR);
?>