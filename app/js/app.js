// ===============================
// 🌍 GLOBAL FUNCTIONS
// ===============================

// Checkout
function handleCheckout() {
    const checked = document.querySelectorAll('.select-item:checked');

    if (checked.length === 0) {
        alert('Please select at least one item to checkout.');
        return;
    }

    const action = document.getElementById('cart-action');
    const form   = document.getElementById('cart-form');

    if (!action || !form) return;

    action.value = 'checkout';
    form.submit();
}

// Sync voucher address → hidden form
function syncAddressToForm(form) {
    if (!form) return;

    const get = id => document.getElementById(id)?.value ?? '';

    const set = (name, value) => {
        const el = form.querySelector(`[name=${name}]`);
        if (el) el.value = value;
    };

    set('recipient_name', get('f_recipient_name'));
    set('phone',          get('f_phone'));
    set('address_line1',  get('f_address_line1'));
    set('address_line2',  get('f_address_line2'));
    set('postal_code',    get('f_postal_code'));
    set('city',           get('f_city'));
    set('state',          get('f_state'));
}


// ===============================
// 🚀 MAIN INIT
// ===============================
$(() => {

    // =========================
    // 🏦 BANK LOGIN TOGGLE (FIXED)
    // =========================
    const bankSelect = document.getElementById('bankSelect');
    const loginBox   = document.getElementById('bankLogin');

    if (bankSelect && loginBox) {

        bankSelect.addEventListener('change', function () {

            if (this.value === '') {
                loginBox.style.display = 'none';

                // clear input when hidden
                const acc = document.getElementById('bankAccount');
                const pass = document.getElementById('bankPassword');

                if (acc) acc.value = '';
                if (pass) pass.value = '';

            } else {
                loginBox.style.display = 'block';
            }
        });
    }

    const payForm = document.getElementById('payForm');

        if (payForm) {
            payForm.addEventListener('submit', function (e) {

                const bank = document.getElementById('bankSelect')?.value;
                const acc  = document.getElementById('bankAccount')?.value.trim();
                const pass = document.getElementById('bankPassword')?.value.trim();

                if (!bank) {
                    e.preventDefault();
                    alert('Please choose a bank first');
                    return;
                }

                if (document.getElementById('bankLogin').style.display !== 'none') {

                    if (!acc || !pass) {
                        e.preventDefault();
                        alert('Please enter bank account and password');
                        return;
                    }
                }
            });
        }

    // =========================
    // 🖼️ PHOTO PREVIEW
    // =========================
    $('label.upload input[type=file]').on('change', function (e) {
        const f = e.target.files[0];
        const img = $(this).closest('label').find('img')[0];

        if (!img) return;

        img.dataset.src ??= img.src;

        if (f?.type?.startsWith('image/')) {
            img.src = URL.createObjectURL(f);
        } else {
            img.src = img.dataset.src;
            this.value = '';
        }
    });


    // =========================
    // 👤 PROFILE TOGGLE
    // =========================
    const userDropdown = document.querySelector('.user-photo-dropdown');
    const userImg = document.querySelector('.user-photo-dropdown img');

    if (userDropdown && userImg) {

        userImg.addEventListener('click', function (e) {
            e.stopPropagation();
            userDropdown.classList.toggle('active');
        });

        document.addEventListener('click', function () {
            userDropdown.classList.remove('active');
        });
    }


    // =========================
    // 📦 VIEW MORE PRODUCTS
    // =========================
    $('.view-more-btn').on('click', function () {
        const button = $(this);
        const section = button.closest('.category-section');
        const extraItems = section.find('.extra-product');
        const total = extraItems.length;

        if (!extraItems.length) return;

        if (extraItems.first().is(':visible')) {
            extraItems.addClass('hidden');
            button.text(`View more ${total} ${total === 1 ? 'item' : 'items'}`);
        } else {
            extraItems.removeClass('hidden');
            button.text('Show less');
        }
    });


    // =========================
    // 📂 DROPDOWN MENU
    // =========================
    $(document).on('click', '.nav-dropdown .dropdown-toggle', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const dropdown = $(this).closest('.nav-dropdown');
        $('.nav-dropdown').not(dropdown).removeClass('active');
        dropdown.toggleClass('active');
    });

    $(document).on('click', function () {
        $('.nav-dropdown').removeClass('active');
    });

    $(document).on('click', '.nav-dropdown .dropdown-content', function (e) {
        e.stopPropagation();
    });


    // =========================
    // 🛒 CART SUMMARY
    // =========================
    function initCartSelectionSummary() {

        const selectAll = document.getElementById('select-all');
        const items     = Array.from(document.querySelectorAll('.select-item'));

        const countEl   = document.getElementById('selected-count');
        const totalEl   = document.getElementById('selected-total-display');

        if (!items.length) return;

        function update() {
            let count = 0;
            let total = 0;

            items.forEach(cb => {
                if (!cb.checked) return;

                const price = parseFloat(cb.dataset.unitPrice) || 0;
                const qtyEl = document.querySelector(
                    `.qty-input[data-cart-item-id="${cb.dataset.cartItemId}"]`
                );

                const qty = qtyEl ? (parseInt(qtyEl.value) || 1) : 1;

                count++;
                total += price * qty;
            });

            if (countEl) countEl.textContent = count;
            if (totalEl) totalEl.textContent = 'RM ' + total.toFixed(2);

            if (selectAll) {
                selectAll.checked =
                    items.every(i => i.checked) && items.length > 0;

                selectAll.indeterminate =
                    items.some(i => i.checked) && !items.every(i => i.checked);
            }
        }

        selectAll?.addEventListener('change', function () {
            items.forEach(i => i.checked = this.checked);
            update();
        });

        items.forEach(i => i.addEventListener('change', update));

        document.querySelectorAll('.qty-input')
            .forEach(i => i.addEventListener('input', update));

        update();
    }

    initCartSelectionSummary();


    // =========================
    // 💰 VOUCHER FORM SYNC
    // =========================
    const applyForm  = document.getElementById('apply-voucher-form');
    const removeForm = document.getElementById('remove-voucher-form');

    if (applyForm) {
        applyForm.addEventListener('submit', function () {
            syncAddressToForm(this);
        });
    }

    if (removeForm) {
        removeForm.addEventListener('submit', function () {
            syncAddressToForm(this);
        });
    }

});