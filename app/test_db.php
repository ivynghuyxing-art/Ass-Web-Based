<?php
require '_base.php';

try {
    echo "Database connected successfully\n";
    $stm = $_db->query('SELECT COUNT(*) FROM user');
    echo "Users in database: " . $stm->fetchColumn() . "\n";

    // Test login query
    $stm = $_db->prepare('SELECT * FROM user WHERE email = ? AND password = SHA1(?)');
    $stm->execute(['admin@cozyhub.com', 'admin123']);
    $user = $stm->fetch();
    if ($user) {
        echo "Admin login test: SUCCESS\n";
        echo "User: " . $user->name . " (role: " . $user->role . ")\n";
    } else {
        echo "Admin login test: FAILED\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>