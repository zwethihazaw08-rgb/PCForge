<?php
$aiOptions = [];
try {
    foreach (array_diff_key(component_categories(), ['fans' => true, 'monitor' => true]) as $aiCategory => $aiLabel) {
        $aiRows = db()->query("SELECT id, name FROM `$aiCategory` WHERE status = 'active' ORDER BY name LIMIT 100")->fetchAll();
        $aiOptions[$aiCategory] = ['label' => $aiLabel, 'products' => $aiRows];
    }
} catch (Throwable $exception) {
    error_log('PCForge AI comparison catalog unavailable');
}
$aiHistory = ($_SESSION['ai_chat_owner'] ?? null) === (int) ($_SESSION['user_id'] ?? 0) ? ($_SESSION['ai_chat'] ?? []) : [];
?>
<link rel="stylesheet" href="<?= e(url('assets/css/ai-assistant.css?v=' . filemtime(__DIR__ . '/../assets/css/ai-assistant.css'))) ?>">
<div class="forge-chat" data-ai-assistant>
    <section class="chat-window" id="chatWindow" role="dialog" aria-labelledby="chatTitle" aria-hidden="true" inert>
        <button type="button" class="chat-resize" data-ai-resize aria-label="Resize chat" title="Drag to resize, or use arrow keys when focused"><svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M3 12V3h9M7 12V7h5"/></svg></button>
        <header class="chat-header">
            <div class="agent-info"><div class="agent-avatar" aria-hidden="true">P<span class="online-dot"></span></div><div><h2 id="chatTitle" class="agent-name">PCForge Assistant</h2><div class="agent-status">AI help for your next build</div></div></div>
            <div class="chat-header-actions">
                <button type="button" class="close-chat chat-expand" data-ai-expand aria-label="Expand chat" title="Expand chat" aria-pressed="false"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M8 3H3v5m13-5h5v5M3 16v5h5m13-5v5h-5"/></svg></button>
                <button type="button" class="close-chat" data-ai-close aria-label="Close chat">&times;</button>
            </div>
        </header>
        <div class="chat-toolbar"><span>Using your current build</span><button type="button" data-ai-reset>New chat</button></div>
        <div class="chat-messages" id="chatMessages" role="log" aria-label="Conversation" aria-live="polite" aria-relevant="additions"></div>
        <div class="quick-replies" data-ai-quick>
            <button type="button" class="qr-btn" data-ai-prompt="Is my build balanced for gaming?">Review my build</button>
            <button type="button" class="qr-btn" data-ai-prompt="What should I upgrade first?">Suggest upgrades</button>
            <button type="button" class="qr-btn" data-ai-prompt="Help me plan a PC. What budget and purpose details do you need?">Plan a PC</button>
        </div>
        <form action="<?= e(url('ai-assistant.php')) ?>" method="post" data-ai-form>
            <?= csrf_field() ?>
            <input type="hidden" name="mode" value="chat">
            <details class="chat-options">
                <summary>Budget &amp; comparison options</summary>
                <div class="chat-options-body">
                    <label for="ai-purpose">Purpose</label><select id="ai-purpose" name="purpose"><option value="">Tell me in chat</option><option>gaming</option><option>school</option><option>editing</option><option>general use</option></select>
                    <label for="ai-budget">Budget (<?= e(store_settings()['currency']) ?>)</label><input id="ai-budget" name="budget" type="number" min="0.01" max="99999999.99" step="0.01" placeholder="Optional spending limit">
                    <?php foreach (['a' => 'First component', 'b' => 'Second component'] as $aiKey => $aiText): ?>
                        <label for="ai-compare-<?= $aiKey ?>"><?= $aiText ?></label><select id="ai-compare-<?= $aiKey ?>" name="compare_<?= $aiKey ?>"><option value="">Choose component (optional)</option><?php foreach ($aiOptions as $aiCategory => $aiGroup): ?><optgroup label="<?= e($aiGroup['label']) ?>"><?php foreach ($aiGroup['products'] as $aiProduct): ?><option value="<?= e($aiCategory . ':' . $aiProduct['id']) ?>"><?= e($aiProduct['name']) ?></option><?php endforeach; ?></optgroup><?php endforeach; ?></select>
                    <?php endforeach; ?>
                </div>
            </details>
            <div class="chat-input-row">
                <label class="visually-hidden" for="chatInput">Message PCForge Assistant</label>
                <textarea class="chat-input" id="chatInput" name="question" rows="1" maxlength="1000" required placeholder="Ask about your PC…"></textarea>
                <button type="submit" class="send-btn" aria-label="Send message"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m22 2-7 20-4-9-9-4 20-7ZM22 2 11 13"/></svg></button>
            </div>
        </form>
        <div class="chat-status" data-ai-status role="status"></div>
        <p class="chat-notice">AI can make mistakes. Compatibility checks take priority. Messages and part details are sent to Groq.</p>
    </section>
    <button type="button" class="chat-trigger" id="chatTrigger" aria-label="Open PCForge Assistant" aria-controls="chatWindow" aria-expanded="false">
        <svg class="open-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        <span class="close-icon" aria-hidden="true">&times;</span><span class="unread-badge" data-ai-unread hidden>1</span>
    </button>
    <script type="application/json" data-ai-history><?= json_encode($aiHistory, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE) ?></script>
    <noscript>Enable JavaScript to chat with PCForge Assistant.</noscript>
</div>
