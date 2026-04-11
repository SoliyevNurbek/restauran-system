const header = document.querySelector('[data-site-header]');
const mobileToggle = document.querySelector('[data-mobile-toggle]');
const mobileNav = document.querySelector('[data-mobile-nav]');

const syncHeaderState = () => {
    if (!header) return;
    header.classList.toggle('is-scrolled', window.scrollY > 12);
};

syncHeaderState();
window.addEventListener('scroll', syncHeaderState, { passive: true });

if (mobileToggle && mobileNav) {
    mobileToggle.addEventListener('click', () => {
        const expanded = mobileToggle.getAttribute('aria-expanded') === 'true';
        mobileToggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
        mobileToggle.classList.toggle('is-active', !expanded);
        mobileNav.classList.toggle('is-open', !expanded);
    });

    mobileNav.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            mobileToggle.setAttribute('aria-expanded', 'false');
            mobileToggle.classList.remove('is-active');
            mobileNav.classList.remove('is-open');
        });
    });
}

document.querySelectorAll('[data-faq-trigger]').forEach((trigger) => {
    trigger.addEventListener('click', () => {
        const item = trigger.closest('[data-faq-item]');
        const panel = item?.querySelector('[data-faq-panel]');
        const expanded = trigger.getAttribute('aria-expanded') === 'true';

        if (!panel) return;

        trigger.setAttribute('aria-expanded', expanded ? 'false' : 'true');
        panel.hidden = expanded;
    });
});
