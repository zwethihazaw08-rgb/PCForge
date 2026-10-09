<?php $receiptQrUrl = $receiptQrUrl ?? $receiptUrl; ?>
<section class="receipt-qr-panel" aria-label="Receipt QR code" data-receipt-qr data-receipt-url="<?= e($receiptQrUrl) ?>">
    <div class="receipt-qr-image" data-qr-image></div>
    <h2 class="h5 mt-3">Your receipt, ready to scan</h2>
    <p class="small text-secondary mb-1">Scan to open this receipt on your phone. Sign in with the account used for this order.</p>
    <p class="small text-secondary mb-0" data-qr-status role="status">Preparing your receipt QR code…</p>
    <noscript><p class="small">Enable JavaScript to display the QR code, or use the receipt link.</p></noscript>
</section>
