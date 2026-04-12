<?php
$title = 'Cart';
$_title = '';
include '../customer_header.php';

if (!isset($_SESSION['user'])) {
    temp('info', 'Please login to view your cart');
    redirect('/login.php');
}

$user_id = $_SESSION['user']->user_id;

$cart = ensureCart($user_id);

if (is_post()) {
    $action = req('action');

    if ($action === 'checkout') {
        $selected_items = req('selected_items', []);

        foreach (req('quantity', []) as $id => $qty) {
            $qty = (int)$qty;
            if ($qty < 1) $qty = 1;
            $item = $_db->prepare('SELECT ci.*, p.stock_quantity, p.price AS product_price FROM cart_item ci JOIN product p ON ci.product_id = p.product_id WHERE ci.cart_item_id = ? AND ci.cart_id = ?');
            $item->execute([$id, $cart->cart_id]);
            $item = $item->fetch();
            if (!$item) continue;
            if ($qty > $item->stock_quantity) {
                $qty = $item->stock_quantity;
            }
            $_db->prepare('UPDATE cart_item SET quantity = ?, price = ? WHERE cart_item_id = ?')->execute([$qty, $qty * $item->product_price, $id]);
        }
        recalcCart($cart->cart_id);

        if (empty($selected_items)) {
            temp('info', 'Please select at least one item to checkout.');
            redirect('/customer/cart.php');
        }
        $_SESSION['checkout_items'] = $selected_items;
        redirect('/customer/checkout.php');
    }

    if ($action === 'remove') {
        $cart_item_id = req('cart_item_id');
        $_db->prepare('DELETE FROM cart_item WHERE cart_item_id = ? AND cart_id = ?')->execute([$cart_item_id, $cart->cart_id]);
        recalcCart($cart->cart_id);
        temp('info', 'Item removed');
        redirect('/customer/cart.php');
    }
}

$items = $_db->prepare('SELECT ci.cart_item_id, ci.quantity, ci.price, p.product_name, p.image, p.price AS unit_price, p.stock_quantity FROM cart_item ci JOIN product p ON ci.product_id = p.product_id WHERE ci.cart_id = ? AND p.is_active = 1');
$items->execute([$cart->cart_id]);
$items = $items->fetchAll();
?>

<div class="title">
    <h2>My Cart</h2>
</div>

<section class="cart-page">
    <?php if ($items): ?>
        <form method="post" id="cart-form">
            <input type="hidden" name="action" id="cart-action" value="checkout">
            <input type="hidden" id="remove-item-id" name="cart_item_id" value="">

            <table class="cart-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"> All</th>
                        <th>Product</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th></th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <input type="checkbox"
                                       class="select-item"
                                       name="selected_items[]"
                                       value="<?= $item->cart_item_id ?>"
                                       data-unit-price="<?= $item->unit_price ?>"
                                       data-cart-item-id="<?= $item->cart_item_id ?>">
                            </td>
                            <td>
                                <img src="../product_img/<?= ($item->image) ?>" alt="<?= ($item->product_name) ?>" width="70" style="margin-right:10px;vertical-align:middle;">
                                <?= ($item->product_name) ?>
                            </td>
                            <td>RM <?= number_format($item->unit_price, 2) ?></td>
                            <td>
                                <input type="number"
                                       name="quantity[<?= $item->cart_item_id ?>]"
                                       value="<?= $item->quantity ?>"
                                       min="1"
                                       max="<?= $item->stock_quantity ?>"
                                       class="qty-input"
                                       data-cart-item-id="<?= $item->cart_item_id ?>">
                            </td>
                            <td></td>
                            <td>
                                <button type="button" class="btn-remove"
                                    onclick="
                                        document.getElementById('remove-item-id').value='<?= $item->cart_item_id ?>';
                                        document.getElementById('cart-action').value='remove';
                                        this.form.submit();
                                    ">
                                    Remove
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        
            <div class="cart-summary-bar">
                <div class="cart-summary-info">
        
                    <div class="summary-divider"></div>
                    <div class="summary-block">
                        <span class="summary-label">Total Items</span>
                        <span class="summary-value" id="selected-count">0</span>
                    </div>
                    
                    <div class="summary-divider"></div>
                    <div class="summary-block highlight">
                        <span class="summary-label">Total Price</span>
                        <span class="summary-value" id="selected-total-display">RM 0.00</span>
                    </div>
                </div>
                <button type="button" class="btn-success" onclick="handleCheckout()">
                    Proceed to Checkout
                </button>
            </div>

        </form>

    <?php else: ?>
        <div class="empty-cart">
            <p>Your cart is empty.</p>
            <a href="../product/viewproduct.php">Browse products</a>
        </div>
    <?php endif; ?>
</section>

<?php include '../_foot.php'; ?>