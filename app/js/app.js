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
    
    function initCartSelectionSummary() {
        const selectAll = document.getElementById('select-all');
        const itemCheckboxes = Array.from(document.querySelectorAll('.select-item'));
        const quantityInputs = Array.from(document.querySelectorAll('input[type="number"][name^="quantity"]'));
        const selectedCountEl = document.getElementById('selected-count');
        const selectedTotalEl = document.getElementById('selected-total');

        if (!selectAll && itemCheckboxes.length === 0) return;

        function updateSelectedSummary() {
            let count = 0;
            let total = 0;

            itemCheckboxes.forEach(checkbox => {
                const row = checkbox.closest('tr');
                const quantityInput = row?.querySelector('input[type="number"][name^="quantity"]');
                const unitPrice = parseFloat(checkbox.dataset.unitPrice || checkbox.dataset.price) || 0;
                const quantity = Number(quantityInput?.value) || 0;
                const lineTotal = unitPrice * quantity;

                if (checkbox.checked) {
                    count += 1;
                    total += lineTotal;
                }
            });

            if (selectedCountEl) selectedCountEl.textContent = count;
            if (selectedTotalEl) selectedTotalEl.textContent = total.toFixed(2);
        }

        itemCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                if (!checkbox.checked && selectAll) {
                    selectAll.checked = false;
                }
                if (checkbox.checked && selectAll) {
                    selectAll.checked = itemCheckboxes.every(item => item.checked);
                }
                updateSelectedSummary();
            });
        });

        quantityInputs.forEach(input => input.addEventListener('input', updateSelectedSummary));

        updateSelectedSummary();
    }

    initCartSelectionSummary();});

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
});