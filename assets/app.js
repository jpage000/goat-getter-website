/* ═══════════════════════════════════════════════════════════════════
   Goat Getter — Landing Page Interactions
   ═══════════════════════════════════════════════════════════════════ */

(function () {
    'use strict';

    // ── Navbar scroll effect ────────────────────────────────────────
    const nav = document.getElementById('gg-nav');
    if (nav) {
        const onScroll = () => {
            nav.classList.toggle('scrolled', window.scrollY > 40);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    // ── Mobile hamburger ────────────────────────────────────────────
    const hamburger = document.getElementById('gg-hamburger');
    const drawer = document.getElementById('gg-drawer');
    if (hamburger && drawer) {
        hamburger.addEventListener('click', () => {
            drawer.classList.toggle('open');
            hamburger.classList.toggle('active');
        });

        // Close drawer on link click
        drawer.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                drawer.classList.remove('open');
                hamburger.classList.remove('active');
            });
        });
    }

    // ── Smooth scroll for anchor links ──────────────────────────────
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (!target) return;
            e.preventDefault();
            const offset = nav ? nav.offsetHeight + 10 : 80;
            const top = target.getBoundingClientRect().top + window.pageYOffset - offset;
            window.scrollTo({ top, behavior: 'smooth' });
        });
    });

    // ── Scroll-triggered fade-in ────────────────────────────────────
    const observerOpts = { threshold: 0.12, rootMargin: '0px 0px -40px 0px' };
    const fadeObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('gg-visible');
                fadeObserver.unobserve(entry.target);
            }
        });
    }, observerOpts);

    // Observe all cards and sections
    document.querySelectorAll(
        '.gg-plugin-card, .gg-feature-card, .gg-coming-card, .gg-section-head, .gg-cta-inner'
    ).forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(24px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        fadeObserver.observe(el);
    });

    // Add the visible class styles
    const style = document.createElement('style');
    style.textContent = '.gg-visible { opacity: 1 !important; transform: translateY(0) !important; }';
    document.head.appendChild(style);

})();
