<?php
$title = 'Checkout';
$_title = '';
include '../_base.php';

if (!isset($_SESSION['user'])) {
    temp('info', 'Please login to checkout');
    redirect('/login.php');
}

$user_id = $_SESSION['user']->user_id;

$cart = $_db->prepare('SELECT c.*, COALESCE(SUM(ci.quantity),0) AS item_qty FROM cart c LEFT JOIN cart_item ci ON c.cart_id = ci.cart_id WHERE c.user_id = ? GROUP BY c.cart_id');
$cart->execute([$user_id]);
$cart = $cart->fetch();

$selected_items = $_SESSION['checkout_items'] ?? [];

if (empty($selected_items)) {
    temp('info', 'No items selected. Please select items to checkout.');
    redirect('/customer/cart.php');
}

if (!$cart || $cart->item_qty == 0) {
    temp('info', 'Your cart is empty. Please add products before checkout.');
    redirect('/product/viewproduct.php');
}

$placeholders = implode(',', array_fill(0, count($selected_items), '?'));
$params       = array_merge($selected_items, [$cart->cart_id]);

$items = $_db->prepare("
    SELECT ci.*, p.product_name, p.price AS unit_price, p.image
    FROM cart_item ci
    JOIN product p ON ci.product_id = p.product_id
    WHERE ci.cart_item_id IN ($placeholders)
      AND ci.cart_id = ?
      AND p.is_active = 1
");
$items->execute($params);
$items = $items->fetchAll();

if (empty($items)) {
    temp('info', 'Selected items are no longer available.');
    redirect('/customer/cart.php');
}

$selected_subtotal = 0;
foreach ($items as $item) {
    $selected_subtotal += $item->unit_price * $item->quantity;
}

$shipping_fee    = 5.00;
$discount_amount = 0;
$voucher         = null;
$voucher_msg     = '';
$voucher_error   = '';

// ── 提前读取地址资料（POST 或空值），保证回填 ──
$recipient_name = trim(req('recipient_name', ''));
$phone          = trim(req('phone', ''));
$address_line1  = trim(req('address_line1', ''));
$address_line2  = trim(req('address_line2', ''));
$postal_code    = trim(req('postal_code', ''));
$city           = trim(req('city', ''));
$state          = trim(req('state', ''));

// Restore voucher from session if already applied
if (isset($_SESSION['applied_voucher'])) {
    $v = $_db->prepare('SELECT * FROM voucher WHERE code = ?');
    $v->execute([$_SESSION['applied_voucher']]);
    $v = $v->fetch();
    if ($v) {
        $voucher         = $v;
        $discount_amount = min($v->discount_amount, $selected_subtotal);
        $voucher_msg     = 'Voucher applied: -RM ' . number_format($discount_amount, 2);
    }
}

$grand_total = $selected_subtotal + $shipping_fee - $discount_amount;

if (is_post()) {
    $action = req('action');

    // ── APPLY VOUCHER ──
    if ($action === 'apply_voucher') {
        $code = strtoupper(trim(req('voucher_code')));

        if (!$code) {
            $voucher_error = 'Please enter a voucher code.';
        } else {
            $v = $_db->prepare('SELECT * FROM voucher WHERE code = ?');
            $v->execute([$code]);
            $v = $v->fetch();

            if (!$v) {
                $voucher_error = 'Invalid voucher code.';
            } else if ($v->started_date && date('Y-m-d') < $v->started_date) {
                $voucher_error = 'This voucher is not active yet.';
            } else if ($v->expired_date && date('Y-m-d') > $v->expired_date) {
                $voucher_error = 'This voucher has expired.';
            } else if ($v->usage_limit !== null && $v->usage_count >= $v->usage_limit) {
                $voucher_error = 'This voucher has reached its usage limit.';
            } else if ($selected_subtotal < $v->minimum_purchase_amount) {
                $voucher_error = 'Minimum purchase of RM ' . number_format($v->minimum_purchase_amount, 2) . ' required.';
            } else {
                $voucher                     = $v;
                $discount_amount             = min($v->discount_amount, $selected_subtotal);
                $voucher_msg                 = 'Voucher applied: -RM ' . number_format($discount_amount, 2);
                $_SESSION['applied_voucher'] = $code;
            }
        }

        $grand_total = $selected_subtotal + $shipping_fee - $discount_amount;
    }

    // ── REMOVE VOUCHER ──
    // ✅ 不再 redirect，直接清除 session 并留在页面，地址资料通过 hidden inputs 保留
    if ($action === 'remove_voucher') {
        unset($_SESSION['applied_voucher']);
        $voucher         = null;
        $discount_amount = 0;
        $voucher_msg     = '';
        $grand_total     = $selected_subtotal + $shipping_fee;
    }

    // ── PLACE ORDER ──
    if ($action === 'place_order') {
        $voucher_code = $voucher ? $voucher->code : null;

        if (!$recipient_name) {
            $_err['recipient_name'] = 'Required';
        }

        if (!$phone) {
            $_err['phone'] = 'Required';
        } else if (!preg_match('/^[0-9]{10,11}$/', $phone)) {
            $_err['phone'] = 'Invalid phone number';
        }

        if (!$address_line1) {
            $_err['address_line1'] = 'Required';
        }

        if (!$postal_code) {
            $_err['postal_code'] = 'Required';
        } else if (!preg_match('/^[0-9]{5}$/', $postal_code)) {
            $_err['postal_code'] = 'Invalid postal code';
        }

        if (!$city) {
            $_err['city'] = 'Required';
        }

        if (!$state) {
            $_err['state'] = 'Required';
        }

        if (!$_err) {
            $orders_id      = $_db->query('SELECT COALESCE(MAX(orders_id),0) + 1 FROM orders')->fetchColumn();
            $orders_item_id = $_db->query('SELECT COALESCE(MAX(orders_item_id),0) + 1 FROM orders_item')->fetchColumn();

            $_db->prepare('INSERT INTO orders (orders_id, user_id, total_price, order_date, status, shipping_fee, recipient_name, phone, address_line1, address_line2, postal_code, city, state, voucher_code, discount_amount) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')
                ->execute([$orders_id, $user_id, $grand_total, date('Y-m-d'), 'Pending', $shipping_fee,
                           $recipient_name, $phone, $address_line1, $address_line2, $postal_code, $city, $state,
                           $voucher_code, $discount_amount]);

            foreach ($items as $item) {
                $_db->prepare('INSERT INTO orders_item (orders_item_id, orders_id, product_id, price, quantity) VALUES (?,?,?,?,?)')
                    ->execute([$orders_item_id++, $orders_id, $item->product_id, $item->unit_price, $item->quantity]);

                $_db->prepare('UPDATE product SET stock_quantity = stock_quantity - ? WHERE product_id = ?')
                    ->execute([$item->quantity, $item->product_id]);

                $_db->prepare('DELETE FROM cart_item WHERE cart_item_id = ? AND cart_id = ?')
                    ->execute([$item->cart_item_id, $cart->cart_id]);
            }

            recalcCart($cart->cart_id);

            if ($voucher) {
                $_db->prepare('UPDATE voucher SET usage_count = usage_count + 1 WHERE voucher_id = ?')
                    ->execute([$voucher->voucher_id]);
            }

            unset($_SESSION['checkout_items']);
            unset($_SESSION['applied_voucher']);

            redirect('/customer/payment.php?orders_id=' . $orders_id);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Untitled' ?></title>
    <link rel="shortcut icon" href="/images/favicon.png">
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/js/app.js"></script>
</head>
<body>
<div class="title">
    <h2>Cozy Hub</h2><hr>
</div>

<div class="checkout-wrapper">

    <div class="checkout-left">
        <h2>Shipping Address</h2>
        <hr class="section-divider">

        <form method="post" id="place-order-form">
            <input type="hidden" name="action" value="place_order">

            <div class="form-group">
                <label>Full Name <span class="required">*</span></label>
                <input type="text" name="recipient_name" id="f_recipient_name"
                       value="<?= encode($recipient_name) ?>"
                       class="<?= isset($_err['recipient_name']) ? 'is-error' : '' ?>"
                       placeholder="e.g. Ahmad bin Ali">
                <?= err('recipient_name') ?>
            </div>

            <div class="form-group">
                <label>Phone <span class="required">*</span></label>
                <input type="text" name="phone" id="f_phone"
                       value="<?= encode($phone) ?>"
                       class="<?= isset($_err['phone']) ? 'is-error' : '' ?>"
                       placeholder="e.g. 0123456789">
                <?= err('phone') ?>
            </div>

            <div class="form-group">
                <label>Address Line 1 <span class="required">*</span></label>
                <input type="text" name="address_line1" id="f_address_line1"
                       value="<?= encode($address_line1) ?>"
                       class="<?= isset($_err['address_line1']) ? 'is-error' : '' ?>"
                       placeholder="Street address, unit number">
                <?= err('address_line1') ?>
            </div>

            <div class="form-group">
                <label>Address Line 2</label>
                <input type="text" name="address_line2" id="f_address_line2"
                       value="<?= encode($address_line2) ?>"
                       placeholder="Apartment, suite, etc. (optional)">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Postal Code <span class="required">*</span></label>
                    <input type="text" name="postal_code" id="f_postal_code"
                           value="<?= encode($postal_code) ?>"
                           class="<?= isset($_err['postal_code']) ? 'is-error' : '' ?>"
                           placeholder="e.g. 10000">
                    <?= err('postal_code') ?>
                </div>

                <div class="form-group">
                    <label>City <span class="required">*</span></label>
                    <input type="text" name="city" id="f_city"
                           value="<?= encode($city) ?>"
                           class="<?= isset($_err['city']) ? 'is-error' : '' ?>"
                           placeholder="e.g. George Town">
                    <?= err('city') ?>
                </div>
            </div>

            <div class="form-group">
                <label>State <span class="required">*</span></label>
                <div class="select-wrapper">
                    <select name="state" id="f_state" class="<?= isset($_err['state']) ? 'is-error' : '' ?>">
                        <option value="">Select your state</option>
                        <?php foreach ($malaysia_states as $s): ?>
                            <option value="<?= $s ?>" <?= $state === $s ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?= err('state') ?>
            </div>

            <button type="submit" class="btn-place-order">
                Proceed to Payment
            </button>
            <a href="/customer/cart.php" class="btn-back-cart">← Back to cart</a>
        </form>
    </div>

    <div class="checkout-right">

        <!-- Payment Methods -->
        <div class="checkout-panel">
            <h3>Payment Methods</h3>
            <label class="payment-option">
                <div class="payment-option-logo">
                    <span>PAY<br>MENT</span>
                </div>
                <div class="payment-option-info">
                    <strong>Online Banking (FPX)</strong>
                    <small>Support all Malaysian banks</small>
                    <div class="payment-badges">
                        <span class="payment-badge">Visa</span>
                        <span class="payment-badge">FPX</span>
                        <span class="payment-badge">Mastercard</span>
                    </div>
                </div>
                <input type="radio" name="payment" value="fpx" checked>
            </label>
        </div>

        <!-- Voucher -->
        <div class="checkout-panel">
            <h3>Voucher</h3>
            <?php if ($voucher): ?>
                <div class="voucher-success">
                    <span><?= htmlspecialchars($voucher_msg) ?></span>
                    <!-- ✅ Remove voucher: bawa balik address data -->
                    <form method="post" style="display:inline;" id="remove-voucher-form">
                        <input type="hidden" name="action" value="remove_voucher">
                        <input type="hidden" name="recipient_name" value="<?= encode($recipient_name) ?>">
                        <input type="hidden" name="phone"          value="<?= encode($phone) ?>">
                        <input type="hidden" name="address_line1"  value="<?= encode($address_line1) ?>">
                        <input type="hidden" name="address_line2"  value="<?= encode($address_line2) ?>">
                        <input type="hidden" name="postal_code"    value="<?= encode($postal_code) ?>">
                        <input type="hidden" name="city"           value="<?= encode($city) ?>">
                        <input type="hidden" name="state"          value="<?= encode($state) ?>">
                        <button type="submit" style="background:none;border:none;color:#dc2626;font-size:12px;cursor:pointer;text-decoration:underline;">Remove</button>
                    </form>
                </div>
            <?php else: ?>
                <!-- ✅ Apply voucher: bawa balik address data -->
                <form method="post" id="apply-voucher-form">
                    <input type="hidden" name="action" value="apply_voucher">
                    <input type="hidden" name="recipient_name" value="<?= encode($recipient_name) ?>">
                    <input type="hidden" name="phone"          value="<?= encode($phone) ?>">
                    <input type="hidden" name="address_line1"  value="<?= encode($address_line1) ?>">
                    <input type="hidden" name="address_line2"  value="<?= encode($address_line2) ?>">
                    <input type="hidden" name="postal_code"    value="<?= encode($postal_code) ?>">
                    <input type="hidden" name="city"           value="<?= encode($city) ?>">
                    <input type="hidden" name="state"          value="<?= encode($state) ?>">

                    <div class="coupon-row">
                        <input type="text" name="voucher_code"
                               value="<?= htmlspecialchars(req('voucher_code', '')) ?>"
                               placeholder="Voucher code">
                        <button type="submit">APPLY</button>
                    </div>
                    <?php if ($voucher_error): ?>
                        <div class="voucher-error"><?= htmlspecialchars($voucher_error) ?></div>
                    <?php endif; ?>
                </form>
            <?php endif; ?>
        </div>

        <!-- Order Summary -->
        <div class="checkout-panel">
            <h3>Order Summary (<?= count($items) ?>)</h3>

            <?php foreach ($items as $item): ?>
                <div class="order-item-row">
                    <img class="order-item-img"
                         src="../product_img/<?= htmlspecialchars($item->image) ?>"
                         alt="<?= htmlspecialchars($item->product_name) ?>">
                    <div class="order-item-info">
                        <div class="item-name"><?= htmlspecialchars($item->product_name) ?></div>
                        <div class="item-qty">Qty: <?= $item->quantity ?></div>
                    </div>
                    <div class="order-item-price">RM <?= number_format($item->unit_price * $item->quantity, 2) ?></div>
                </div>
            <?php endforeach; ?>

            <div class="summary-totals">
                <div class="total-row">
                    <span>Subtotal</span>
                    <span>RM <?= number_format($selected_subtotal, 2) ?></span>
                </div>
                <div class="total-row">
                    <span>Shipping</span>
                    <span>RM <?= number_format($shipping_fee, 2) ?></span>
                </div>
                <?php if ($discount_amount > 0): ?>
                    <div class="total-row discount">
                        <span>Voucher Discount</span>
                        <span>- RM <?= number_format($discount_amount, 2) ?></span>
                    </div>
                <?php endif; ?>
                <div class="total-row grand">
                    <span>Grand Total</span>
                    <span>RM <?= number_format($grand_total, 2) ?></span>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>