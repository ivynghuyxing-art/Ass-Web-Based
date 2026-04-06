<?php
include '_base.php';

$token = req('token');
if (!$token) {
    temp('error', 'Invalid verification link.');
    redirect('login.php');
}

// Fetch the token and user information
$stm = $_db->prepare('
    SELECT vt.*, u.user_id 
    FROM verification_tokens vt
    JOIN user u ON u.user_id = vt.user_id
    WHERE vt.token = ? AND vt.expire > NOW()
');
$stm->execute([$token]);
$verification = $stm->fetch();

if (!$verification) {
    temp('error', 'The verification link is invalid or has expired.');
    redirect('login.php');
}

// Mark the user as verified (valid = 1)
$stm = $_db->prepare('
    UPDATE user SET valid = 1 WHERE user_id = ?
');
$stm->execute([$verification->user_id]);

// Delete the token
$stm = $_db->prepare('
    DELETE FROM verification_tokens WHERE token = ?
');
$stm->execute([$token]);

temp('info', 'Your email has been verified. You can now log in.');
redirect('login.php');
?>