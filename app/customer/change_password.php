<?php
require '../_base.php';

auth(); 

$user = $_user;

if (is_post()) {
    $current = req('current');
    $new     = req('new');
    $confirm = req('confirm');

  
    $stm = $_db->prepare('SELECT * FROM user WHERE user_id = ? AND password = SHA1(?)');
    $stm->execute([$user->user_id, $current]);
    $u = $stm->fetch();

    if (!$u) {
        $_err['current'] = 'Wrong current password';
    }

    if (!$new) {
        $_err['new'] = 'Required';
    } else if (strlen($new) < 5) {
        $_err['new'] = 'At least 5 characters';
    }

    if ($new !== $confirm) {
        $_err['confirm'] = 'Passwords do not match';
    }

    if (!$_err) {
        $_db->prepare('UPDATE user SET password = SHA1(?) WHERE user_id = ?')
            ->execute([$new, $user->user_id]);

        temp('info', 'Password updated!');
        redirect('/customer/home.php');
    }
}
?>

<!DOCTYPE html>
<html lang ="en">
<head>
    <meta charset ="UTF-8">
    <meta name="viewport" content= "width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Untitled' ?></title>
    <link rel = "shortcut icon" href="/images/favicon.png">
    <link rel = "stylesheet" href="/css/app.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/js/app.js"></script>
</head>

<body>

<div class="center-box">
    <div class ="login-title">Change Password</div>

    <form method="post" class ="box">
        <div class="change-password-label">
            <h2>Current Password</h2>
            <input type="password" id="current" name="current" placeholder="Current Password" required>
            <?= err('current') ?>
        </div>

      <div class="change-password-label">
            <h2>New Password</h2>
            <input type="password" id="new" name="new" placeholder="New Password" required>
            <?= err('new') ?>
        </div>

        <div class="change-password-label">
            <h2>Confirm</h2>
            <input type="password" id="confirm" name="confirm" placeholder="Confirm New Password" required>
            <?= err('confirm') ?>
        </div>

        <button type="submit" class ="register-btn">Update Password</button>
        <div class = "change-password-label">
            <a href="/customer/home.php"> ← Back To Home</a>
        </div>
       
    </form>
</div>
</body>