// frontoffice/src/assets/js/main.js
// JavaScript minimal pour le frontoffice

(function() {
    'use strict';

    // ============================================
    // Lazy Loading des images
    // ============================================
    
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src || img.src;
                        img.classList.add('loaded');
                        observer.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }

    // ============================================
    // Smooth scroll pour les ancres
    // ============================================

    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href === '#') return;

                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    }

    // ============================================
    // Analytics minimal
    // ============================================

    function trackPageView() {
        if (typeof window.gtag === 'function') {
            gtag('config', 'GA_ID');
        }
    }

    // ============================================
    // Initialisation
    // ============================================

    document.addEventListener('DOMContentLoaded', () => {
        initLazyLoading();
        initSmoothScroll();
        trackPageView();

        console.log('🚀 IranInfo Frontoffice loaded');
    });

})();
