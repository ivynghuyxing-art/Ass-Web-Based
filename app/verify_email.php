<?php
require '_base.php';

if(!$_user && !isset($_SESSION['verify_user_id'])){
    temp('info', 'Please register first.');
    redirect('register.php');
}

$user_id = $_SESSION['verify_user_id'] ?? $_SESSION['user']->user_id;
$email = $_SESSION['verify_email'] ?? $_SESSION['user']->email;

if(is_post()){
    $code = req('code');

    if(!$code){
        $_err['code'] = 'Required';
    } else if(strlen($code) != 6 || !is_numeric($code)){
        $_err['code'] = 'Invalid code format';
    }

    if(!$_err){
        // Check code in database
        $stm = $_db->prepare('SELECT verification_code FROM user WHERE user_id = ? AND email = ?');
        $stm->execute([$user_id, $email]);
        $user = $stm->fetch();

        if($user && $user->verification_code == $code){
            // Code correct, activate account
            $stm = $_db->prepare('UPDATE user SET valid = 1, email_verified = 1, verification_code = NULL WHERE user_id = ?');
            $stm->execute([$user_id]);

            // Clear session
            unset($_SESSION['verify_user_id']);
            unset($_SESSION['verify_email']);

            temp('info', 'Email verified successfully! You can now login.');
            redirect('login.php');
        } else {
            $_err['code'] = 'Invalid verification code';
        }
    }
}

$title = 'Verify Email | Cozy Hub';
include '_head.php';
?>

<body>
    <div id="info"><?= temp('info') ?></div>

    <div class="auth-wrapper">
        <div class="auth-card">
            <h1 class="auth-title">Verify Your Email</h1>
            <p class="auth-subtitle">Enter the 6-digit code sent to <?= $email ?></p>

            <form method="post" class="auth-form">

                <label for="code">Verification Code</label>
                <?= html_text('code', 'maxlength="6" pattern="[0-9]{6}" inputmode="numeric"') ?>
                <?= err('code') ?>

                <button type="submit" class="register-btn">Verify</button>

                <p class="switch">
                    Didn't receive the code?
                    <a href="resend_verification.php">Resend</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>
<?php include '_foot.php'; ?>