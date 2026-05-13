// ============================================================
// Al-Ashraq M.V - Main JavaScript
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    // ---- Mobile Menu Toggle ----
    const menuToggle = document.getElementById('menuToggle');
    const mainNav    = document.getElementById('mainNav');

    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', function () {
            mainNav.classList.toggle('open');
            this.classList.toggle('active');
        });
    }

    // ---- Auto-dismiss flash messages ----
    const flashMsg = document.getElementById('flashMsg');
    if (flashMsg) {
        setTimeout(() => {
            flashMsg.style.opacity = '0';
            flashMsg.style.transition = 'opacity 0.5s';
            setTimeout(() => flashMsg.remove(), 500);
        }, 4000);
    }

    // ---- Accordion ----
    document.querySelectorAll('.accordion-header').forEach(function (header) {
        header.addEventListener('click', function () {
            const body = this.nextElementSibling;
            const isOpen = body.classList.contains('open');

            // Close all
            document.querySelectorAll('.accordion-body').forEach(b => b.classList.remove('open'));
            document.querySelectorAll('.accordion-header').forEach(h => h.classList.remove('open'));

            if (!isOpen) {
                body.classList.add('open');
                this.classList.add('open');
            }
        });
    });

    // ---- Tabs ----
    document.querySelectorAll('.tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const target = this.dataset.tab;
            const parent = this.closest('.tabs-wrapper') || document;

            parent.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            parent.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));

            this.classList.add('active');
            const panel = parent.querySelector('#' + target) || document.getElementById(target);
            if (panel) panel.classList.add('active');
        });
    });

    // ---- Password Confirmation Validation ----
    const confirmPass = document.getElementById('confirm_password');
    const password    = document.getElementById('password');

    if (confirmPass && password) {
        function checkPasswords() {
            if (confirmPass.value && password.value !== confirmPass.value) {
                confirmPass.setCustomValidity('Passwords do not match.');
                confirmPass.style.borderColor = '#dc3545';
            } else {
                confirmPass.setCustomValidity('');
                confirmPass.style.borderColor = '';
            }
        }
        password.addEventListener('input', checkPasswords);
        confirmPass.addEventListener('input', checkPasswords);
    }

    // ---- Password Visibility Toggle ----
    document.querySelectorAll('.toggle-password').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    this.innerHTML = '<i class="fas fa-eye-slash"></i>';
                } else {
                    input.type = 'password';
                    this.innerHTML = '<i class="fas fa-eye"></i>';
                }
            }
        });
    });

    // ---- Counter Animation (Stats) ----
    function animateCounter(el) {
        const target = parseInt(el.dataset.target, 10);
        const duration = 1500;
        const step = target / (duration / 16);
        let current = 0;

        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            el.textContent = Math.floor(current) + (el.dataset.suffix || '');
        }, 16);
    }

    // Intersection Observer for counters
    const counters = document.querySelectorAll('[data-target]');
    if (counters.length) {
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(counter => observer.observe(counter));
    }

    // ---- Gallery Lightbox (simple) ----
    const galleryItems = document.querySelectorAll('.gallery-item img');
    if (galleryItems.length) {
        // Create lightbox elements
        const overlay = document.createElement('div');
        overlay.id = 'lightbox';
        overlay.style.cssText = `
            display:none; position:fixed; inset:0; background:rgba(0,0,0,0.9);
            z-index:9999; align-items:center; justify-content:center; cursor:pointer;
        `;
        const img = document.createElement('img');
        img.style.cssText = 'max-width:90vw; max-height:90vh; border-radius:10px; box-shadow:0 20px 60px rgba(0,0,0,0.5);';
        overlay.appendChild(img);
        document.body.appendChild(overlay);

        galleryItems.forEach(function (el) {
            el.style.cursor = 'zoom-in';
            el.addEventListener('click', function () {
                img.src = this.src;
                img.alt = this.alt;
                overlay.style.display = 'flex';
            });
        });

        overlay.addEventListener('click', function () {
            this.style.display = 'none';
        });
    }

    // ---- Confirm Delete ----
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(this.dataset.confirm || 'Are you sure?')) {
                e.preventDefault();
            }
        });
    });

    // ---- Smooth scroll for anchor links ----
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // ---- Active sidebar nav detection ----
    const sidebarLinks = document.querySelectorAll('.sidebar-nav a');
    sidebarLinks.forEach(link => {
        if (link.href === window.location.href) {
            link.classList.add('active');
        }
    });

});