/* ==========================================================================
   Artiva — shared storefront behaviour
   ========================================================================== */

(function () {
    'use strict';

    const ready = (fn) => {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    };

    /* ---------- Mobile navigation ---------- */

    function initMobileNav() {
        const toggle = document.querySelector('[data-nav-toggle]');
        const nav = document.getElementById('siteNav');
        if (!toggle || !nav) return;

        const close = () => {
            nav.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
        };

        toggle.addEventListener('click', () => {
            const open = nav.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });

        nav.querySelectorAll('a').forEach((link) => link.addEventListener('click', close));

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') close();
        });

        document.addEventListener('click', (event) => {
            if (!nav.classList.contains('is-open')) return;
            if (nav.contains(event.target) || toggle.contains(event.target)) return;
            close();
        });
    }

    /* ---------- Flash messages ---------- */

    function initFlashMessages() {
        document.querySelectorAll('[data-dismiss-alert]').forEach((button) => {
            button.addEventListener('click', () => {
                const alert = button.closest('[data-flash]');
                if (alert) dismiss(alert);
            });
        });

        document.querySelectorAll('[data-flash]').forEach((alert) => {
            window.setTimeout(() => dismiss(alert), 8000);
        });
    }

    function dismiss(alert) {
        alert.classList.add('is-flashing-out');
        window.setTimeout(() => alert.remove(), 300);
    }

    /* ---------- Confirmation prompts ---------- */

    function initConfirmations() {
        document.querySelectorAll('[data-confirm]').forEach((element) => {
            element.addEventListener('click', (event) => {
                const message = element.getAttribute('data-confirm') || 'Are you sure?';
                if (!window.confirm(message)) {
                    event.preventDefault();
                    event.stopPropagation();
                }
            });
        });
    }

    /* ---------- Scroll reveal ---------- */

    function initReveal() {
        const items = document.querySelectorAll('[data-reveal]');
        if (!items.length) return;

        if (!('IntersectionObserver' in window)) {
            items.forEach((item) => item.classList.add('is-revealed'));
            return;
        }

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-revealed');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
        );

        items.forEach((item) => observer.observe(item));
    }

    /* ---------- Sticky header shadow ---------- */

    function initHeaderScroll() {
        const header = document.querySelector('[data-site-header]');
        if (!header) return;

        const update = () => {
            header.style.boxShadow = window.scrollY > 8
                ? '0 10px 30px -24px rgba(16, 38, 77, 0.6)'
                : 'none';
        };

        update();
        window.addEventListener('scroll', update, { passive: true });
    }

    /* ---------- Submit feedback ---------- */

    function initSubmitFeedback() {
        document.querySelectorAll('form[data-loading]').forEach((form) => {
            form.addEventListener('submit', () => {
                form.querySelectorAll('button[type="submit"]').forEach((button) => {
                    button.classList.add('is-loading');
                    button.setAttribute('aria-busy', 'true');
                });
            });
        });
    }

    /* ---------- Password visibility ---------- */

    function initPasswordToggles() {
        document.querySelectorAll('[data-toggle-password]').forEach((button) => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.getAttribute('data-toggle-password'));
                if (!input) return;

                const show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                button.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                button.innerHTML = show ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
            });
        });
    }

    ready(() => {
        initMobileNav();
        initFlashMessages();
        initConfirmations();
        initReveal();
        initHeaderScroll();
        initSubmitFeedback();
        initPasswordToggles();
    });
})();
