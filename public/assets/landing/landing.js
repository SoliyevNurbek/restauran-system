const header = document.querySelector('[data-site-header]');
const mobileToggle = document.querySelector('[data-mobile-toggle]');
const mobileNav = document.querySelector('[data-mobile-nav]');
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const syncHeader = () => {
    if (!header) return;
    header.classList.toggle('is-scrolled', window.scrollY > 10);
};

syncHeader();
window.addEventListener('scroll', syncHeader, { passive: true });

if (mobileToggle && mobileNav) {
    mobileToggle.addEventListener('click', () => {
        const isOpen = mobileToggle.getAttribute('aria-expanded') === 'true';
        mobileToggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        mobileToggle.classList.toggle('is-active', !isOpen);
        mobileNav.classList.toggle('is-open', !isOpen);
    });

    mobileNav.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            mobileToggle.setAttribute('aria-expanded', 'false');
            mobileToggle.classList.remove('is-active');
            mobileNav.classList.remove('is-open');
        });
    });
}

const revealItems = document.querySelectorAll('[data-reveal]');

if (prefersReducedMotion) {
    revealItems.forEach((item) => item.classList.add('is-visible'));
} else {
    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.16 });

    revealItems.forEach((item) => revealObserver.observe(item));
}

if (!prefersReducedMotion) {
    // 3D effect: soft parallax shifts layered hero elements by depth value.
    document.querySelectorAll('[data-parallax-scene]').forEach((scene) => {
        const layers = scene.querySelectorAll('[data-depth]');

        if (!layers.length) return;

        scene.addEventListener('mousemove', (event) => {
            const rect = scene.getBoundingClientRect();
            const offsetX = (event.clientX - rect.left) / rect.width - 0.5;
            const offsetY = (event.clientY - rect.top) / rect.height - 0.5;

            layers.forEach((layer) => {
                const depth = Number(layer.getAttribute('data-depth')) || 0;
                const moveX = offsetX * depth;
                const moveY = offsetY * depth;
                layer.style.transform = `translate3d(${moveX}px, ${moveY}px, 0) rotate(${depth > 0 ? 1 : -1}deg)`;
            });
        });

        scene.addEventListener('mouseleave', () => {
            layers.forEach((layer) => {
                layer.style.transform = '';
            });
        });
    });

    // 3D effect: subtle tilt keeps dashboard/product cards tactile without toy-like motion.
    document.querySelectorAll('[data-tilt]').forEach((card) => {
        const limit = 10;

        card.addEventListener('mousemove', (event) => {
            const rect = card.getBoundingClientRect();
            const px = (event.clientX - rect.left) / rect.width - 0.5;
            const py = (event.clientY - rect.top) / rect.height - 0.5;
            const rotateY = px * limit;
            const rotateX = py * -limit;

            card.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-2px)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
        });
    });
}
