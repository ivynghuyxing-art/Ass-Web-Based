<?php
$title = 'Payment';
$_title = '';
include '../_base.php';
require_once '../_base.php';

$order_id = req('orders_id');

// ❗ 如果没有 id，直接回去
if (!$order_id) {
    redirect('/customer/order_history.php');
}

// ✅ 更新 status
$_db->prepare("
    UPDATE orders 
    SET status = 'Paid'
    WHERE orders_id = ?
")->execute([$order_id]);

// 成功提示
$_SESSION['success'] = "Payment successful!";

// 跳回订单页
redirect('/customer/order_history.php');
if (!isset($_SESSION['user'])) {
    redirect('/login.php');
}

$orders_id = (int)req('orders_id');

if (!$orders_id) {
    redirect('/customer/order.php');
}

// Get order - make sure it belongs to this user and is still Pending
$order = $_db->prepare('SELECT * FROM orders WHERE orders_id = ? AND user_id = ? AND status = ?');
$order->execute([$orders_id, $_SESSION['user']->user_id, 'Pending']);
$order = $order->fetch();

if (!$order) {
    temp('info', 'Order not found.');
    redirect('/customer/order.php');
}

if (is_post()) {
    $action = req('action');

    if ($action === 'pay') {
        // Mark as Paid
        $_db->prepare('UPDATE orders SET status = ? WHERE orders_id = ?')
            ->execute(['Paid', $orders_id]);
        temp('info', 'Payment successful! Your order has been placed.');
        redirect('/customer/order_detail.php');
    }

    if ($action === 'cancel') {
        // Mark as Cancelled
        $_db->prepare('UPDATE orders SET status = ? WHERE orders_id = ?')
            ->execute(['Cancelled', $orders_id]);
        temp('info', 'Payment cancelled.');
        redirect('/customer/cart.php');
    }
}
?>

<!DOCTYPE html>
<html lang ="en">
<head>
    <meta charset ="UTF-8">
    <meta name="viewport" content= "width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Untitled' ?></title>
    <link rel = "shortcut icon" href="/images/favicon.png">
    <link rel = "stylesheet" href="/css/app.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/js/app.js"></script>
</head>
<body>

<div class="payment-page">
    <div class="payment-card">

        <div class="payment-card-header">
            <div class="bank-logo">🏦 FPX Online Banking</div>
            <h2>Confirm Payment</h2>
            <div class="subtitle">Secure payment powered by FPX</div>
        </div>

        <div class="payment-card-body">

            <div class="payment-detail-row">
                <span class="label">Order ID</span>
                <span class="value">#<?= str_pad($order->orders_id, 6, '0', STR_PAD_LEFT) ?></span>
            </div>
            <div class="payment-detail-row">
                <span class="label">Order Date</span>
                <span class="value"><?= date('d M Y', strtotime($order->order_date)) ?></span>
            </div>
            <div class="payment-detail-row">
                <span class="label">Recipient</span>
                <span class="value"><?= htmlspecialchars($order->recipient_name) ?></span>
            </div>
            <div class="payment-detail-row">
                <span class="label">Total Amount</span>
                <span class="value amount">RM <?= number_format($order->total_price, 2) ?></span>
            </div>

            <hr class="payment-divider">

            <div class="payment-notice-box">
                ⚠️ This is a simulated payment. No real transaction will occur.
            </div>

            <label class="bank-select-label">Select Your Bank</label>
            <select class="bank-select">
                <option>Maybank2u</option>
                <option>CIMB Clicks</option>
                <option>Public Bank</option>
                <option>RHB Now</option>
                <option>Hong Leong Connect</option>
                <option>AmBank</option>
                <option>Bank Islam</option>
                <option>BSN</option>
            </select>

            <form method="post">
                <input type="hidden" name="action" value="pay">
                <input type="hidden" name="orders_id" value="<?= $order->orders_id ?>">
                <button type="submit" class="btn-pay">Pay Now  RM <?= number_format($order->total_price, 2) ?></button>
            </form>

            <form method="post">
                <input type="hidden" name="action" value="cancel">
                <input type="hidden" name="orders_id" value="<?= $order->orders_id ?>">
                <button type="submit" class="btn-cancel-payment"
                        onclick="return confirm('Cancel this payment?')">
                    Cancel Payment
                </button>
            </form>

            <div class="secure-badge">
                <span>🔒 256-bit SSL Secured</span>
            </div>

        </div>
    </div>
</div>
</body>

