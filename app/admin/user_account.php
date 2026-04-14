<?php
include_once('../_base.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;
    $user_id = $_POST['user_id'] ?? null;

    if ($user_id && $action) {
        try {
            if ($action === 'block') {
                $stmt = $_db->prepare("UPDATE user SET valid = 0 WHERE user_id = ?");
                $_SESSION['message'] = "User ID $user_id has been blocked.";
            } else {
                $stmt = $_db->prepare("UPDATE user SET valid = 1 WHERE user_id = ?");
                $_SESSION['message'] = "User ID $user_id has been unblocked.";
            }

            $stmt->execute([$user_id]);

            header("Location: admin_panel.php?page=users");
            exit();
        } catch (PDOException $e) {
            $_SESSION['message'] = "Database error: " . $e->getMessage();
            header("Location: admin_panel.php?page=users");
            exit();
        }
    }
}

try {
    $stmt = $_db->prepare("
        SELECT u.user_id, u.name, u.email, u.gender, m.name AS membership, u.valid,
               COUNT(o.orders_id) AS total_orders
        FROM user u
        LEFT JOIN membership m ON u.membership_id = m.membership_id
        LEFT JOIN orders o ON u.user_id = o.user_id
        WHERE u.role = 'customer'
        GROUP BY u.user_id, u.name, u.email, u.gender, m.name, u.valid
    ");
    $stmt->execute();
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $users = [];
}
?>

<?php if (isset($_SESSION['message'])): ?>
    <div style="padding: 10px; margin: 10px 0; background: #e8f5e9; border-left: 4px solid #4caf50;">
        <?= htmlspecialchars($_SESSION['message']) ?>
    </div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<div class="dashboard-section">
  <h2>👥 User Accounts</h2>
  <p class="muted">Manage User Account</p>

  <div id="messageBox" class="alert" style="display: none;"></div>
  
  <table class="styled-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Gender</th>
        <th>Membership</th>
        <th>Total Orders</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
            <?php foreach($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user->user_id) ?></td>
                <td><?= htmlspecialchars($user->name) ?></td>
                <td><?= htmlspecialchars($user->email) ?></td>
                <td><?= htmlspecialchars($user->gender ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($user->membership ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($user->total_orders) ?></td>
                
                <td>
                    <span class="status <?= $user->valid ? 'active' : 'inactive' ?>">
                        <?= $user->valid ? 'Active' : 'Inactive' ?>
                    </span>
                </td>
                <td>
                    <div class="action-buttons">
                    <?php if ($user->valid): ?>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Do you want to block this user？')">
                            <input type="hidden" name="action" value="block">
                            <input type="hidden" name="user_id" value="<?= $user->user_id ?>">
                            <button type="submit" class="btn danger">🚫 Block</button>
                        </form>
                    <?php else: ?>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Do you want to unblock this user？')">
                            <input type="hidden" name="action" value="unblock">
                            <input type="hidden" name="user_id" value="<?= $user->user_id ?>">
                            <button type="submit" class="btn success">✅ Unblock</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>