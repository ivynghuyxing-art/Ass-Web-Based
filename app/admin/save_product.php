<?php
require '../_base.php';

if (!isset($_SESSION['user']) || $_SESSION['user']->role !== 'admin') {
    redirect('/login.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin_panel.php?page=add_product');
}

// Get form data
$product_name = trim($_POST['product_name'] ?? '');
$price = floatval($_POST['price'] ?? 0);
$stock_quantity = intval($_POST['stock_quantity'] ?? 0);
$category_id = intval($_POST['category_id'] ?? 0);
$description = trim($_POST['description'] ?? '');

$errors = [];

// Validation
if (empty($product_name)) {
    $errors[] = "Product name is required";
} elseif (strlen($product_name) > 255) {
    $errors[] = "Product name cannot exceed 255 characters";
}

if ($price <= 0) {
    $errors[] = "Valid price is required";
}

if ($stock_quantity < 0) {
    $errors[] = "Stock quantity cannot be negative";
}

if ($category_id <= 0) {
    $errors[] = "Please select a category";
}

$image_path = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = __DIR__ . '/../product_img/';

    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($file_extension, $allowed_extensions)) {
        $errors[] = "Only JPG, JPEG, PNG, GIF, WEBP images are allowed";
    } else {
        $filename = time() . '_' . uniqid() . '.' . $file_extension;
        $destination = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            $image_path = $filename;
        } else {
            $errors[] = "Failed to upload image";
        }
    }
} else {
    $errors[] = "Product image is required";
}

if (!empty($errors)) {
    foreach ($errors as $error) {
        temp('info', $error, 'error');
    }
    redirect('admin_panel.php?page=add_product');
}

try {
    $sql = "INSERT INTO product (product_name, category_id, price, stock_quantity, image, description) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $_db->prepare($sql);
    $stmt->execute([
        $product_name,
        $category_id,
        $price,
        $stock_quantity,
        $image_path,
        $description
    ]);
    
    $new_product_id = $_db->lastInsertId();
    temp('info', "Product '{$product_name}' added successfully! (ID: {$new_product_id})", 'success');
    redirect('admin_panel.php?page=product');
    
} catch (PDOException $e) {
    temp('info', "Database error: " . $e->getMessage(), 'error');
    redirect('admin_panel.php?page=add_product');
}
?>