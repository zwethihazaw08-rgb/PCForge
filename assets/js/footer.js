(() => {
    const sections = [...document.querySelectorAll('[data-footer-section]')];
    const mobile = window.matchMedia('(max-width: 575.98px)');
    const syncFooterSections = () => sections.forEach(section => { section.open = !mobile.matches; });
    syncFooterSections();
    mobile.addEventListener?.('change', syncFooterSections);
})();
