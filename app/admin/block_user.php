<?php
include_once('../_base.php');

// Get user ID
$user_id = $_GET['id'] ?? null;

if ($user_id) {
    try {
        $stmt = $_db->prepare("UPDATE user SET valid = 0 WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Prompt message
        $_SESSION['message'] = "User ID $user_id has been blocked.";

        // Return to user list
        header("Location: user_account.php");
        exit();
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>