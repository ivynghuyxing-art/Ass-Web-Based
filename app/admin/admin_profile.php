<?php

require_once '../_base.php';


if (!isset($_SESSION['user'])) {
    die("Admin not logged in");
}

$title = 'Admin Profile';
$admin = $_SESSION['user'];
$photo = $admin->profile_photo ?? '';

if (!empty($_POST['new_password'])) {

    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $user_id = $admin->user_id;

    
    $stm = $_db->prepare('SELECT password FROM user WHERE user_id = ?');
    $stm->execute([$user_id]);
    $row = $stm->fetch();

    $db_password = $row->password ?? '';


    if (sha1($current) !== $db_password) {
        $_err['password'] = "Current password incorrect!";
    }
    elseif ($new !== $confirm) {
        $_err['password'] = "Passwords do not match!";
    }
    elseif (strlen($new) < 6) {
        $_err['password'] = "Password must be at least 6 characters!";
    }
    else {
     
        $hashed = sha1($new);

        $stm = $_db->prepare('UPDATE user SET password = ? WHERE user_id = ?');
        $stm->execute([$hashed, $user_id]);

        temp('info', 'Password updated successfully.');
        redirect('admin_panel.php?page=profile');
    }
}

if (is_get()) {
    $_SESSION['photo'] = $photo;
}

if (is_post() && empty($_POST['new_password'])) {

    $email = req('email');
    $name = req('name');
    $photo = $_SESSION['photo'];
    $f = get_file('photo');

    // Email validation
    if ($email === '') {
        $_err['email'] = 'Required';
    } elseif (strlen($email) > 100) {
        $_err['email'] = 'Maximum 100 characters';
    } elseif (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    } else {
        $stm = $_db->prepare('SELECT COUNT(*) FROM user WHERE email = ? AND user_id != ?');
        $stm->execute([$email, $admin->user_id]);
        if ($stm->fetchColumn() > 0) {
            $_err['email'] = 'Duplicated';
        }
    }

    // Name validation
    if ($name === '') {
        $_err['name'] = 'Required';
    } elseif (strlen($name) > 100) {
        $_err['name'] = 'Maximum 100 characters';
    }

    // Photo validation
    if ($f) {
        if (!str_starts_with($f->type, 'image/')) {
            $_err['photo'] = 'Must be image';
        } elseif ($f->size > 1 * 1024 * 1024) {
            $_err['photo'] = 'Maximum 1MB';
        }
    }

    // save
    if (!$_err) {
        if ($f) {
            if ($photo && file_exists(__DIR__ . '/../photo/' . $photo)) {
                unlink(__DIR__ . '/../photo/' . $photo);
            }
            $photo = save_photo($f, __DIR__ . '/../photo');
        }

        $stm = $_db->prepare('UPDATE user SET email = ?, name = ?, profile_photo = ? WHERE user_id = ?');
        $stm->execute([$email, $name, $photo, $admin->user_id]);

        // update_session
        $_SESSION['user']->email = $email;
        $_SESSION['user']->name = $name;
        $_SESSION['user']->profile_photo = $photo;
        $_SESSION['photo'] = $photo;

        temp('info', 'Profile updated successfully.');
        redirect('admin_panel.php?page=profile');
    }
}

$email = $email ?? $admin->email;
$name = $name ?? $admin->name;
$displayPhoto = $photo ? '/photo/' . $photo : '/images/favicon.png';

?>

<div class="profile-page">
    <div class="page-header">
        <h1>Admin Profile</h1>
        <p class="muted">Update your administrator details and profile photo.</p>
    </div>

    <div id="info"><?= temp('info') ?></div>

    <div class="profile-grid">
        <div class="profile-card">
            <img src="<?= encode($displayPhoto) ?>" alt="Admin Photo">
            <h2><?= encode($admin->name) ?></h2>
            <p class="muted"><?= ucfirst(encode($admin->role)) ?></p>

            <div class="profile-info">
                <div>
                    <strong>Email</strong>
                    <span><?= encode($admin->email) ?></span>
                </div>
                <div>
                    <strong>User ID</strong>
                    <span><?= encode($admin->user_id) ?></span>
                </div>
                <div>
                    <strong>Gender</strong>
                    <span><?= encode($admin->gender ?? 'N/A') ?></span>
                </div>
            </div>
        </div>

        <div class="profile-form">
            <form method="post" enctype="multipart/form-data">

                <div class="form-group">
                    <label>Name</label>
                    <?= html_text('name', 'maxlength="100"') ?>
                    <?= err('name') ?>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <?= html_text('email', 'maxlength="100"') ?>
                    <?= err('email') ?>
                </div>

                <hr>
                <h3>Change Password</h3>

                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password">
                </div>

                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password">
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password">
                    <?= err('password') ?>
                </div>

                <div class="form-group upload">
                    <label>Profile Photo</label>
                    <label class="upload">
                        <?= html_file('photo', 'image/*', 'hidden') ?>
                        <img src="<?= encode($displayPhoto) ?>">
                    </label>
                    <?= err('photo') ?>
                </div>

                <div class="form-actions">
                    <button type="submit">Save Changes</button>
                    <button type="reset" class="reset-btn">Reset</button>
                </div>

            </form>
        </div>
    </div>
</div>  