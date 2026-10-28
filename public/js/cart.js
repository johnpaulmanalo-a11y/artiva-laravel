/* ==========================================================================
   Artiva — cart page behaviour
   ========================================================================== */

(function () {
    'use strict';

    function initRemoveButtons() {
        document.querySelectorAll('[data-cart-remove]').forEach((button) => {
            button.addEventListener('click', () => {
                const form = button.closest('form');
                if (!form) return;

                const title = button.getAttribute('data-artwork-title') || 'this artwork';
                if (!window.confirm(`Remove ${title} from your cart?`)) return;

                form.submit();
            });
        });
    }

    function initClearCart() {
        const button = document.querySelector('[data-cart-clear]');
        if (!button) return;

        button.addEventListener('click', () => {
            if (!window.confirm('Remove every artwork from your cart?')) return;
            const form = button.closest('form');
            if (form) form.submit();
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        initRemoveButtons();
        initClearCart();
    });
})();
