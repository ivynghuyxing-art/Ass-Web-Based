<?php
require '../_base.php';

$token = req('token');

// Check token valid and not expired
$stm = $_db->prepare('SELECT * FROM user WHERE reset_token = ? AND reset_expiry > NOW()');
$stm->execute([$token]);
$u = $stm->fetch();

if(!$u){
    temp('info', 'Invalid or expired reset link.');
    redirect('login.php');
}

if(is_post()){
    $password = req('password');
    $confirm  = req('confirm');

    if(!$password){
        $_err['password'] = 'Required';
    } else if(strlen($password) < 5 || strlen($password) > 100){
        $_err['password'] = 'Between 5-100 characters only';
    }

    if(!$confirm){
        $_err['confirm'] = 'Required';
    } else if($password !== $confirm){
        $_err['confirm'] = 'Passwords do not match';
    }

    if(!$_err){
        $_db->prepare('UPDATE user SET password = SHA1(?), reset_token = NULL, reset_expiry = NULL WHERE user_id = ?')
            ->execute([$password, $u->user_id]);

        temp('info', 'Password reset successful! Please login.');
        redirect('/login.php');
    }
}

$title = 'Reset Password';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <div id="info"><?= temp('info') ?></div>
    <div class="center-box">
        <div class="login-title">Reset Password</div>
        <form method="post" class="box">
            <input type="hidden" name="token" value="<?= encode($token) ?>">
            <h2>New Password</h2>
            <input type="password" name="password" placeholder="New password" autocomplete="off">
            <?= err('password') ?>
            <input type="password" name="confirm" placeholder="Confirm password" autocomplete="off">
            <?= err('confirm') ?>
            <button type="submit" class="register-btn">Reset Password</button>
        </form>
    </div>
</body>
</html>