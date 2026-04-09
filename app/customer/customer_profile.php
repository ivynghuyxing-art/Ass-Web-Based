<?php
include '../customer_header.php';

// ----------------------------------------------------------------------------

// Authenticated users
auth();// only allowed user who have 
        // login to access

//retrive to display
if (is_get()) {
    
    $stm = $_db->prepare('SELECT * FROM user WHERE user_id=?');
    $stm->execute([$_user->user_id]);
    $u = $stm->fetch();

    if (!$u) {
        redirect('/');
    }

    extract((array)$u);
    $_SESSION['photo'] = $u->profile_photo;
}

//update profile
if (is_post()) {
    $email = req('email');
    $name  = req('name');
    $photo = $_SESSION['photo'];
    $f = get_file('photo');

    // Validate: email
    if ($email == '') {
        $_err['email'] = 'Required';
    }
    else if (strlen($email) > 100) {
        $_err['email'] = 'Maximum 100 characters';
    }
    else if (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    }
    else {
        // validate to ensure no same email address
        // we allowed user to change email, cannot same email
        $stm = $_db->prepare('
            SELECT COUNT(*) FROM user
            WHERE email =? AND user_id !=?
        ');
        $stm->execute([$email,$_user->user_id]);

        if ($stm->fetchColumn() > 0) {
            $_err['email'] = 'Duplicated';
        }
    }

    // Validate: name
    if ($name == '') {
        $_err['name'] = 'Required';
    }
    else if (strlen($name) > 100) {
        $_err['name'] = 'Maximum 100 characters';
    }

    // Validate: photo (file) --> optional
    if ($f) {
        if (!str_starts_with($f->type, 'image/')) {
            $_err['photo'] = 'Must be image';
        }
        else if ($f->size > 1 * 1024 * 1024) {
            $_err['photo'] = 'Maximum 1MB';
        }
    }

    // DB operation
    if (!$_err) {
        

        // (1) Delete and save photo --> optional
        if ($f) {
            unlink("../photo/$photo");
            $photo = save_photo($f, '../photo');
        }
        
        // (2) Update user (email, name, photo)
        // TODO
        $stm = $_db->prepare('
            UPDATE user
            SET email = ?, name =?, profile_photo = ?
            WHERE user_id = ?
        ');
        $stm->execute([$email,$name,$photo,$_user->user_id]);

        // (3) Update global user object
        $_user->email = $email;
        $_user->name  = $name;
        $_user->profile_photo = $photo;


        temp('info', 'Record updated');
        redirect('/');
    }
}

// ----------------------------------------------------------------------------

$_title = 'User | Profile';

?>

<form method="post" class="form" enctype="multipart/form-data">
    <label for="email">Email</label>
    <?= html_text('email', 'maxlength="100"') ?>
    <?= err('email') ?>

    <label for="name">Name</label>
    <?= html_text('name', 'maxlength="100"') ?>
    <?= err('name') ?>

    <label for="photo">Photo</label>
    <label class="upload" tabindex="0">
        <?= html_file('photo', 'image/*', 'hidden') ?>
        <img src="/photo/<?= $photo ?>">
    </label>
    <?= err('photo') ?>

    <section>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '../_foot.php';