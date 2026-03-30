<?php
// admin_header.php
require '_base.php';  

if (!isset($_SESSION['admin_id'])) {
    redirect('/admin/admin_login.php');
}

$admin_id = $_SESSION['admin_id'];
$select_profile = $_db->prepare("SELECT * FROM admin WHERE admin_id=?");
$select_profile->execute([$admin_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Panel' ?></title>
    <link rel="shortcut icon" href="/images/favicon.png">
    <link rel="stylesheet" href="/css/app.css"> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>

<header class="header">
    <!-- 左边 Logo -->
    <a href="/admin/dashboard.php" class="logo">Admin Panel</a>

    <!-- 右边按钮 -->
    <div class="right">
        <button id="menu-btn">Menu</button>
        <button id="user-btn">Profile</button>
    </div>

    <!-- profile dropdown -->
    <div class="profile">
        <?php if ($fetch_profile): ?>
            <img src="../photo/<?= $fetch_profile['photo'] ?? 'default.png'; ?>" class="logo-img" width="80">
            <p><?= $fetch_profile['name']; ?></p>
            <a href="/admin/update_profile.php">Update Profile</a>
            <a href="/logout.php">Logout</a>
        <?php else: ?>
            <p>Please login</p>
        <?php endif; ?>
    </div>
</header>