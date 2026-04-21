const header = document.querySelector('[data-site-header]');
const mobileToggle = document.querySelector('[data-mobile-toggle]');
const mobileNav = document.querySelector('[data-mobile-nav]');
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const registerLinks = document.querySelectorAll('[data-register-link]');
const registrationContext = {
    source: 'landing',
    entry_point: 'landing',
    selected_plan: 'Pro',
    recommended_plan: 'Pro',
    halls_count: '2',
    monthly_leads: '70',
    selected_role: 'owner',
    selected_scale: 'growth',
    selected_timing: 'now',
};

const syncRegisterLinks = () => {
    registerLinks.forEach((link) => {
        const href = link.getAttribute('href');

        if (!href || href.startsWith('#')) return;

        const url = new URL(href, window.location.origin);
        const linkContext = {
            ...registrationContext,
            entry_point: link.dataset.entryPoint || registrationContext.entry_point,
        };

        Object.entries(linkContext).forEach(([key, value]) => {
            if (!value) return;
            url.searchParams.set(key, value);
        });

        link.href = url.toString();
    });
};

const syncHeader = () => {
    if (!header) return;
    header.classList.toggle('is-scrolled', window.scrollY > 10);
};

syncHeader();
syncRegisterLinks();
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

document.querySelectorAll('[data-pricing-calculator]').forEach((calculator) => {
    const plans = JSON.parse(calculator.dataset.plans || '[]');
    const hallsInput = calculator.querySelector('[data-calc-halls]');
    const leadsInput = calculator.querySelector('[data-calc-leads]');
    const planSelect = calculator.querySelector('[data-calc-plan]');
    const hallsOutput = calculator.querySelector('[data-calc-halls-output]');
    const leadsOutput = calculator.querySelector('[data-calc-leads-output]');
    const recommendation = calculator.querySelector('[data-calc-recommendation]');
    const priceOutput = calculator.querySelector('[data-calc-price]');
    const yearlyOutput = calculator.querySelector('[data-calc-yearly]');
    const recoveredOutput = calculator.querySelector('[data-calc-recovered]');
    const noteOutput = calculator.querySelector('[data-calc-note]');

    if (!hallsInput || !leadsInput || !planSelect) return;

    const formatUzs = (value) => `${new Intl.NumberFormat('en-US').format(value).replace(/,/g, ' ')} UZS`;
    const getPlan = (name) => plans.find((plan) => plan.name === name) || plans[0];

    const syncCalculator = () => {
        const halls = Number(hallsInput.value);
        const leads = Number(leadsInput.value);
        let recommendedName = 'Basic';

        if (halls >= 5 || leads >= 120) {
            recommendedName = 'Premium';
        } else if (halls >= 3 || leads >= 60) {
            recommendedName = 'Pro';
        }

        const selectedPlan = getPlan(planSelect.value);
        const recommendedPlan = getPlan(recommendedName);
        const recovered = Math.round(leads * (recommendedName === 'Premium' ? 0.34 : recommendedName === 'Pro' ? 0.3 : 0.22));

        hallsOutput.textContent = `${halls} ta zal`;
        leadsOutput.textContent = `${leads} ta lead / oy`;
        recommendation.textContent = recommendedPlan.name;
        priceOutput.textContent = formatUzs(selectedPlan.amount);
        yearlyOutput.textContent = formatUzs(selectedPlan.amount * 12);
        recoveredOutput.textContent = `${recovered} lead`;
        noteOutput.textContent = `${recommendedPlan.name} tavsiyasi ${halls} ta zal va ${leads} ta oylik lead oqimi uchun eng mos operatsion nazoratni beradi.`;

        registrationContext.selected_plan = selectedPlan.name;
        registrationContext.recommended_plan = recommendedPlan.name;
        registrationContext.halls_count = String(halls);
        registrationContext.monthly_leads = String(leads);
        syncRegisterLinks();
    };

    [hallsInput, leadsInput, planSelect].forEach((field) => field.addEventListener('input', syncCalculator));
    syncCalculator();
});

document.querySelectorAll('[data-demo-funnel]').forEach((funnel) => {
    const title = funnel.querySelector('[data-funnel-title]');
    const text = funnel.querySelector('[data-funnel-text]');
    const rolePill = funnel.querySelector('[data-funnel-role-pill]');
    const scalePill = funnel.querySelector('[data-funnel-scale-pill]');
    const timingPill = funnel.querySelector('[data-funnel-timing-pill]');
    const primary = funnel.querySelector('[data-funnel-primary]');
    const secondary = funnel.querySelector('[data-funnel-secondary]');
    const state = { 0: 'owner', 1: 'growth', 2: 'now' };

    const syncFunnel = () => {
        const roleMap = {
            owner: ['Owner flow', 'Rahbar demo tavsiya qilinadi', "Daromad, bandlik va boshqaruv nazorati markazga olinadi."],
            admin: ['Admin flow', 'Operatsion demo tavsiya qilinadi', "Kalendar, bron va mijoz bilan ishlash oqimi tezlashtiriladi."],
            manager: ['Manager flow', 'Sales demo tavsiya qilinadi', "Lead pipeline va follow-up jarayoni asosiy fokusga olinadi."],
        };
        const scaleMap = {
            compact: 'Compact setup',
            growth: 'Growth setup',
            scale: 'Scale setup',
        };
        const timingMap = {
            now: 'Shu hafta',
            month: 'Shu oy',
            later: 'Rejalashtirilgan',
        };

        const [roleLabel, roleTitle, roleText] = roleMap[state[0]] || roleMap.owner;
        title.textContent = roleTitle;
        text.textContent = `${roleText} ${scaleMap[state[1]] || 'Growth setup'} va ${timingMap[state[2]] || 'Shu hafta'} uchun tez yo'l tayyor.`;
        rolePill.textContent = roleLabel;
        scalePill.textContent = scaleMap[state[1]] || 'Growth setup';
        timingPill.textContent = timingMap[state[2]] || 'Shu hafta';
        primary.textContent = state[2] === 'later' ? "Konsultatsiya olish" : "Demo bron qilish";
        secondary.textContent = state[2] === 'later' ? "Registratsiyani ko'rish" : 'Registratsiya';

        registrationContext.selected_role = state[0];
        registrationContext.selected_scale = state[1];
        registrationContext.selected_timing = state[2];
        syncRegisterLinks();
    };

    funnel.querySelectorAll('[data-funnel-choice]').forEach((choice) => {
        choice.addEventListener('click', () => {
            const step = Number(choice.dataset.step);
            state[step] = choice.dataset.value;

            funnel.querySelectorAll(`[data-step="${step}"]`).forEach((item) => {
                item.classList.toggle('is-selected', item === choice);
            });

            funnel.querySelectorAll('[data-funnel-step]').forEach((stepCard, index) => {
                stepCard.classList.toggle('is-active', index === step);
            });

            syncFunnel();
        });
    });

    syncFunnel();
});
