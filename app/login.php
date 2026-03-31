<?php
require '_base.php';

if(is_post()){
    $email    = req('email');
    $password = req('password');

    if(!$email){
        $_err['email'] = 'Required';
    } else if(!is_email($email)){
        $_err['email'] = 'Invalid email';
    }

    if(!$password){
        $_err['password'] = 'Required';
    }

    if(!$_err){
        $stm = $_db->prepare('SELECT * FROM user WHERE email = ? AND password = SHA1(?)');
        $stm->execute([$email, $password]);
        $u = $stm->fetch();

        if($u){
            $_SESSION['user'] = $u;

            // Redirect based on role
            if($u->role === 'admin'){
                $_SESSION['admin']=$u;
                temp('info', 'Welcome!');
                redirect('/admin/admin_panel.php');
                
            } else {
                temp('info', 'Login successfully!');
                redirect('/customer/home.php');
            }
        } else {
            $_err['email']='Email is incorrect';
            $_err['password'] = 'Password is incorrect';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cozy Hub</title>
    <link rel="shortcut icon" href="/images/favicon.png">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <div id="info"><?= temp('info') ?></div>

    <div class="center-box">
        <div class="login-title">Welcome to Cozy Hub</div>

        <form method="post" class="box">
            <h2>Login</h2>

            <input type="text" name="email" placeholder="Email"
                   value="<?= encode($email ?? '') ?>"
                   autocomplete="off">
            <?= err('email') ?>

            <input type="password" name="password" placeholder="Password" autocomplete="off">
            <?= err('password') ?>

            <button type="submit" class="register-btn">Login</button>

            <p class="switch">
                No account?
                <a href="/register.php">Register</a>
            </p>
        </form>
    </div>
</body>
</html>