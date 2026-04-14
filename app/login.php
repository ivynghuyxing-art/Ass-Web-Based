    <?php
    require '_base.php';

    if(is_post()){
        $email    = req('email');
        $password = req('password');

        if(!$email){
            $_err['email'] = 'Required';
        } else if(!is_email($email)){
            $_err['email'] = 'Invalid email';
        }

        if(!$password){
            $_err['password'] = 'Required';
        }else if(!is_password($password)){
            $_err['password'] = 'Password must between 5 to 100 characters.';
        }

        if(!$_err){
            $stm = $_db->prepare('SELECT * FROM user WHERE email = ? AND password = SHA1(?)');
            $stm->execute([$email, $password]);
            $u = $stm->fetch();

            if ($u) {
                if ($u->valid == 0){
                    $_err['login'] = 'Your account has been blocked. Please contact support.';
                }
                else if($u->email_verified == 0){
                    $_SESSION['verify_user_id'] = $u->user_id;
                    $_SESSION['verify_email'] = $u->email;
                    temp('info', 'Please verify your email first.');
                    redirect('verify_email.php');
                }
                else {
                $_SESSION['user'] = $u;
                temp('info', 'Welcome, ' . $u->name);
                
                if ($u->role === 'admin') {
                    redirect('/admin/admin_panel.php');
                } else {
                    redirect('/customer/home.php');
                }
                }
            } else {
                $_err['login'] = 'Invalid email or password';
            }
        }
    }
    $title = 'Login';
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

        <div class="center-box">
            <div class="login-title">Welcome to Cozy Hub</div>

            <form method="post" class="box">
                <h2>Login</h2>

                <div style="color: red; text-align: center; margin-bottom: 10px;">
                    <?= err('login') ?>
                </div>

                <input type="text" name="email" placeholder="Email" value="<?= encode($email ?? '') ?>"> <?= err('email') ?>
                <input type="password" name="password" placeholder="Password" autocomplete="off"> <?= err('password') ?>
                <button type="submit" class="register-btn">Login</button>
                    <p class="switch">
                        No account?
                        <a href="/register.php">Register</a>
                    </p>
                    <p class="switch">
                        <a href="customer/forget_password.php">Forgot Password?</a>
                    </p>
            </form>
        </div>
    </body>
</html>