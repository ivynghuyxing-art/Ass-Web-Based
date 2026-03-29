<?php
require '_base.php';

if(is_post()){
    $cust_name = req('cust_name');
    $email = req('email');
    $password = req('password');
    $confirm = req('confirm');
    $f = get_file('photo');


    if(!$cust_name){
        $_err['cust_name']= 'Required';
    }
    else if(strlen($cust_name) > 100){
        $_err['cust_name'] = 'Maximum 100 character only';
    }
    else if(!is_unique($cust_name,'customer',"cust_name")){
        $_err['cust_name'] = 'Username already exist';
    }

    if(!$email){
        $_err['email'] = 'Required';
    }
    else if(strlen($email) > 100){
        $_err['email'] = 'Maximum 100 character only';
    }
    else if(!is_email($email)){
        $_err['email'] = 'Invalid email';
    }
    else if(!is_unique($email,'customer','email')){
        $_err['email'] = 'Email already exist!';
    }

    if(!$password){
        $_err['password'] = 'Required';
    }
    else if(strlen($password) <5 || strlen($password)>100){
        $_err['password'] = 'Between 5-100 characters only';
    }

    if(!$confirm){
        $_err['confirm'] = 'Required';
    }
    else if ($password !== $confirm) {
        $_err['confirm'] = 'Passwords do not match';
    }

    //validate photo

    if(!$f){
        $_err['photo'] = 'Required';
    }
    else if(!str_starts_with($f->type,'image/')){
        $_err['photo'] = 'Must be image';
    }
    else if($f->size >1 *1024 *1024){
        $_err['photo'] = 'Maximum 1MB';
    }

    if(!$_err){
        $photo = save_photo($f, 'photo');

        $stm = $_db->prepare('INSERT INTO customer (cust_name, email,password,photo) VALUES (?,?,?,?)');
        $stm->execute([
            $cust_name,
            $email,
            $password,
            $photo
        ]);

        temp('info', 'Register Succefully');
        redirect('login.php');
    }

}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Register</title>

    <link rel="stylesheet" href="/css/user.css"> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/js/app.js"></script>
</head>

<div class="auth-wrapper">

    <div class="auth-card">

        <h1 class="auth-title">Create Account</h1>
        <p class="auth-subtitle">Join us today</p>

        <form method="post" class="auth-form" enctype="multipart/form-data">


            <label for="cust_name">Username</label>
            <?= html_text('cust_name', 'maxlength="50"') ?>
            <span class="error"> <?= err('cust_name') ?></span>

            <label for="email">Email</label>
            <?= html_text('email', 'maxlength="100"') ?>
            <span class="error"> <?= err('email')?></span>

            <label for="password">Password</label>
            <?= html_password('password', 'maxlength="100"') ?>
             <span class="error"> <?= err('password')?></span>

            <label for="confirm">Confirm</label>
            <?= html_password('confirm', 'maxlength="100"') ?>
            <span class="error"> <?= err('confirm')?></span>

            <label for="photo">Photo</label>
            <label class="upload" tabindex="0">
                <?= html_file('photo', 'image/*','hidden') ?>
                <img src="/images/photo.jpg">
            </label>
             <span class="error"> <?= err('photo')?></span>

            <button type="submit" class="register-btn">Register</button>

            <p class="switch">
                Already have an account?
                <a href="/login.php">Login</a>
            </p>

        </form>

    </div>

</div>


</html>