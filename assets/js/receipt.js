/* Receipt codes are generated locally with Project Nayuki's MIT-licensed library. */
(() => {
    'use strict';

    document.querySelectorAll('[data-print-receipt]').forEach((button) => {
        button.hidden = false;
        button.addEventListener('click', () => window.print());
    });

    document.querySelectorAll('[data-receipt-qr]').forEach((panel) => {
        const status = panel.querySelector('[data-qr-status]');
        try {
            // Use the origin the customer opened, including a LAN host or tunnel.
            const receiptUrl = new URL(panel.dataset.receiptUrl, window.location.href);
            if (!['http:', 'https:'].includes(receiptUrl.protocol) || !receiptUrl.hostname) throw new Error('Invalid receipt URL');
            const qr = qrcodegen.QrCode.encodeText(receiptUrl.href, qrcodegen.QrCode.Ecc.MEDIUM);
            const border = 4; // QR quiet zone, always white in both site themes.
            const size = qr.size + border * 2;
            const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('viewBox', `0 0 ${size} ${size}`);
            svg.setAttribute('width', '232');
            svg.setAttribute('height', '232');
            svg.setAttribute('role', 'img');
            svg.setAttribute('aria-label', 'QR code to open your order receipt');
            svg.setAttribute('shape-rendering', 'crispEdges');
            const background = document.createElementNS(svg.namespaceURI, 'rect');
            background.setAttribute('width', String(size));
            background.setAttribute('height', String(size));
            background.setAttribute('fill', '#fff');
            svg.append(background);
            const pixels = [];
            for (let y = 0; y < qr.size; y++) {
                for (let x = 0; x < qr.size; x++) {
                    if (qr.getModule(x, y)) pixels.push(`M${x + border},${y + border}h1v1h-1z`);
                }
            }
            const path = document.createElementNS(svg.namespaceURI, 'path');
            path.setAttribute('d', pixels.join(' '));
            path.setAttribute('fill', '#000');
            svg.append(path);
            panel.querySelector('[data-qr-image]').replaceChildren(svg);
            const localHost = ['localhost', '127.0.0.1', '[::1]'].includes(receiptUrl.hostname);
            status.textContent = localHost
                ? 'For phone scanning, open PCForge using your computer’s network address or shared site URL, then display this code again.'
                : 'Your phone must be able to reach this site. This QR opens a receipt; it does not make a payment.';
        } catch (error) {
            status.textContent = 'The QR code could not be displayed. You can still open and print your receipt using the receipt link.';
        }
    });
})();
