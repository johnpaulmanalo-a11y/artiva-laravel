/* ==========================================================================
   Artiva — admin dashboard behaviour
   ========================================================================== */

(function () {
    'use strict';

    function initSidebar() {
        const sidebar = document.querySelector('[data-admin-sidebar]');
        const burger = document.querySelector('[data-admin-nav-toggle]');
        const scrim = document.querySelector('[data-admin-nav-scrim]');
        if (!sidebar || !burger) return;

        const setOpen = (open) => {
            sidebar.classList.toggle('is-open', open);
            if (scrim) scrim.classList.toggle('is-visible', open);
            document.body.style.overflow = open ? 'hidden' : '';
        };

        burger.addEventListener('click', () => setOpen(!sidebar.classList.contains('is-open')));

        if (scrim) {
            scrim.addEventListener('click', () => setOpen(false));
        }

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') setOpen(false);
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 991) setOpen(false);
        });
    }

    function initAutoSubmit() {
        document.querySelectorAll('[data-admin-auto-submit]').forEach((control) => {
            control.addEventListener('change', () => {
                const form = control.closest('form');
                if (!form) return;
                form.requestSubmit ? form.requestSubmit() : form.submit();
            });
        });
    }

    function initImagePreview() {
        document.querySelectorAll('[data-upload-input]').forEach((input) => {
            input.addEventListener('change', () => {
                const file = input.files && input.files[0];
                const preview = document.querySelector(input.getAttribute('data-upload-input'));
                if (!file || !preview) return;

                const url = URL.createObjectURL(file);
                const existingImage = preview.querySelector('img');

                if (existingImage) {
                    existingImage.src = url;
                } else {
                    const image = document.createElement('img');
                    image.src = url;
                    image.alt = 'Selected file preview';
                    preview.innerHTML = '';
                    preview.appendChild(image);
                }
            });
        });
    }

    function initBars() {
        document.querySelectorAll('.bar-chart__bar').forEach((bar, index) => {
            bar.style.animationDelay = `${Math.min(index * 55, 400)}ms`;
        });
    }

    function initRevenueTooltips() {
        document.querySelectorAll('[data-metric]').forEach((element) => {
            element.addEventListener('click', () => {
                element.classList.toggle('is-open');
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        initSidebar();
        initAutoSubmit();
        initImagePreview();
        initBars();
        initRevenueTooltips();
    });
})();
