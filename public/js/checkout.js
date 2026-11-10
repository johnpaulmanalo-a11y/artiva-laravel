/* ==========================================================================
   Artiva — checkout behaviour
   ========================================================================== */

(function () {
    'use strict';

    function initPaymentMethods() {
        const options = Array.from(document.querySelectorAll('[data-payment-option]'));
        const gcashFields = document.querySelector('[data-gcash-fields]');
        const cardFields = document.querySelector('[data-card-fields]');
        const paypalFields = document.querySelector('[data-paypal-fields]');
        if (!options.length) return;

        const setPanel = (panel, visible) => {
            if (!panel) return;
            panel.classList.toggle('is-visible', visible);
            panel.setAttribute('aria-hidden', String(!visible));
            panel.inert = !visible;
        };

        const sync = () => {
            const selected = options.find((option) => option.querySelector('input').checked);
            const method = selected ? selected.querySelector('input').value : null;

            options.forEach((option) => {
                option.classList.toggle('is-selected', option === selected);
            });

            setPanel(gcashFields, method === 'gcash');
            setPanel(cardFields, method === 'card');
            setPanel(paypalFields, method === 'paypal');
        };

        options.forEach((option) => {
            const input = option.querySelector('input');
            if (input) input.addEventListener('change', sync);
            option.addEventListener('click', () => {
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
        const cardholder = document.querySelector('[data-cardholder-name]');
        const previewName = document.querySelector('[data-card-preview-name]');

        if (cardholder && previewName) {
            cardholder.addEventListener('input', () => {
                previewName.textContent = cardholder.value.trim().toUpperCase() || 'CARDHOLDER NAME';
            });
        }

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

        form.addEventListener('submit', (event) => {
            const selectedMethod = form.querySelector('input[name="payment_method"]:checked');

            // Card fields are display-only until a PCI-compliant gateway is integrated.
            if (selectedMethod && selectedMethod.value === 'card') {
                event.preventDefault();
                window.alert('Card payment is not available yet. Please choose GCash, or wait until a secure card payment provider is connected.');
                return;
            }

            if (selectedMethod && selectedMethod.value === 'paypal') {
                event.preventDefault();
                window.alert('PayPal payment is not available yet. Please choose GCash.');
                return;
            }

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
