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
});