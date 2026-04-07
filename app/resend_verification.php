<?php
require '_base.php';

if(!isset($_SESSION['verify_user_id'])){
    temp('info', 'No verification in progress.');
    redirect('login.php');
}

$user_id = $_SESSION['verify_user_id'];
$email = $_SESSION['verify_email'];

// Get user details
$stm = $_db->prepare('SELECT name FROM user WHERE user_id = ?');
$stm->execute([$user_id]);
$user = $stm->fetch();

if(!$user){
    temp('info', 'User not found.');
    redirect('register.php');
}

if(is_post()){
    // Generate new 6-digit verification code
    $verification_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

    // Update code in database
    $stm = $_db->prepare('UPDATE user SET verification_code = ? WHERE user_id = ?');
    $stm->execute([$verification_code, $user_id]);

    // Send verification email
    $mail = get_mail();
    $mail->addAddress($email, $user->name);
    $mail->Subject = 'Email Verification Code - Resent';
    $mail->Body = "Your new verification code is: $verification_code\n\nPlease enter this code to verify your email address.";
    $mail->send();

    temp('info', 'Verification code resent to your email.');
    redirect('verify_email.php');
}

$title = 'Resend Verification | Cozy Hub';
include 'customer_header.php';
?>

<body>
    <div id="info"><?= temp('info') ?></div>

    <div class="auth-wrapper">
        <div class="auth-card">
            <h1 class="auth-title">Resend Verification Code</h1>
            <p class="auth-subtitle">Click the button to resend the verification code to <?= $email ?></p>

            <form method="post" class="auth-form">
                <button type="submit" class="register-btn">Resend Code</button>

                <p class="switch">
                    <a href="verify_email.php">Back to Verification</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>
<?php include '_foot.php'; ?>