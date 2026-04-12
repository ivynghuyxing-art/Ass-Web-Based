<?php
$title = 'Voucher Management';
$_title = 'Vouchers';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_voucher') {
    try {
        $voucher_id = $_POST['voucher_id'];

        $get_code = $_db->prepare('SELECT code FROM voucher WHERE voucher_id = ?');
        $get_code->execute([$voucher_id]);
        $voucher_code = $get_code->fetchColumn();
        
        if (!$voucher_code) {
            temp('error', 'Voucher not found!');
            redirect('admin_panel.php?page=vouchers');
            exit;
        }

        $check = $_db->prepare('SELECT COUNT(*) FROM orders WHERE voucher_code = ?');
        $check->execute([$voucher_code]);
        if ($check->fetchColumn() > 0) {
            temp('error', 'Cannot delete voucher that has been used in orders');
        } else {
            $stmt = $_db->prepare('DELETE FROM voucher WHERE voucher_id = ?');
            $stmt->execute([$voucher_id]);
            temp('info', 'Voucher deleted successfully!');
        }
    } catch (Exception $e) {
        temp('error', 'Delete failed: ' . $e->getMessage());
    }
    
    redirect('admin_panel.php?page=vouchers');
    exit;
}

if (isset($_GET['reset_count'])) {
    $voucher_id = $_GET['reset_count'];
    $stmt = $_db->prepare('UPDATE voucher SET usage_count = 0 WHERE voucher_id = ?');
    $stmt->execute([$voucher_id]);
    temp('info', 'Usage count reset successfully!');
    redirect('admin_panel.php?page=vouchers');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_voucher') {
    $code = strtoupper(trim($_POST['code']));
    $discount_amount = $_POST['discount_amount'];
    $started_date = !empty($_POST['started_date']) ? $_POST['started_date'] : null;
    $expired_date = !empty($_POST['expired_date']) ? $_POST['expired_date'] : null;
    $usage_limit = !empty($_POST['usage_limit']) ? $_POST['usage_limit'] : null;
    $minimum_purchase_amount = isset($_POST['minimum_purchase_amount']) ? $_POST['minimum_purchase_amount'] : 0;
    
    if (!$code) {
        $_err['code'] = 'Voucher code is required';
    } elseif (!preg_match('/^[A-Z0-9]+$/', $code)) {
        $_err['code'] = 'Only uppercase letters and numbers allowed';
    } else {
        $check = $_db->prepare('SELECT COUNT(*) FROM voucher WHERE code = ?');
        $check->execute([$code]);
        if ($check->fetchColumn() > 0) {
            $_err['code'] = 'Voucher code already exists';
        }
    }
    
    if (!$discount_amount || $discount_amount <= 0) {
        $_err['discount_amount'] = 'Valid discount amount is required';
    } elseif ($discount_amount > 999999.99) {
        $_err['discount_amount'] = 'Discount amount too high';
    }
    
    if ($started_date && $expired_date && $started_date > $expired_date) {
        $_err['expired_date'] = 'Expiry date must be after start date';
    }
    
    if ($usage_limit !== null && $usage_limit !== '' && ($usage_limit < 0 || $usage_limit > 999999)) {
        $_err['usage_limit'] = 'Usage limit must be between 0 and 999999';
    }
    
    if ($minimum_purchase_amount < 0) {
        $_err['minimum_purchase_amount'] = 'Minimum purchase amount cannot be negative';
    }
    
    if (!isset($_err) || empty($_err)) {
        $usage_limit_value = (empty($usage_limit) && $usage_limit !== '0') ? null : (int)$usage_limit;
        
        $stmt = $_db->prepare('INSERT INTO voucher (code, discount_amount, started_date, expired_date, usage_limit, minimum_purchase_amount, usage_count) VALUES (?, ?, ?, ?, ?, ?, 0)');
        $stmt->execute([$code, $discount_amount, $started_date, $expired_date, $usage_limit_value, $minimum_purchase_amount]);
        
        temp('info', 'Voucher added successfully!');
        redirect('admin_panel.php?page=vouchers');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_voucher') {
    $voucher_id = $_POST['voucher_id'];
    $code = strtoupper(trim($_POST['code']));
    $discount_amount = $_POST['discount_amount'];
    $started_date = !empty($_POST['started_date']) ? $_POST['started_date'] : null;
    $expired_date = !empty($_POST['expired_date']) ? $_POST['expired_date'] : null;
    $usage_limit = !empty($_POST['usage_limit']) ? $_POST['usage_limit'] : null;
    $minimum_purchase_amount = isset($_POST['minimum_purchase_amount']) ? $_POST['minimum_purchase_amount'] : 0;

    if (!$code) {
        $_err['code'] = 'Voucher code is required';
    } elseif (!preg_match('/^[A-Z0-9]+$/', $code)) {
        $_err['code'] = 'Only uppercase letters and numbers allowed';
    } else {
        $check = $_db->prepare('SELECT COUNT(*) FROM voucher WHERE code = ? AND voucher_id != ?');
        $check->execute([$code, $voucher_id]);
        if ($check->fetchColumn() > 0) {
            $_err['code'] = 'Voucher code already exists';
        }
    }
    
    if (!$discount_amount || $discount_amount <= 0) {
        $_err['discount_amount'] = 'Valid discount amount is required';
    }
    
    if ($started_date && $expired_date && $started_date > $expired_date) {
        $_err['expired_date'] = 'Expiry date must be after start date';
    }
    
    if (!isset($_err) || empty($_err)) {
        $usage_limit_value = (empty($usage_limit) && $usage_limit !== '0') ? null : (int)$usage_limit;
        
        $stmt = $_db->prepare('UPDATE voucher SET code = ?, discount_amount = ?, started_date = ?, expired_date = ?, usage_limit = ?, minimum_purchase_amount = ? WHERE voucher_id = ?');
        $stmt->execute([$code, $discount_amount, $started_date, $expired_date, $usage_limit_value, $minimum_purchase_amount, $voucher_id]);
        
        temp('info', 'Voucher updated successfully!');
        redirect('admin_panel.php?page=vouchers');
    }
}

$edit_voucher = null;
if (isset($_GET['edit'])) {
    $stmt = $_db->prepare('SELECT * FROM voucher WHERE voucher_id = ?');
    $stmt->execute([$_GET['edit']]);
    $edit_voucher = $stmt->fetch();
}

$vouchers = $_db->query('SELECT * FROM voucher ORDER BY voucher_id DESC')->fetchAll();
?>

<div class="voucher-management">
    <div id="info"><?= temp('info') ?></div>
    
    <!-- Add/Edit Voucher Form -->
    <div class="voucher-form-card">
        <h2><?= $edit_voucher ? 'Edit Voucher' : 'Add New Voucher' ?></h2>
        
        <form action="" method="POST" id="voucherForm">
            <input type="hidden" name="action" value="<?= $edit_voucher ? 'edit_voucher' : 'add_voucher' ?>">
            <?php if ($edit_voucher): ?>
                <input type="hidden" name="voucher_id" value="<?= $edit_voucher->voucher_id ?>">
            <?php endif; ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Voucher Code *</label>
                    <input type="text" name="code" 
                           value="<?= htmlspecialchars($edit_voucher->code ?? $_POST['code'] ?? '') ?>" 
                           placeholder="e.g., SAVE10, WELCOME20, COZY2026" 
                           required>
                    <small>Use uppercase letters and numbers only (no spaces or special characters)</small>
                    <?php if (isset($_err['code'])): ?>
                        <div class="error-message"><?= $_err['code'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label>Discount Amount (RM) *</label>
                    <input type="number" name="discount_amount" 
                           value="<?= $edit_voucher->discount_amount ?? $_POST['discount_amount'] ?? '' ?>" 
                           step="0.01" min="0.01" required>
                    <small>Fixed discount amount off the total purchase</small>
                    <?php if (isset($_err['discount_amount'])): ?>
                        <div class="error-message"><?= $_err['discount_amount'] ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" name="started_date" 
                           id="started_date"
                           value="<?= $edit_voucher->started_date ?? $_POST['started_date'] ?? '' ?>" 
                           min="<?= date('Y-m-d') ?>">
                    <small>Leave empty for immediate activation</small>
                </div>
                
                <div class="form-group">
                    <label>Expiry Date</label>
                    <input type="date" name="expired_date" 
                           id="expired_date"
                           value="<?= $edit_voucher->expired_date ?? $_POST['expired_date'] ?? '' ?>" 
                           min="<?= date('Y-m-d') ?>">
                    <small>Leave empty for no expiry date</small>
                    <?php if (isset($_err['expired_date'])): ?>
                        <div class="error-message"><?= $_err['expired_date'] ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Usage Limit</label>
                    <input type="number" name="usage_limit" 
                           value="<?= $edit_voucher->usage_limit ?? $_POST['usage_limit'] ?? '' ?>" 
                           min="1" placeholder="Unlimited">
                    <small>Maximum number of times this voucher can be used (leave empty for unlimited)</small>
                    <?php if (isset($_err['usage_limit'])): ?>
                        <div class="error-message"><?= $_err['usage_limit'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label>Minimum Purchase (RM)</label>
                    <input type="number" name="minimum_purchase_amount" 
                           value="<?= $edit_voucher->minimum_purchase_amount ?? $_POST['minimum_purchase_amount'] ?? '0' ?>" 
                           step="0.01" min="0">
                    <small>Minimum cart total required to use this voucher</small>
                    <?php if (isset($_err['minimum_purchase_amount'])): ?>
                        <div class="error-message"><?= $_err['minimum_purchase_amount'] ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn-submit"><?= $edit_voucher ? 'Update Voucher' : 'Add Voucher' ?></button>
                <?php if ($edit_voucher): ?>
                    <a href="admin_panel.php?page=vouchers" class="btn-cancel">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <!-- Vouchers List -->
    <div class="voucher-form-card">
        <h2>Existing Vouchers</h2>
        
        <?php if (count($vouchers) > 0): ?>
            <table class="voucher-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Code</th>
                        <th>Discount (RM)</th>
                        <th>Min Purchase</th>
                        <th>Valid Period</th>
                        <th>Usage</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vouchers as $v): 
                        $today = date('Y-m-d');
                        $is_valid = true;
                        $status_text = 'Active';
                        $status_class = 'status-active';
                        
                        if ($v->started_date && $today < $v->started_date) {
                            $is_valid = false;
                            $status_text = 'Upcoming';
                            $status_class = 'status-upcoming';
                        } elseif ($v->expired_date && $today > $v->expired_date) {
                            $is_valid = false;
                            $status_text = 'Expired';
                            $status_class = 'status-expired';
                        } elseif ($v->usage_limit !== null && $v->usage_count >= $v->usage_limit) {
                            $is_valid = false;
                            $status_text = 'Used Up';
                            $status_class = 'status-expired';
                        }
                        
                        $usage_percentage = 0;
                        if ($v->usage_limit) {
                            $usage_percentage = min(100, ($v->usage_count / $v->usage_limit) * 100);
                        }
                    ?>
                        <tr>
                            <td><?= $v->voucher_id ?></td>
                            <td><strong><?= htmlspecialchars($v->code) ?></strong></td>
                            <td>RM <?= number_format($v->discount_amount, 2) ?></td>
                            <td><?= $v->minimum_purchase_amount > 0 ? 'RM ' . number_format($v->minimum_purchase_amount, 2) : 'None' ?></td>
                            <td style="font-size: 12px;">
                                <?php if ($v->started_date && $v->expired_date): ?>
                                    <?= date('d/m/Y', strtotime($v->started_date)) ?><br>→ <?= date('d/m/Y', strtotime($v->expired_date)) ?>
                                <?php elseif ($v->started_date): ?>
                                    From: <?= date('d/m/Y', strtotime($v->started_date)) ?>
                                <?php elseif ($v->expired_date): ?>
                                    Until: <?= date('d/m/Y', strtotime($v->expired_date)) ?>
                                <?php else: ?>
                                    Always valid
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 5px;">
                                    <?php if ($v->usage_limit): ?>
                                        <div class="usage-progress">
                                            <div class="usage-progress-bar" style="width: <?= $usage_percentage ?>%"></div>
                                        </div>
                                        <span><?= $v->usage_count ?> / <?= $v->usage_limit ?></span>
                                    <?php else: ?>
                                        <span><?= $v->usage_count ?> / ∞</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge <?= $status_class ?>"><?= $status_text ?></span>
                            </td>
                            <td class="action-buttons">
                                <a href="admin_panel.php?page=vouchers&edit=<?= $v->voucher_id ?>" class="btn-edit">Edit</a>
                                <?php if ($v->usage_count > 0): ?>
                                    <a href="admin_panel.php?page=vouchers&reset_count=<?= $v->voucher_id ?>" class="btn-reset" onclick="return confirm('Reset usage count for this voucher?')">Reset</a>
                                <?php endif; ?>

                                <form method="POST" style="display:inline;" class="delete-form" onsubmit="return confirm('Delete this voucher? This action cannot be undone.');">
                                    <input type="hidden" name="action" value="delete_voucher">
                                    <input type="hidden" name="voucher_id" value="<?= $v->voucher_id ?>">
                                    <button type="submit" class="btn-delete">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; color: #888; padding: 40px;">No vouchers created yet. Use the form above to add your first voucher.</p>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    const startDateInput = document.getElementById('started_date');
    const endDateInput = document.getElementById('expired_date');
    
    function validateDates() {
        if (startDateInput?.value && startDateInput.value < today) {
            alert('Start date cannot be in the past!');
            startDateInput.value = '';
            return false;
        }
        if (endDateInput?.value && endDateInput.value < today) {
            alert('Expiry date cannot be in the past!');
            endDateInput.value = '';
            return false;
        }
        if (startDateInput?.value && endDateInput?.value && startDateInput.value > endDateInput.value) {
            alert('Start date must be before expiry date!');
            endDateInput.value = '';
            return false;
        }
        return true;
    }
    
    const form = document.getElementById('voucherForm');
    if (form) {
        form.addEventListener('submit', e => {
            if (!validateDates()) e.preventDefault();
        });
    }
});
</script>