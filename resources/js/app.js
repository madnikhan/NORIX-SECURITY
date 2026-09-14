const prefersReducedMotion = () =>
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function initMotionFlag() {
    if (!prefersReducedMotion()) {
        document.documentElement.classList.add('motion-ok');
    }
}

function initReveal() {
    const nodes = document.querySelectorAll('.reveal');
    if (!nodes.length) {
        return;
    }

    if (prefersReducedMotion() || !('IntersectionObserver' in window)) {
        nodes.forEach((node) => node.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { rootMargin: '0px 0px -8% 0px', threshold: 0.12 },
    );

    nodes.forEach((node) => observer.observe(node));
}

function initCityTickers() {
    document.querySelectorAll('[data-city-ticker]').forEach((track) => {
        const items = Array.from(track.querySelectorAll('.hud-ticker__item'));
        if (!items.length) {
            return;
        }

        let index = 0;
        items[0].classList.add('is-active');

        if (prefersReducedMotion() || items.length === 1) {
            return;
        }

        window.setInterval(() => {
            items[index].classList.remove('is-active');
            index = (index + 1) % items.length;
            items[index].classList.add('is-active');
        }, 2400);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initMotionFlag();
    initReveal();
    initCityTickers();
});
