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
 
        // Generate 6-digit verification code
        $verification_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
 
        $stm = $_db->prepare("INSERT INTO user (name, email, password, profile_photo, role, membership_id, valid, verification_code, email_verified, gender) VALUES (?,?,SHA1(?),?,'customer',1,0,?,0,?)");
        $stm->execute([$name, $email, $password, $photo, $verification_code, $gender]);
 
        // Send verification email
        $mail = get_mail();
        $mail->addAddress($email, $name);
        $mail->Subject = 'Email Verification Code';
        $mail->Body = "Your verification code is: $verification_code\n\nPlease enter this code to verify your email address.";
        $mail->send();
 
        // Store user_id in session for verification
        $_SESSION['verify_user_id'] = $_db->lastInsertId();
        $_SESSION['verify_email'] = $email; 
 
        temp('info', 'Registration successful! Please check your email for verification code.');
        redirect('verify_email.php');
    }
}

$title = 'Register';
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