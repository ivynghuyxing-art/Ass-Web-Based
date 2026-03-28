<?php
require '_base.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $custname = $_POST['custname'];
    $password = $_POST['password'];

    $stm = $_db->prepare("SELECT * FROM customer WHERE cust_name = ?");
    $stm->execute([$custname]);
    $customer= $stm->fetch();

    if ($customer && password_verify($password, $customer->password)) {

        $_SESSION['user'] = [
            'id' => $customer->id,
            'name' => $customer->cust_name,
        ];

        header("Location: /customer/home.php");
        exit();

    } else {
        $error = "Invalid username or password";
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

        <?php
        if($error){
            echo $error;
        }
        ?>

        <input type="text" name="custname" placeholder="Username" required autocomplete="off">

        <input type="password" name="password" placeholder="Password" required autocomplete="off">

        <button type="submit">Login</button>

        <p>
            No account? <a href="/register.php">Register</a>
        </p>

    </form>

</div>

</body>
</html>