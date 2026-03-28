<?php
require '_base.php';

$title = 'Register';
$_title = 'Create Account';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    //check password match
    if ($password != $confirm) {
        $error = "Passwords do not match";
    }
    else {

        // check username exist
        $stm = $_db->prepare("SELECT cust_id FROM customer WHERE cust_name = ?");
        $stm->execute([$username]);

        

        if ($stm->fetch()) {
            $error = "Username already exists";
            }
        else {

            //insert user
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $stm = $_db->prepare("INSERT INTO customer (cust_name, password,membership_id)
                                  VALUES (?, ?, ?)");
            $stm->execute([$username, $hashed, null]);


            temp('info','Account has been created!');
            redirect ('login.php');

        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Register</title>

    <link rel="stylesheet" href="/css/user.css"> 
</head>

<div class="auth-wrapper">

    <div class="auth-card">

        <h1 class="auth-title">Create Account</h1>
        <p class="auth-subtitle">Join us today</p>
         <?php if($error): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>

    <?php if($success): ?>
        <p style="color:green;"><?= $success ?></p>
    <?php endif; ?>

        <form method="post" class="auth-form">

            <label>Username</label>
            <input type="text" name="username" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <label>Confirm Password</label>
            <input type="password" name="confirm_password" required>

            <button type="submit">Register</button>

            <p class="switch">
                Already have an account?
                <a href="/login.php">Login</a>
            </p>

        </form>

    </div>

</div>
<?php 
include '_foot.php'; 
?>

</html>