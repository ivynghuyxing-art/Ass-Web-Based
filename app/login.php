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

    if(!$err){
        $stm = $_db->prepare('SELECT * FROM customer WHERE email=? AND password=SHA1(?)');
        $stm->execute([$email,$password]);
        $u = $stm->fetch();

        if($u){
        temp('info', 'Login succesfully!');
        login($u);
        }

        else{
        $err['password'] = 'Not matched';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <link rel="stylesheet" href="/css/user.css">
</head>

<body>

<div class="center-box">

     <div class="login-title">
        Welcome to Cozy Hub
</div>

    <form method="post" class="box">

        <h2>Login</h2>

        
        <input type="text" name="email" placeholder="Email" required autocomplete="off">

        <input type="password" name="password" placeholder="Password" required autocomplete="off">

        <a href="/customer/home.php" class='login'>Login</a>

        <p class="switch">
                No account?
                <a href="/register.php">Register</a>
        </p>


    </form>

</div>

</body>
</html>