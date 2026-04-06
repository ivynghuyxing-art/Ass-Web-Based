<?php
require '_base.php';
 
if(is_post()){
    $name     = req('name');
    $email    = req('email');
    $password = req('password');
    $confirm  = req('confirm');
    $gender   = req('gender');
    $f        = get_file('photo');
 
    // Validate name
    if(!$name){
        $_err['name'] = 'Required';
    } else if(strlen($name) > 100){
        $_err['name'] = 'Maximum 100 characters only';
    } else if(!is_unique($name, 'user', 'name')){
        $_err['name'] = 'Username already exists';
    }
 
    // Validate email
    if(!$email){
        $_err['email'] = 'Required';
    } else if(strlen($email) > 100){
        $_err['email'] = 'Maximum 100 characters only';
    } else if(!is_email($email)){
        $_err['email'] = 'Invalid email';
    } else if(!is_unique($email, 'user', 'email')){
        $_err['email'] = 'Email already exists!';
    }
 
    // Validate password
    if(!$password){
        $_err['password'] = 'Required';
    } else if(strlen($password) < 5 || strlen($password) > 100){
        $_err['password'] = 'Between 5-100 characters only';
    }
 
    // Validate confirm password
    if(!$confirm){
        $_err['confirm'] = 'Required';
    } else if($password !== $confirm){
        $_err['confirm'] = 'Passwords do not match';
    }

    // Validate gender
    if(!$gender){
        $_err['gender'] = 'Required';
    } else if(!in_array($gender, ['M', 'F'])){
        $_err['gender'] = 'Invalid gender';
    }
 
    // Validate photo
    if(!$f){
        $_err['photo'] = 'Required';
    } else if(!str_starts_with($f->type, 'image/')){
        $_err['photo'] = 'Must be an image';
    } else if($f->size > 1 * 1024 * 1024){
        $_err['photo'] = 'Maximum 1MB';
    }
 
if(!$_err){
        $photo = save_photo($f, 'photo');
 
        $stm = $_db->prepare("INSERT INTO user (name, email, password, profile_photo, role, membership_id, valid) VALUES (?,?,SHA1(?),?,'customer',1,0)");
        $stm->execute([$name, $email, $password, $photo]);

        $id = $_db->lastInsertId();

        // Verification Token (optional - can be skipped if email fails)
        $token = sha1(uniqid() . rand());
        $expire = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $_db->prepare('INSERT INTO verification_tokens (user_id, token, expire) VALUES (?, ?, ?)')->execute([$id, $token, $expire]);

        $verification_link = "/verify_email.php?token=$token";
        $mail = get_mail();
        $mail->addAddress($email, $name);
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification';
        $mail->Body = "
            <p>Hi $name,</p>
            <p>Thank you for registering. Please verify your email by clicking the link below:</p>
            <p><a href='$verification_link'>$verification_link</a></p>
            <p>This link will expire in 1 hour.</p>
        ";
        
        try {
            $mail->send();
        } catch (Exception $e) {
        }
        
        temp('info', 'Registered successfully! Please check your email to verify your account.');
        redirect('login.php');
    }
}

$title = 'Register | Cozy Hub';
include '_head.php';

?>
 
<body>
    <div id="info"><?= temp('info') ?></div>
 
    <div class="auth-wrapper">
        <div class="auth-card">
            <h1 class="auth-title">Create Account</h1>
            <p class="auth-subtitle">Join us today</p>
 
            <form method="post" class="auth-form" enctype="multipart/form-data">
 
                <label for="name">Username</label>
                <?= html_text('name', 'maxlength="100"') ?>
                <?= err('name') ?>
 
                <label for="email">Email</label>
                <?= html_text('email', 'maxlength="100"') ?>
                <?= err('email') ?>

                <label for="gender">Gender</label>
                <select id="gender" name="gender">
                    <option value="">-- Select Gender --</option>
                    <option value="M" <?= ($gender ?? '') === 'M' ? 'selected' : '' ?>>Male</option>
                    <option value="F" <?= ($gender ?? '') === 'F' ? 'selected' : '' ?>>Female</option>
                </select>
                <?= err('gender') ?>

                <label for="password">Password</label>
                <?= html_password('password', 'maxlength="100"') ?>
                <?= err('password') ?>
 
                <label for="confirm">Confirm Password</label>
                <?= html_password('confirm', 'maxlength="100"') ?>
                <?= err('confirm') ?>
 
                <label for="photo">Profile Photo</label>
                <label class="upload">
                    <?= html_file('photo', 'image/*', 'hidden') ?>
                    <img src="/images/photo.jpg">
                </label>
                <?= err('photo') ?>
 
                <button type="submit" class="register-btn">Register</button>
 
                <p class="switch">
                    Already have an account?
                    <a href="/login.php">Login</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>
<?php include '_foot.php'; ?>