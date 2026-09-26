'use strict';

const themeToggle = document.getElementById('admin-theme-toggle');
if (themeToggle) {
    themeToggle.hidden = false;
    const updateToggle = () => {
        const dark = document.documentElement.dataset.theme === 'dark';
        themeToggle.textContent = dark ? '\u2600' : '\u263e';
        themeToggle.setAttribute('aria-pressed', String(dark));
        themeToggle.setAttribute('aria-label', dark ? 'Switch to light mode' : 'Switch to dark mode');
        themeToggle.setAttribute('title', dark ? 'Switch to light mode' : 'Switch to dark mode');
    };
    updateToggle();
    themeToggle.addEventListener('click', () => {
        const theme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
        document.documentElement.dataset.theme = theme;
        document.documentElement.dataset.bsTheme = theme;
        try { localStorage.setItem('pcforge-theme', theme); } catch (_) {}
        updateToggle();
    });
}

// The same form still submits normally without JavaScript. Destructive actions
// also have explicit labels and server-side validation.
const confirmation = document.createElement('div');
confirmation.className = 'modal fade';
confirmation.id = 'admin-confirmation';
confirmation.tabIndex = -1;
confirmation.setAttribute('aria-labelledby', 'confirmation-title');
confirmation.innerHTML = '<div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h2 class="modal-title fs-5" id="confirmation-title">Confirm action</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><p id="confirmation-message" class="mb-0"></p></div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-danger" id="confirmation-submit">Confirm</button></div></div></div>';
document.body.appendChild(confirmation);
let pendingForm = null;
document.addEventListener('submit', event => {
    const form = event.target;
    const message = form.dataset.confirm || (form.matches('[data-order-status-form]') && form.elements.status.value === 'cancelled' ? 'Cancel this order? Cancellation is final.' : '');
    if (!message || !window.bootstrap) return;
    event.preventDefault();
    pendingForm = form;
    document.getElementById('confirmation-message').textContent = message;
    bootstrap.Modal.getOrCreateInstance(confirmation).show();
});
document.getElementById('confirmation-submit').addEventListener('click', () => {
    if (pendingForm) HTMLFormElement.prototype.submit.call(pendingForm);
});
const upload = document.getElementById('image');
let previewUrl;
upload?.addEventListener('change', () => {
    const preview = document.getElementById('upload-preview');
    if (previewUrl) URL.revokeObjectURL(previewUrl);
    const file = upload.files[0];
    preview.hidden = !file;
    if (file) { previewUrl = URL.createObjectURL(file); preview.src = previewUrl; }
});
