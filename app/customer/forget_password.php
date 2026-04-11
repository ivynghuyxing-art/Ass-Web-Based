<?php
require '../_base.php';

if(is_post()){
    $email = req('email');

    if(!$email){
        $_err['email'] = 'Required';
    } else if(!is_email($email)){
        $_err['email'] = 'Invalid email';
    }

    if(!$_err){
        $stm = $_db->prepare('SELECT * FROM user WHERE email = ?');
        $stm->execute([$email]);
        $u = $stm->fetch();

        if($u){
            // Generate token
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $_db->prepare('UPDATE user SET reset_token = ?, reset_expiry = ? WHERE email = ?')
                ->execute([$token, $expiry, $email]);

            // Send email
            $link = "http://localhost:8000/customer/reset_password.php?token=$token";
            $mail = get_mail();
            $mail->addAddress($email, $u->name);
            $mail->Subject = 'Reset Your Password';
            $mail->Body = "Click the link to reset your password:\n$link\n\nLink expires in 1 hour.";
            $mail->send();
        }
        
        temp('info', 'Reset link has been sent to your email.');
        redirect('/login.php');
    }
}
$title = 'Forgot Password';
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
        <div class="login-title">
            Forgot Password
        </div>
        <form method="post" class="box">
            <h2>Reset Password</h2>
            <input type="text" name="email" placeholder="Enter your email" value="<?= encode($email ?? '') ?>">
            <?= err('email') ?>
            <button type="submit" class="register-btn">Send Reset Link</button>
            <p class="switch"><a href="/login.php">← Back to Login</a></p>
        </form>
    </div>
</body>
</html>