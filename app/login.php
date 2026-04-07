    <?php
    error_reporting(E_ALL);
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
        }

        if(!$_err){
            $stm = $_db->prepare('SELECT * FROM user WHERE email = ? AND password = SHA1(?)');
            $stm->execute([$email, $password]);
            $u = $stm->fetch();

            if ($u) {
                if($u->valid == 0 || $u->email_verified == 0){
                    $_SESSION['verify_user_id'] = $u->user_id;
                    $_SESSION['verify_email'] = $u->email;
                    temp('info', 'Please verify your email first.');
                    redirect('verify_email.php');
                }

                $_SESSION['user'] = $u;
                temp('info', 'Welcome back, ' . $u->name);

                // 2. ROLE REDIRECTION
                if ($u->role === 'admin') {
                    redirect('/admin/admin_panel.php');
                } else {
                    redirect('/customer/home.php');
                }
            } else {
                $_err['login'] = 'Invalid email or password';
            }
        }
    }
    $_title = 'Login';
    include 'customer_header.php';
    ?>


    <body>
        <div id="info"><?= temp('info') ?></div>

        <div class="center-box">
            <div class="login-title">Welcome to Cozy Hub</div>

            <form method="post" class="box">
            <h2>Login</h2>

            <div style="color: red; text-align: center; margin-bottom: 10px;">
                <?= err('login') ?>
            </div>

            <input type="text" name="email" placeholder="Email"
                value="<?= encode($email ?? '') ?>"
                autocomplete="off">
            <?= err('email') ?>
                <input type="password" name="password" placeholder="Password" autocomplete="off">
                <?= err('password') ?>

                <button type="submit" class="register-btn">Login</button>

                <p class="switch">
                    No account?
                    <a href="/register.php">Register</a>
                </p>
            </form>
        </div>
    </body>
    </html>