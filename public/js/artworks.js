/* ==========================================================================
   Artiva — explore / filtering behaviour
   ========================================================================== */

(function () {
    'use strict';

    function submitFilters(form) {
        if (!form) return;
        form.submit();
    }

    function initSortSelects() {
        document.querySelectorAll('[data-auto-submit]').forEach((control) => {
            control.addEventListener('change', () => {
                const form = control.closest('form');
                if (form) form.requestSubmit ? form.requestSubmit() : submitFilters(form);
            });
        });
    }

    function initMobileFilters() {
        const toggle = document.querySelector('[data-filters-toggle]');
        const panel = document.querySelector('[data-filters-panel]');
        if (!toggle || !panel) return;

        toggle.addEventListener('click', () => {
            const open = panel.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    function initPriceValidation() {
        const min = document.getElementById('min_price');
        const max = document.getElementById('max_price');
        if (!min || !max) return;

        const validate = () => {
            const minValue = parseFloat(min.value);
            const maxValue = parseFloat(max.value);

            if (!Number.isNaN(minValue) && !Number.isNaN(maxValue) && minValue > maxValue) {
                max.setCustomValidity('The maximum price must be greater than the minimum price.');
            } else {
                max.setCustomValidity('');
            }
        };

        min.addEventListener('input', validate);
        max.addEventListener('input', validate);
    }

    function initCategoryNav() {
        // Selecting a category radio updates the visible label before submit.
        document.querySelectorAll('[data-category-option]').forEach((input) => {
            input.addEventListener('change', () => {
                const form = input.closest('form');
                if (form && window.innerWidth < 992) form.requestSubmit();
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        initSortSelects();
        initMobileFilters();
        initPriceValidation();
        initCategoryNav();
    });
})();
