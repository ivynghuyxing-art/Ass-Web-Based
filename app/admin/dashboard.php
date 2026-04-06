<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-dark px-3">
    <span class="navbar-brand">Stationary Admin</span>
    <a href="../logout.php" class="btn btn-danger">Logout</a>
</nav>
<div class="d-flex">
    <div class="bg-light p-3" style="width:200px;height:100vh;">
        <h5>Menu</h5>
        <ul class="nav flex-column">
            <li class="nav-item"><a href="dashboard.php" class="nav-link">Dashboard</a></li>
            <li class="nav-item"><a href="users.php" class="nav-link">Users</a></li>
            <li class="nav-item"><a href="products.php" class="nav-link">Products</a></li>
        </ul>
    </div>

    <div class="p-4" style="width:100%;">
    <?php

?>

<h2>Dashboard</h2>

<div class="row">
    <div class="col-md-4">
        <div class="card p-3 bg-primary text-white">
            <h4>Total Users</h4>
            <p><?php echo $totalUsers; ?></p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3 bg-success text-white">
            <h4>Total Products</h4>
            <p><?php echo $totalProducts; ?></p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3 bg-warning text-white">
            <h4>Total Orders</h4>
            <p><?php echo $totalOrders; ?></p>
        </div>
    </div>
</div>

</div></div> <!-- close sidebar -->
</body>
</html>
<?php
include 'db.php';
include 'header.php';
include 'sidebar.php';

$stmt = $pdo->query("SELECT * FROM `user`");
?>

<h2>Users</h2>

<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Action</th>
    </tr>

    <?php while($row = $stmt->fetch()): ?>
    <tr>
        <td><?= $row['user_id'] ?></td>
        <td><?= $row['name'] ?></td>
        <td><?= $row['email'] ?></td>
        <td>
            <a href="delete_user.php?id=<?= $row['user_id'] ?>" class="btn btn-danger">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</div></div>
</body>
</html>

