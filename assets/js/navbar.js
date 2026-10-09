(() => {
    const navbar = document.querySelector('.lg-navbar');
    if (!navbar) return;

    const tabs = navbar.querySelector('[data-glass-tabs]');
    const menu = navbar.querySelector('#mainNavbar');
    const menuToggle = navbar.querySelector('.lg-menu-toggle');
    const pill = tabs.querySelector('.lg-pill');
    const items = [...tabs.querySelectorAll('.lg-item')];
    const activeItem = items.find((item) => item.getAttribute('aria-current') === 'page');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const compactLayout = window.matchMedia('(max-width: 1199.98px)');
    const menuStateKey = 'pcforge-navbar-open';
    let animation;
    let touchOrigin;
    let shownItem;

    const readMenuState = () => {
        try { return sessionStorage.getItem(menuStateKey) === 'open'; } catch (error) { return false; }
    };
    const writeMenuState = (open) => {
        try {
            if (open) sessionStorage.setItem(menuStateKey, 'open');
            else sessionStorage.removeItem(menuStateKey);
        } catch (error) { /* Storage may be unavailable. */ }
    };

    // Keep the mobile menu usable when Bootstrap's optional CDN script is
    // unavailable. The button owns this state, so it also avoids a delayed or
    // swallowed touch click on mobile browsers.
    const setMenuOpen = (open, restoreFocus = true) => {
        if (!menu || !menuToggle) return;
        open = open && compactLayout.matches;
        touchOrigin = null;
        writeMenuState(open);
        navbar.classList.toggle('is-menu-open', open);
        menu.classList.toggle('is-open', open);
        menuToggle.setAttribute('aria-expanded', String(open));
        menu.inert = compactLayout.matches && !open;

        if (!open) {
            menu.querySelectorAll('[data-bs-toggle="dropdown"]').forEach((toggle) => {
                window.bootstrap?.Dropdown.getInstance(toggle)?.hide();
            });
            if (restoreFocus && compactLayout.matches && menu.contains(document.activeElement)) {
                menuToggle.focus({ preventScroll: true });
            }
        }

    };
    menuToggle?.addEventListener('click', (event) => {
        event.preventDefault();
        setMenuOpen(!menu.classList.contains('is-open'));
    });
    const dismissMenu = () => {
        if (compactLayout.matches && menu.classList.contains('is-open')) setMenuOpen(false);
    };
    document.addEventListener('click', (event) => {
        if (!navbar.contains(event.target)) dismissMenu();
    });
    // Dismiss on new scroll input, not scroll events: mobile browser chrome,
    // focus, and residual momentum can move the page after the menu opens.
    window.addEventListener('wheel', (event) => {
        if (event.deltaY && !event.ctrlKey) dismissMenu();
    }, { passive: true });
    document.addEventListener('touchstart', (event) => {
        touchOrigin = null;
        if (!compactLayout.matches || !menu.classList.contains('is-open') || event.touches.length !== 1) return;
        const touch = event.touches[0];
        touchOrigin = { id: touch.identifier, x: touch.clientX, y: touch.clientY };
    }, { passive: true });
    document.addEventListener('touchmove', (event) => {
        if (!touchOrigin || event.touches.length !== 1) return;
        const touch = event.touches[0];
        if (touch.identifier !== touchOrigin.id) return;
        const distanceX = Math.abs(touch.clientX - touchOrigin.x);
        const distanceY = Math.abs(touch.clientY - touchOrigin.y);
        if (distanceY >= 12 && distanceY > distanceX) dismissMenu();
    }, { passive: true });
    ['touchend', 'touchcancel'].forEach((name) => {
        document.addEventListener(name, () => { touchOrigin = null; }, { passive: true });
    });
    document.addEventListener('pointerdown', (event) => {
        const viewport = document.documentElement;
        if (event.pointerType === 'mouse' && (event.clientX >= viewport.clientWidth || event.clientY >= viewport.clientHeight)) dismissMenu();
    }, { passive: true });
    document.addEventListener('keydown', (event) => {
        if (event.defaultPrevented || !compactLayout.matches || !menu.classList.contains('is-open')) return;
        if (event.key === 'Escape') {
            setMenuOpen(false);
            menuToggle.focus({ preventScroll: true });
        } else if (['ArrowDown', 'ArrowUp', 'PageDown', 'PageUp', 'Home', 'End', ' '].includes(event.key)
            && !event.ctrlKey && !event.metaKey && !event.altKey
            && !event.target.closest('input, textarea, select, [contenteditable]')
            && !(event.key === ' ' && event.target.closest('button, a'))) {
            dismissMenu();
        }
    });
    compactLayout.addEventListener('change', () => setMenuOpen(false));
    // Keep the mobile menu expanded when a navigation link loads another page,
    // matching the desktop navbar. It still closes from explicit dismissal.
    setMenuOpen(compactLayout.matches && readMenuState(), false);

    // FLIP: measure the visible pill before replacing its layout, then animate
    // the inverse transform. Measuring first also handles interrupted motion.
    const movePillTo = (item, animate = true) => {
        if (!item || !tabs.getClientRects().length) {
            animation?.cancel();
            pill.style.opacity = '0';
            shownItem = null;
            return;
        }

        const first = pill.getBoundingClientRect();
        const wasVisible = pill.style.opacity === '1';
        animation?.cancel();
        pill.style.left = `${item.offsetLeft}px`;
        pill.style.top = `${item.offsetTop}px`;
        pill.style.width = `${item.offsetWidth}px`;
        pill.style.height = `${item.offsetHeight}px`;
        pill.style.opacity = '1';
        tabs.dataset.ready = '';
        shownItem = item;

        const last = pill.getBoundingClientRect();
        if (!animate || !wasVisible || reducedMotion.matches || !last.width || !last.height) return;

        animation = pill.animate([
            { transform: `translate(${first.left - last.left}px, ${first.top - last.top}px) scale(${first.width / last.width}, ${first.height / last.height})` },
            { transform: 'translate(0, 0) scale(1, 1)' },
        ], { duration: 450, easing: 'cubic-bezier(.34, 1.56, .64, 1)' });
    };

    const restorePill = () => {
        const focusedItem = items.find((item) => item === document.activeElement);
        movePillTo(focusedItem || activeItem);
    };

    items.forEach((item) => {
        item.addEventListener('pointerenter', (event) => {
            if (event.pointerType === 'mouse' || event.pointerType === 'pen') movePillTo(item);
        });
        item.addEventListener('focus', () => movePillTo(item));
        item.addEventListener('click', (event) => {
            if (event.button === 0 && !event.ctrlKey && !event.metaKey && !event.shiftKey && !event.altKey) movePillTo(item);
        });
    });

    tabs.addEventListener('pointermove', (event) => {
        if (reducedMotion.matches || event.pointerType === 'touch') return;
        const rect = tabs.getBoundingClientRect();
        tabs.style.setProperty('--lg-x', `${((event.clientX - rect.left) / rect.width) * 100}%`);
        tabs.style.setProperty('--lg-y', `${((event.clientY - rect.top) / rect.height) * 100}%`);
    });
    tabs.addEventListener('pointerleave', () => {
        tabs.style.removeProperty('--lg-x');
        tabs.style.removeProperty('--lg-y');
        restorePill();
    });
    tabs.addEventListener('focusout', () => requestAnimationFrame(restorePill));

    // Collapse, viewport changes, and font loading all change the link boxes.
    const snapPill = () => movePillTo(shownItem || activeItem, false);
    new ResizeObserver(snapPill).observe(tabs);
    navbar.querySelector('#mainNavbar').addEventListener('shown.bs.collapse', snapPill);
    window.addEventListener('pageshow', () => movePillTo(activeItem, false));
    document.fonts.ready.then(snapPill);
    reducedMotion.addEventListener('change', () => {
        tabs.style.removeProperty('--lg-x');
        tabs.style.removeProperty('--lg-y');
        snapPill();
    });
    movePillTo(activeItem, false);

    const toggles = [...navbar.querySelectorAll('[data-theme-toggle], [data-theme-toggle-mobile]')];
    const syncToggle = (toggle) => {
        const dark = document.documentElement.dataset.theme === 'dark';
        const label = dark ? 'Switch to light mode' : 'Switch to dark mode';
        toggle.textContent = dark ? '\u2600' : '\u263e';
        toggle.setAttribute('aria-pressed', String(dark));
        toggle.setAttribute('aria-label', label);
        toggle.title = label;
    };
    toggles.forEach((toggle) => {
        syncToggle(toggle);
        toggle.addEventListener('click', () => {
            const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
            document.documentElement.dataset.theme = nextTheme;
            try { localStorage.setItem('pcforge-theme', nextTheme); } catch (error) { /* Storage may be unavailable. */ }
            toggles.forEach(syncToggle);
        });
    });
})();
