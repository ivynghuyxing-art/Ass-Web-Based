<?php
require '../_base.php';
$title = 'Admin Panel';

if(!isset($_SESSION['admin_id'])){
    header('Location: admin_login.php');
    exit();
}

$page=$_GET['page'] ?? 'dashboard';
require '../admin_header.php';

switch($page){
    case 'dashboard':
        include 'dashboard.php';
        break;
    
    case 'add_product':
        include 'add_product.php';
        break;

    case 'product':
        include 'product.php';
        break;

    case 'logout':
        include'admin_logout.php';
        break;

    default:
    echo "Page not founded!";
}?>

<!-- Flash message -->
    <div id="info"><?= temp('info') ?></div>
    </div>
    </body>
    </html>