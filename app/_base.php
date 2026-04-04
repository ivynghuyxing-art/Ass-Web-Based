<?php

session_start();

date_default_timezone_set('Asia/Kuala_Lumpur');

//IS GET REQUEST?
function is_get(){
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

//IS POST REQUEST?
function is_post(){
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

//obtain get parameter
function get($key,$value = null){
    $value =$_GET[$key] ?? $value;
    return is_array($value)?array_map('trim',$value):trim($value);
}

//obtain post paramater
function post($key,$value=null){
    $value =$_POST[$key] ?? $value;
    return is_array($value)?array_map('trim',$value):trim($value);
}

//obtain request(get abd oost parameter)
function req($key,$value = null){
    $value =$_REQUEST[$key] ?? $value;
    return is_array($value)?array_map('trim',$value):trim($value);
}

// Redirect to URL
function redirect($url = null) {
    $url ??= $_SERVER['REQUEST_URI'];
    header("Location: $url");
    exit();
}

// Set or get temporary session variable
function temp($key, $value = null) {
    if ($value !== null) {
        $_SESSION["temp_$key"] = $value;
    }
    else {
        $value = $_SESSION["temp_$key"] ?? null;
        unset($_SESSION["temp_$key"]);
        return $value;
    }
}

function encode($value) {
    return htmlentities($value);
}

function html_text($key, $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='text' id='$key' name='$key' value='$value' $attr>";
}
function html_password($key, $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='password' id='$key' name='$key' value='$value' $attr>";
}
function html_file($key, $accept = '', $attr = '') {
    echo "<input type='file' id='$key' name='$key' accept='$accept' $attr>";
}

function get_file($key){
    $f = $_FILES[$key] ?? null;

    if($f && $f['error'] == 0){
        return (object)$f;
    }
    return null;
}


//is unique?
function is_unique($value, $table, $field) {
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() == 0;
}

function is_exists($value, $table, $field) {
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() > 0;
}

function save_photo($f, $folder,$width=200, $height =200){
    $photo = uniqid(). '.jpg';

    require_once 'lib/SimpleImage.php';
    $img = new SimpleImage();
    $img->fromFile($f->tmp_name)
        ->thumbnail($width,$height)
        ->toFile("$folder/$photo", 'image/jpeg');

    return $photo;

}

//ERROR HANDLING

// Global error array
$_err = [];

// Generate <span class='err'>
function err($key) {
    global $_err;
    if ($_err[$key] ?? false) {
        echo"<span class='err'>$_err[$key]</span>";
    }
    else {
        echo '<span></span>';
    }
}

function is_email($value){
    return filter_var($value,FILTER_VALIDATE_EMAIL) !== false;
}

$_user = $_SESSION['user'] ?? null;

function login($user, $url = '/') {
    $_SESSION['user'] = $user;
    redirect($url);
}

function ensureCart($user_id) {
    global $_db;
    $cart = $_db->prepare('SELECT * FROM cart WHERE user_id = ?');
    $cart->execute([$user_id]);
    $cart = $cart->fetch();
    if (!$cart) {
        $_db->prepare('INSERT INTO cart (user_id, total_price, total_quantity) VALUES (?,0,0)')->execute([$user_id]);
        $cart = (object)['cart_id' => $_db->lastInsertId(), 'total_price' => 0, 'total_quantity' => 0];
    }
    return $cart;
}

function recalcCart($cart_id) {
    global $_db;
    $_db->prepare('UPDATE cart SET total_quantity = (SELECT COALESCE(SUM(quantity),0) FROM cart_item WHERE cart_id = ?), total_price = (SELECT COALESCE(SUM(price),0) FROM cart_item WHERE cart_id = ?) WHERE cart_id = ?')
        ->execute([$cart_id, $cart_id, $cart_id]);
}

function sanitize_qty($qty) {
    $qty = (int)$qty;
    return $qty > 0 ? $qty : 1;
}

function auth(...$roles) {
    global $_user;
    if ($_user) {
        if ($roles) {
            if (in_array($_user->role, $roles)) {
                return; // OK
            }
        }
        else {
            return; // OK
        }
    }
    
    redirect('/login.php');
}


$_db = new PDO('mysql:dbname=stationary_shop', 'root', '', [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
]);

?>
