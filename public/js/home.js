/* ==========================================================================
   Artiva — home page behaviour
   ========================================================================== */

(function () {
    'use strict';

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function initHeroParallax() {
        const visual = document.querySelector('.hero__visual');
        if (!visual || reduceMotion || window.innerWidth < 992) return;

        const main = visual.querySelector('.hero__frame--main');
        const sub = visual.querySelector('.hero__frame--sub');
        if (!main || !sub) return;

        visual.addEventListener('mousemove', (event) => {
            const rect = visual.getBoundingClientRect();
            const x = (event.clientX - rect.left) / rect.width - 0.5;
            const y = (event.clientY - rect.top) / rect.height - 0.5;

            main.style.transform = `translate3d(${x * -14}px, ${y * -14}px, 0) rotate(1.4deg)`;
            sub.style.transform = `translate3d(${x * 10}px, ${y * 10}px, 0)`;
        });

        visual.addEventListener('mouseleave', () => {
            main.style.transform = '';
            sub.style.transform = '';
        });
    }

    function initStaggeredGrids() {
        document.querySelectorAll('[data-stagger]').forEach((grid) => {
            Array.from(grid.children).forEach((child, index) => {
                if (child.hasAttribute('data-reveal')) {
                    child.style.setProperty('--reveal-delay', `${Math.min(index * 70, 420)}ms`);
                }
            });
        });
    }

    function initStatCounters() {
        const stats = document.querySelectorAll('[data-count-to]');
        if (!stats.length || reduceMotion || !('IntersectionObserver' in window)) return;

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;

                const element = entry.target;
                const target = Number(element.getAttribute('data-count-to')) || 0;
                const suffix = element.getAttribute('data-count-suffix') || '';
                const duration = 900;
                const start = performance.now();

                const tick = (now) => {
                    const progress = Math.min((now - start) / duration, 1);
                    const value = Math.round(target * (1 - Math.pow(1 - progress, 3)));
                    element.textContent = value + suffix;
                    if (progress < 1) requestAnimationFrame(tick);
                };

                requestAnimationFrame(tick);
                observer.unobserve(element);
            });
        }, { threshold: 0.5 });

        stats.forEach((stat) => observer.observe(stat));
    }

    document.addEventListener('DOMContentLoaded', () => {
        initHeroParallax();
        initStaggeredGrids();
        initStatCounters();
    });
})();
