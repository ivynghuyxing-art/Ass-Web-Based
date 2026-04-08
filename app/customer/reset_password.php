<?php
require '../_base.php';

// Initialize error array
$_err = [];

if (is_post()) {
    $email = req('email');

    // 1. Validation (Hardcoded strings instead of $text)
    if ($email == '') {
        $_err['email'] = 'Email is required';
    } else if (!is_email($email)) {
        $_err['email'] = 'Invalid email format';
    } else if (!is_exists($email, 'user', 'email')) { // Changed 'users' to 'user'
        $_err['email'] = 'This email is not registered';
    }

    if (empty($_err)) {
        // 2. Fetch User
        $stm = $_db->prepare("SELECT * FROM user WHERE email = ?");
        $stm->execute([$email]);
        $user = $stm->fetch(); // Fetch as object

        if ($user) {
            $id = sha1(uniqid() . rand());

            // 3. Handle Token (Using $user->id based on your registration logic)
            $stm = $_db->prepare("DELETE FROM token WHERE user_id = ?");
            $stm->execute([$user->user_id]);

            $stm = $_db->prepare("INSERT INTO token (id, expire, user_id) VALUES (?, ADDTIME(NOW(), '01:00:00'), ?)");
            $stm->execute([$id, $user->user_id]);

            // 4. Prepare Email
            $base_url = "http://" . $_SERVER['HTTP_HOST']; 
            $url = "$base_url/reset_password.php?id=$id"; // Absolute URL for email links

            $m = get_mail();
            $m->addAddress($email, $user->name);
            
            // Check for profile_photo (synchronize with register.php)
          
            $photo_html = "";

            $m->isHTML(true);
            $m->Subject = "Password Reset Request - Cozy Hub";
            $m->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #eee; padding: 20px; border-radius: 10px;'>
                    <div style='text-align: center; margin-bottom: 20px;'>
                        $photo_html
                        <h2 style='color: #8B0000;'>Reset Your Password</h2>
                    </div>
                    <p>Hi <strong>{$user->name}</strong>,</p>
                    <p>We received a request to reset your password. Click the button below to continue:</p>
                    <p style='text-align: center; margin: 30px 0;'>
                        <a href='$url' style='background-color: #8B0000; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Reset Password</a>
                    </p>
                    <p>If you didn't request this, you can ignore this email.</p>
                </div>
            ";

            if ($m->send()) {
                temp('info', 'Reset link sent! Please check your inbox.');
                redirect('login.php');
            } else {
                $_err['email'] = 'Email server error. Try again later.';
            }
        }
    }
}

$_title = 'Forgot Password';

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

<div class="auth-wrapper">
    <div class="auth-card">
        <h1 class="auth-title">Forgot Password</h1>
        <p class="auth-subtitle">Enter your email to receive a reset link</p>

        <form method="post" class="auth-form">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" placeholder="example@mail.com" value="<?= htmlspecialchars($email ?? '') ?>">
            <span class="err"><?= $_err['email'] ?? '' ?></span>

            <button type="submit">Send Reset Link</button>
        </form>
        
        <div class="switch">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</div>
</body>

