<?php
require_once __DIR__ . '/../_base.php';

$title = 'Admin Profile';
$admin = $_SESSION['user'];
$photo = $admin->profile_photo ?? '';
if (is_get()) {
    $_SESSION['photo'] = $photo;
}

if (is_post()) {
    $email = req('email');
    $name = req('name');
    $photo = $_SESSION['photo'];
    $f = get_file('photo');

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

    if ($name === '') {
        $_err['name'] = 'Required';
    } elseif (strlen($name) > 100) {
        $_err['name'] = 'Maximum 100 characters';
    }

    if ($f) {
        if (!str_starts_with($f->type, 'image/')) {
            $_err['photo'] = 'Must be image';
        } elseif ($f->size > 1 * 1024 * 1024) {
            $_err['photo'] = 'Maximum 1MB';
        }
    }

    if (!$_err) {
        if ($f) {
            if ($photo && file_exists(__DIR__ . '/../photo/' . $photo)) {
                unlink(__DIR__ . '/../photo/' . $photo);
            }
            $photo = save_photo($f, __DIR__ . '/../photo');
        }

        $stm = $_db->prepare('UPDATE user SET email = ?, name = ?, profile_photo = ? WHERE user_id = ?');
        $stm->execute([$email, $name, $photo, $admin->user_id]);

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
                    <strong>Joined</strong>
                    <span><?= encode($admin->created_at ?? 'N/A') ?></span>
                </div>
            </div>
        </div>

        <div class="profile-form">
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Name</label>
                    <?= html_text('name', 'maxlength="100"') ?>
                    <?= err('name') ?>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <?= html_text('email', 'maxlength="100"') ?>
                    <?= err('email') ?>
                </div>

                <div class="form-group upload">
                    <label for="photo">Profile Photo</label>
                    <label class="upload" tabindex="0">
                        <?= html_file('photo', 'image/*', 'hidden') ?>
                        <img src="<?= encode($displayPhoto) ?>" alt="Profile Photo">
                    </label>
                    <?= err('photo') ?>
                    <small>Choose a new image (optional, max 1MB).</small>
                </div>

                <div class="form-actions">
                    <button type="submit">Save Profile</button>
                    <button type="reset" class="reset-btn">Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>
