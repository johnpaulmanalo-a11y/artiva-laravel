/* ==========================================================================
   Artiva — checkout behaviour
   ========================================================================== */

(function () {
    'use strict';

    function initPaymentMethods() {
        const options = Array.from(document.querySelectorAll('[data-payment-option]'));
        const fields = document.querySelector('[data-card-fields]');
        if (!options.length) return;

        const sync = () => {
            const selected = options.find((option) => option.querySelector('input').checked);
            const method = selected ? selected.querySelector('input').value : null;

            options.forEach((option) => {
                option.classList.toggle('is-selected', option === selected);
            });

            if (fields) {
                fields.classList.toggle('is-visible', method === 'card');
            }

            if (method !== 'card') {
                const cardNumber = document.getElementById('card_number');
                const cardExpiry = document.getElementById('card_expiry');
                const cardCvv = document.getElementById('card_cvv');
                [cardNumber, cardExpiry, cardCvv].forEach((input) => {
                    if (!input) return;
                    input.value = '';
                    input.setCustomValidity('');
                });
            }
        };

        options.forEach((option) => {
            option.addEventListener('click', () => {
                const input = option.querySelector('input');
                if (input) input.checked = true;
                sync();
            });
        });

        sync();
    }

    function initCardFormatting() {
        const number = document.getElementById('card_number');
        const expiry = document.getElementById('card_expiry');
        const cvv = document.getElementById('card_cvv');
        const previewNumber = document.querySelector('[data-card-preview-number]');
        const previewExpiry = document.querySelector('[data-card-preview-expiry]');

        if (number) {
            number.addEventListener('input', () => {
                const digits = number.value.replace(/\D/g, '').slice(0, 19);
                number.value = digits.replace(/(.{4})/g, '$1 ').trim();

                if (previewNumber) {
                    const masked = digits
                        ? '•••• •••• •••• ' + digits.slice(-4).padStart(4, '•')
                        : '•••• •••• •••• ••••';
                    previewNumber.textContent = masked;
                }
            });
        }

        if (expiry) {
            expiry.addEventListener('input', () => {
                let digits = expiry.value.replace(/\D/g, '').slice(0, 4);
                if (digits.length >= 3) {
                    digits = digits.slice(0, 2) + '/' + digits.slice(2);
                }
                expiry.value = digits;

                if (previewExpiry) {
                    previewExpiry.textContent = digits || 'MM/YY';
                }
            });
        }

        if (cvv) {
            cvv.addEventListener('input', () => {
                cvv.value = cvv.value.replace(/\D/g, '').slice(0, 4);
            });
        }
    }

    function initSubmitGuard() {
        const form = document.querySelector('[data-checkout-form]');
        if (!form) return;

        form.addEventListener('submit', () => {
            const button = form.querySelector('button[type="submit"]');
            if (button) {
                button.classList.add('is-loading');
                button.setAttribute('aria-busy', 'true');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        initPaymentMethods();
        initCardFormatting();
        initSubmitGuard();
    });
})();
