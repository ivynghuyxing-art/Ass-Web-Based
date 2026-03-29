<?php
require '_base.php';


if(is_post()){
    $email = req('email');
    $password = req('password');

    if($email == ''){
        $err['email'] = 'Required';
    }
    else if(!is_email($email)) {
        $err['email'] = "Invalid email";
    }

    //validate password
    if($password ==''){
        $err['password'] = 'Required';
    }

    if(!$_err){
        $stm = $_db->prepare('SELECT * FROM admin WHERE email=? AND password=SHA1(?)');
        $stm->execute([$email,$password]);
        $a = $stm->fetch();

        if($a){
            temp('info', 'Login succesfully!');
            login($a,'admin_header.php');
        }

        else{
        $_err['password'] = 'Not matched';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Login' ?></title>
    <title>Admin Login</title>
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
    <!-- Flash message -->
    <div id="info"><?= temp('info') ?></div>
    
<div class="center-box">
    <form method="post" class="box">
        <h2>Admin Login</h2>

        <input type="text" name="email" placeholder="Email" required autocomplete="off">
        <span class="error"><?= $err['email'] ?? '' ?></span>

        <input type="password" name="password" placeholder="Password" required autocomplete="off">
        <span class="error"><?= $err['password'] ?? '' ?></span>

        <button type="submit" class="register-btn">Login</button>
    </form>
</div>

</body>
</html>