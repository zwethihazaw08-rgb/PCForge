(() => {
    document.querySelectorAll('[data-build-carousel]').forEach(section => {
        const track = section.querySelector('.home-builds-track');
        if (!track) return;
        const controls = section.querySelector('.home-builds-controls');
        const previous = section.querySelector('[data-build-prev]');
        const next = section.querySelector('[data-build-next]');
        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        const maxScroll = () => Math.max(0, track.scrollWidth - track.clientWidth);
        const update = () => {
            controls.hidden = maxScroll() < 2;
            previous.disabled = track.scrollLeft < 2;
            next.disabled = track.scrollLeft >= maxScroll() - 2;
        };
        const move = direction => {
            const card = track.querySelector('.gaming-deal-card');
            const gap = parseFloat(getComputedStyle(track).columnGap) || 0;
            track.scrollBy({ left: direction * (card.getBoundingClientRect().width + gap), behavior: reducedMotion.matches ? 'instant' : 'smooth' });
        };
        previous.addEventListener('click', () => move(-1));
        next.addEventListener('click', () => move(1));
        track.addEventListener('scroll', update, { passive: true });
        track.addEventListener('keydown', event => {
            if (event.target !== track) return;
            if (!['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
            event.preventDefault();
            if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
                move(event.key === 'ArrowLeft' ? -1 : 1);
            } else {
                track.scrollTo({ left: event.key === 'Home' ? 0 : maxScroll(), behavior: reducedMotion.matches ? 'instant' : 'smooth' });
            }
        });
        if ('ResizeObserver' in window) new ResizeObserver(update).observe(track);
        else window.addEventListener('resize', update);
        update();
    });
})();
