// ✅ 全局函数，onclick="handleCheckout()" 才能调用到
function handleCheckout() {
    const checked = document.querySelectorAll('.select-item:checked');
    if (checked.length === 0) {
        alert('Please select at least one item to checkout.');
        return;
    }
    document.getElementById('cart-action').value = 'checkout';
    document.getElementById('cart-form').submit();
}

$(() => {

    // Photo preview
    $('label.upload input[type=file]').on('change', e => {
        const f = e.target.files[0];
        const img = $(e.target).closest('label').find('img')[0];

        if (!img) return;

        img.dataset.src ??= img.src;

        if (f?.type.startsWith('image/')) {
            img.src = URL.createObjectURL(f);
        }
        else {
            img.src = img.dataset.src;
            e.target.value = '';
        }
    });

    // admin.js
    window.addEventListener('DOMContentLoaded', () => {
        const userBtn = document.getElementById("user-btn");
        const profile = document.querySelector(".profile");

        if (userBtn && profile) {
            userBtn.onclick = () => {
                profile.classList.toggle("active");
            }
        }
    });

    // View more products for category sections
    $('.view-more-btn').on('click', function () {
        const button = $(this);
        const section = button.closest('.category-section');
        const extraItems = section.find('.extra-product');
        const totalExtras = extraItems.length;

        if (extraItems.first().is(':visible')) {
            extraItems.addClass('hidden');
            button.text(`View more ${totalExtras} ${totalExtras === 1 ? 'item' : 'items'}`);
        } else {
            extraItems.removeClass('hidden');
            button.text('Show less');
        }
    });

    // Header categories dropdown
    $(document).on('click', '.nav-dropdown .dropdown-toggle', function (event) {
        event.preventDefault();
        event.stopPropagation();
        const dropdown = $(this).closest('.nav-dropdown');
        $('.nav-dropdown').not(dropdown).removeClass('active');
        dropdown.toggleClass('active');
    });

    $(document).on('click', function () {
        $('.nav-dropdown').removeClass('active');
    });

    $('.nav-dropdown .dropdown-content').on('click', function (event) {
        event.stopPropagation();
    });

    // Banner Slider
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');

    function showSlide(index) {
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));

        slides[index].classList.add('active');
        dots[index].classList.add('active');
        currentSlide = index;
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }

    function prevSlide() {
        currentSlide = (currentSlide - 1 + slides.length) % slides.length;
        showSlide(currentSlide);
    }

    // Event listeners
    if (nextBtn && prevBtn && slides.length > 0) {
        nextBtn.addEventListener('click', nextSlide);
        prevBtn.addEventListener('click', prevSlide);

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => showSlide(index));
        });

        // Auto slide
        setInterval(nextSlide, 5000);
    }

    // ✅ Cart selection summary
    function initCartSelectionSummary() {
        const selectAll      = document.getElementById('select-all');
        const itemCheckboxes = Array.from(document.querySelectorAll('.select-item'));
        const quantityInputs = Array.from(document.querySelectorAll('.qty-input'));
        const selectedCountEl = document.getElementById('selected-count');
        const selectedTotalEl = document.getElementById('selected-total-display'); // ✅ 新 ID

        if (itemCheckboxes.length === 0) return;

        function updateSummary() {
            let count = 0;
            let total = 0;

            itemCheckboxes.forEach(cb => {
                if (!cb.checked) return;
                const cartItemId = cb.dataset.cartItemId;
                const unitPrice  = parseFloat(cb.dataset.unitPrice) || 0;
                const qtyInput   = document.querySelector(`.qty-input[data-cart-item-id="${cartItemId}"]`);
                const qty        = qtyInput ? parseInt(qtyInput.value) || 1 : 1;
                count++;
                total += unitPrice * qty;
            });

            if (selectedCountEl) selectedCountEl.textContent = count;
            if (selectedTotalEl) selectedTotalEl.textContent = 'RM ' + total.toFixed(2);

            // 全选 checkbox 状态
            if (selectAll) {
                selectAll.checked       = itemCheckboxes.length > 0 && itemCheckboxes.every(cb => cb.checked);
                selectAll.indeterminate = itemCheckboxes.some(cb => cb.checked) && !itemCheckboxes.every(cb => cb.checked);
            }
        }

        // 全选
        selectAll?.addEventListener('change', function () {
            itemCheckboxes.forEach(cb => cb.checked = this.checked);
            updateSummary();
        });

        itemCheckboxes.forEach(cb => cb.addEventListener('change', updateSummary));
        quantityInputs.forEach(input => input.addEventListener('input', updateSummary));

        updateSummary();
    }

    initCartSelectionSummary();
});

$(document).ready(function () {
    // 点击头像开关 dropdown
    $('.user-photo-dropdown img').on('click', function (e) {
        e.stopPropagation();
        $('.user-photo-dropdown').toggleClass('active');
    });

    // 点击其他地方关闭
    $(document).on('click', function () {
        $('.user-photo-dropdown').removeClass('active');
    });


//Before submitting the Apply or Remove voucher form, sync the latest values from the left address section into the hidden input fields.
function syncAddressToForm(form) {
    const get = id => document.getElementById(id)?.value ?? '';
    form.querySelector('[name=recipient_name]').value = get('f_recipient_name');
    form.querySelector('[name=phone]').value           = get('f_phone');
    form.querySelector('[name=address_line1]').value   = get('f_address_line1');
    form.querySelector('[name=address_line2]').value   = get('f_address_line2');
    form.querySelector('[name=postal_code]').value     = get('f_postal_code');
    form.querySelector('[name=city]').value            = get('f_city');
    form.querySelector('[name=state]').value           = get('f_state');
}

document.getElementById('apply-voucher-form')
    ?.addEventListener('submit', function () { syncAddressToForm(this); });

document.getElementById('remove-voucher-form')
    ?.addEventListener('submit', function () { syncAddressToForm(this); });

});