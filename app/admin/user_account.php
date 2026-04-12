<?php
include_once('../_base.php');

try {
    $stmt = $_db->query("SELECT user_id, name, email, role, valid FROM user");
    $users = $stmt ? $stmt->fetchAll() : [];
} catch (PDOException $e) {
    echo "<p style='color:red'>Database error: " . $e->getMessage() . "</p>";
    $users = [];
}
?>

<div class="dashboard-section">
  <h2>👥 User Accounts</h2>
  <p class="muted">Manage User Account</p>
  
  <table class="styled-table">
    <thead>
      <tr>
        <th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($users as $user): ?>
      <tr>
        <td><?= $user->user_id ?></td>
        <td><?= $user->name ?></td>
        <td><?= $user->email ?></td>
        <td>
          <span class="badge <?= $user->role ?>">
            <?= ucfirst($user->role) ?>
          </span>
        </td>
        <td>
          <span class="status <?= $user->valid ? 'active' : 'inactive' ?>">
            <?= $user->valid ? 'Active' : 'Inactive' ?>
          </span>
        </td>
        <td>
          <a href="edit_user.php?id=<?= $user->user_id ?>" class="btn">✏️ Edit</a>
          <a href="delete_user.php?id=<?= $user->user_id ?>" class="btn danger">🗑️ Delete</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
