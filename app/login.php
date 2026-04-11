<?php
$title = 'Checkout';
$_title = '';
include '../customer_header.php';

if (!isset($_SESSION['user'])) {
    temp('info', 'Please login to checkout');
    redirect('/login.php');
}

$user_id = $_SESSION['user']->user_id;

$cart = $_db->prepare('SELECT c.*, COALESCE(SUM(ci.quantity),0) AS item_qty FROM cart c LEFT JOIN cart_item ci ON c.cart_id = ci.cart_id WHERE c.user_id = ? GROUP BY c.cart_id');
$cart->execute([$user_id]);
$cart = $cart->fetch();

$items = [];
if ($cart && $cart->item_qty > 0) {
    $items = $_db->prepare('SELECT ci.*, p.product_name, p.price AS unit_price, p.image FROM cart_item ci JOIN product p ON ci.product_id = p.product_id WHERE ci.cart_id = ?');
    $items->execute([$cart->cart_id]);
    $items = $items->fetchAll();
}

if (!$cart || $cart->item_qty == 0) {
    temp('info', 'Your cart is empty. Please add products before checkout.');
    redirect('/product/viewproduct.php');
}

$shipping_fee = 5.00;
$grand_total = $cart->total_price + $shipping_fee;

if (is_post()) {
    $recipient_name = trim(req('recipient_name'));
    $phone          = trim(req('phone'));
    $address_line1  = trim(req('address_line1'));
    $address_line2  = trim(req('address_line2'));
    $postal_code    = trim(req('postal_code'));
    $city           = trim(req('city'));
    $state          = trim(req('state'));

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

        $_db->prepare('INSERT INTO orders (orders_id, user_id, total_price, order_date, status, shipping_fee, recipient_name, phone, address_line1, address_line2, postal_code, city, state) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)')
            ->execute([$orders_id, $user_id, $grand_total, date('Y-m-d'), 'Pending', $shipping_fee,
                       $recipient_name, $phone, $address_line1, $address_line2, $postal_code, $city, $state]);

        foreach ($items as $item) {
            $_db->prepare('INSERT INTO orders_item (orders_item_id, orders_id, product_id, price, quantity) VALUES (?,?,?,?,?)')
                ->execute([$orders_item_id++, $orders_id, $item->product_id, $item->unit_price, $item->quantity]);

            $_db->prepare('UPDATE product SET stock_quantity = stock_quantity - ? WHERE product_id = ?')
                ->execute([$item->quantity, $item->product_id]);
        }

        $_db->prepare('DELETE FROM cart_item WHERE cart_id = ?')->execute([$cart->cart_id]);
        $_db->prepare('UPDATE cart SET total_price=0, total_quantity=0 WHERE cart_id = ?')->execute([$cart->cart_id]);

        temp('info', 'Order placed successfully!');
        redirect('/customer/order.php');
    }
}

$malaysia_states = [
    'Johor','Kedah','Kelantan','Melaka','Negeri Sembilan',
    'Pahang','Perak','Perlis','Pulau Pinang','Sabah',
    'Sarawak','Selangor','Terengganu','Kuala Lumpur','Labuan','Putrajaya'
];
?>

<style>
.checkout-wrapper {
    display: grid;
    grid-template-columns: 1fr 420px;
    gap: 40px;
    max-width: 1100px;
    margin: 30px auto 60px;
    padding: 0 24px;
    align-items: start;
}

.checkout-left h2 {
    font-size: 26px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 6px;
}

.checkout-left .section-divider {
    border: none;
    border-top: 1px solid #ddd;
    margin: 10px 0 28px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 7px;
}

.form-group label .required {
    color: darkred;
    margin-left: 2px;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 11px 14px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    color: #333;
    background: #fff;
    box-sizing: border-box;
    transition: border-color 0.2s;
    appearance: none;
    -webkit-appearance: none;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: darkred;
    box-shadow: 0 0 0 3px rgba(139,0,0,0.08);
}

.form-group input.is-error,
.form-group select.is-error {
    border-color: #e53e3e;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.select-wrapper {
    position: relative;
}

.select-wrapper::after {
    content: '▾';
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    color: #666;
    font-size: 14px;
}

.btn-place-order {
    width: 100%;
    padding: 14px;
    background: darkred;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    margin-top: 10px;
    transition: background 0.2s;
}

.btn-place-order:hover {
    background: rgb(173,8,8);
}

.btn-back-cart {
    display: block;
    text-align: center;
    margin-top: 12px;
    color: darkred;
    font-size: 14px;
    text-decoration: none;
}

.btn-back-cart:hover {
    text-decoration: underline;
}

.checkout-right {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.checkout-panel {
    background: #fff;
    border: 1px solid #e8e8e8;
    border-radius: 12px;
    padding: 24px;
}

.checkout-panel h3 {
    font-size: 20px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 16px;
}

.payment-notice {
    color: darkred;
    font-size: 13px;
    margin-bottom: 14px;
    font-weight: 500;
}

.payment-option {
    display: flex;
    align-items: center;
    gap: 14px;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 14px 16px;
    cursor: pointer;
    transition: border-color 0.2s;
}

.payment-option:hover { border-color: darkred; }

.payment-option input[type="radio"] {
    accent-color: darkred;
    width: 18px;
    height: 18px;
    flex-shrink: 0;
    margin-left: auto;
}

.payment-option-logo {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    background: #1a1a2e;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.payment-option-logo span {
    color: white;
    font-size: 10px;
    font-weight: 700;
    text-align: center;
    line-height: 1.2;
}

.payment-option-info strong {
    display: block;
    font-size: 15px;
    color: #1a1a1a;
}

.payment-option-info small {
    color: #777;
    font-size: 12px;
}

.payment-badges {
    display: flex;
    gap: 6px;
    margin-top: 5px;
}

.payment-badge {
    background: #f0f0f0;
    border-radius: 4px;
    padding: 2px 7px;
    font-size: 11px;
    color: #555;
    font-weight: 600;
}

.coupon-row {
    display: flex;
    gap: 10px;
}

.coupon-row input {
    flex: 1;
    padding: 11px 14px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    box-sizing: border-box;
}

.coupon-row input:focus {
    outline: none;
    border-color: darkred;
}

.coupon-row button {
    padding: 11px 20px;
    background: #1a1a1a;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    letter-spacing: 0.05em;
    transition: background 0.2s;
}

.coupon-row button:hover { background: #333; }

.order-item-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}

.order-item-row:last-of-type { border-bottom: none; }

.order-item-img {
    width: 54px;
    height: 54px;
    object-fit: contain;
    border-radius: 6px;
    background: #f5f5f5;
    border: 1px solid #eee;
    flex-shrink: 0;
}

.order-item-info { flex: 1; }

.order-item-info .item-name {
    font-size: 13px;
    font-weight: 600;
    color: #1a1a1a;
    line-height: 1.3;
}

.order-item-info .item-qty {
    font-size: 12px;
    color: #888;
    margin-top: 2px;
}

.order-item-price {
    font-size: 14px;
    font-weight: 700;
    color: #1a1a1a;
    white-space: nowrap;
}

.summary-totals {
    margin-top: 16px;
    border-top: 1px solid #eee;
    padding-top: 14px;
}

.summary-totals .total-row {
    display: flex;
    justify-content: space-between;
    font-size: 14px;
    color: #555;
    margin-bottom: 8px;
}

.summary-totals .total-row.grand {
    font-size: 16px;
    font-weight: 700;
    color: #1a1a1a;
    border-top: 1px solid #ddd;
    padding-top: 12px;
    margin-top: 6px;
}

@media (max-width: 860px) {
    .checkout-wrapper { grid-template-columns: 1fr; }
    .checkout-right { order: -1; }
}
</style>

<div class="title">
    <h2>Checkout</h2>
</div>

<div class="checkout-wrapper">

    <!-- ══ LEFT: Shipping Address Form ══ -->
    <div class="checkout-left">
        <h2>Shipping Address</h2>
        <hr class="section-divider">

        <form method="post">

            <div class="form-group">
                <label>Full Name <span class="required">*</span></label>
                <input type="text" name="recipient_name"
                       value="<?= encode($recipient_name ?? '') ?>"
                       class="<?= isset($_err['recipient_name']) ? 'is-error' : '' ?>"
                       placeholder="e.g. Ahmad bin Ali">
                <?= err('recipient_name') ?>
            </div>

            <div class="form-group">
                <label>Phone <span class="required">*</span></label>
                <input type="text" name="phone"
                       value="<?= encode($phone ?? '') ?>"
                       class="<?= isset($_err['phone']) ? 'is-error' : '' ?>"
                       placeholder="e.g. 0123456789">
                <?= err('phone') ?>
            </div>

            <div class="form-group">
                <label>Address Line 1 <span class="required">*</span></label>
                <input type="text" name="address_line1"
                       value="<?= encode($address_line1 ?? '') ?>"
                       class="<?= isset($_err['address_line1']) ? 'is-error' : '' ?>"
                       placeholder="Street address, unit number">
                <?= err('address_line1') ?>
            </div>

            <div class="form-group">
                <label>Address Line 2</label>
                <input type="text" name="address_line2"
                       value="<?= encode($address_line2 ?? '') ?>"
                       placeholder="Apartment, suite, etc. (optional)">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Postal Code <span class="required">*</span></label>
                    <input type="text" name="postal_code"
                           value="<?= encode($postal_code ?? '') ?>"
                           class="<?= isset($_err['postal_code']) ? 'is-error' : '' ?>"
                           placeholder="e.g. 10000">
                    <?= err('postal_code') ?>
                </div>

                <div class="form-group">
                    <label>City <span class="required">*</span></label>
                    <input type="text" name="city"
                           value="<?= encode($city ?? '') ?>"
                           class="<?= isset($_err['city']) ? 'is-error' : '' ?>"
                           placeholder="e.g. George Town">
                    <?= err('city') ?>
                </div>
            </div>

            <div class="form-group">
                <label>State <span class="required">*</span></label>
                <div class="select-wrapper">
                    <select name="state" class="<?= isset($_err['state']) ? 'is-error' : '' ?>">
                        <option value="">Select your state</option>
                        <?php foreach ($malaysia_states as $s): ?>
                            <option value="<?= $s ?>" <?= ($state ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?= err('state') ?>
            </div>

            <button type="submit" class="btn-place-order"
                    onclick="return confirm('Confirm and place order?')">
                Place Order
            </button>
            <a href="/customer/cart.php" class="btn-back-cart">← Back to cart</a>

        </form>
    </div>

    <!-- ══ RIGHT: Panels ══ -->
    <div class="checkout-right">

        <!-- Payment Methods -->
        <div class="checkout-panel">
            <h3>Payment Methods</h3>
            <p class="payment-notice">Please input the shipping address</p>
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

        <!-- Coupon -->
        <div class="checkout-panel">
            <h3>Coupon</h3>
            <div class="coupon-row">
                <input type="text" placeholder="Coupon code">
                <button type="button">APPLY</button>
            </div>
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
                    <span>RM <?= number_format($cart->total_price, 2) ?></span>
                </div>
                <div class="total-row">
                    <span>Shipping</span>
                    <span>RM <?= number_format($shipping_fee, 2) ?></span>
                </div>
                <div class="total-row grand">
                    <span>Grand Total</span>
                    <span>RM <?= number_format($grand_total, 2) ?></span>
                </div>
            </div>
        </div>

    </div><!-- /.checkout-right -->
</div><!-- /.checkout-wrapper -->

<?php include '../_foot.php'; ?>