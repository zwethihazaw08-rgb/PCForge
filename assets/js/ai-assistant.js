(() => {
    'use strict';
    const widget = document.querySelector('[data-ai-assistant]');
    if (!widget) return;
    const window_ = widget.querySelector('.chat-window');
    const trigger = widget.querySelector('.chat-trigger');
    const messages = widget.querySelector('.chat-messages');
    const form = widget.querySelector('[data-ai-form]');
    const input = form.elements.question;
    const status = widget.querySelector('[data-ai-status]');
    const badge = widget.querySelector('[data-ai-unread]');
    const quick = widget.querySelector('[data-ai-quick]');
    const reset = widget.querySelector('[data-ai-reset]');
    let busy = false;
    let open = false;
    let retryAt = 0;
    let cooldownTimer;
    const resizeHandle = widget.querySelector('[data-ai-resize]');
    const expandButton = widget.querySelector('[data-ai-expand]');
    let expanded = false;
    let previousSize = null;
    let drag = null;
    const sizeLimits = () => {
        const mobile = window.matchMedia('(max-width: 480px)').matches;
        return { width: Math.max(1, window.innerWidth - (mobile ? 24 : 48)), height: Math.max(1, window.innerHeight - (mobile ? 90 : 112)) };
    };
    const setSize = (width, height) => {
        const max = sizeLimits();
        window_.style.setProperty('--chat-width', Math.min(max.width, Math.max(300, width)) + 'px');
        window_.style.setProperty('--chat-height', Math.min(max.height, Math.max(400, height)) + 'px');
    };
    const setExpanded = value => {
        expanded = value;
        expandButton.setAttribute('aria-pressed', String(value));
        expandButton.setAttribute('aria-label', value ? 'Restore chat size' : 'Expand chat');
        expandButton.title = value ? 'Restore chat size' : 'Expand chat';
    };
    expandButton.addEventListener('click', () => {
        if (!expanded) {
            previousSize = { width: window_.style.getPropertyValue('--chat-width'), height: window_.style.getPropertyValue('--chat-height') };
            setSize(760, sizeLimits().height);
            setExpanded(true);
        } else {
            for (const dimension of ['width', 'height']) {
                if (previousSize?.[dimension]) window_.style.setProperty('--chat-' + dimension, previousSize[dimension]);
                else window_.style.removeProperty('--chat-' + dimension);
            }
            setExpanded(false);
        }
    });
    // The bottom-right corner stays anchored; drag the top-left corner outward.
    resizeHandle.addEventListener('pointerdown', event => {
        if (event.button !== 0 || !event.isPrimary) return;
        event.preventDefault();
        resizeHandle.focus({ preventScroll: true });
        drag = { id: event.pointerId, x: event.clientX, y: event.clientY, width: window_.offsetWidth, height: window_.offsetHeight };
        resizeHandle.setPointerCapture(event.pointerId);
        window_.classList.add('is-resizing');
        setExpanded(false);
    });
    resizeHandle.addEventListener('pointermove', event => {
        if (!drag || event.pointerId !== drag.id) return;
        setSize(drag.width + drag.x - event.clientX, drag.height + drag.y - event.clientY);
    });
    const endResize = event => {
        if (!drag || event.pointerId !== drag.id) return;
        drag = null;
        window_.classList.remove('is-resizing');
        if (resizeHandle.hasPointerCapture(event.pointerId)) resizeHandle.releasePointerCapture(event.pointerId);
    };
    ['pointerup', 'pointercancel', 'lostpointercapture'].forEach(name => resizeHandle.addEventListener(name, endResize));
    resizeHandle.addEventListener('keydown', event => {
        const directions = { ArrowLeft: [32, 0], ArrowRight: [-32, 0], ArrowUp: [0, 32], ArrowDown: [0, -32] };
        if (!directions[event.key]) return;
        event.preventDefault();
        const [width, height] = directions[event.key];
        setSize(window_.offsetWidth + width, window_.offsetHeight + height);
        setExpanded(false);
    });
    window.addEventListener('resize', () => {
        if (expanded) setSize(760, sizeLimits().height);
    });
    const append = (parent, tag, text = '', classes = '') => {
        const node = document.createElement(tag);
        node.textContent = text;
        node.className = classes;
        parent.append(node);
        return node;
    };
    const scroll = () => { messages.scrollTop = messages.scrollHeight; };
    const toggle = (value) => {
        open = value;
        window_.classList.toggle('open', value);
        window_.inert = !value;
        window_.setAttribute('aria-hidden', String(!value));
        trigger.setAttribute('aria-expanded', String(value));
        trigger.setAttribute('aria-label', value ? 'Close chat — PCForge Assistant' : 'Ask AI — PCForge Assistant');
        if (value) { badge.hidden = true; input.focus(); scroll(); }
        else trigger.focus();
    };
    trigger.addEventListener('click', () => toggle(!open));
    widget.querySelector('[data-ai-close]').addEventListener('click', () => toggle(false));
    widget.addEventListener('keydown', event => {
        if (event.key === 'Escape' && open) { event.preventDefault(); toggle(false); }
    });
    input.addEventListener('keydown', event => {
        if (event.key === 'Enter' && !event.shiftKey && !event.isComposing) {
            event.preventDefault();
            form.requestSubmit();
        }
    });
    input.addEventListener('input', () => {
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 88) + 'px';
    });
    const message = (text, role, time = Date.now()) => {
        const row = append(messages, 'div', '', `msg msg-${role}`);
        const bubble = append(row, 'div', '', 'msg-bubble');
        if (text) append(bubble, 'p', text);
        const timestamp = append(row, 'time', new Date(time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }), 'msg-time');
        timestamp.dateTime = new Date(time).toISOString();
        scroll();
        return bubble;
    };
    const checks = (parent, values, title) => {
        const details = append(parent, 'details');
        append(details, 'summary', title);
        const list = append(details, 'ul');
        Object.entries(values).forEach(([name, check]) => append(list, 'li', `${name}: ${check.status} — ${check.message}`));
    };
    const answer = (data, time) => {
        const bubble = message(data.answer, 'agent', time);
        checks(bubble, data.checks, 'Build checks at time of answer');
        const suggestion = data.suggestion;
        if (suggestion) {
            append(bubble, 'h3', 'Suggested parts');
            suggestion.parts.forEach(part => {
                const row = append(bubble, 'p', `${part.category}: `);
                const link = append(row, 'a', `${part.name} (${part.price})`);
                // Product links originate from PHP, never from the model's prose.
                link.href = part.url;
            });
            append(bubble, 'p', `Suggested parts: ${suggestion.replacement_cost}. Resulting build total: ${suggestion.total}.`);
            if (suggestion.over_budget) append(bubble, 'p', 'This suggestion exceeds your budget.', 'chat-warning');
            if (suggestion.review.missing_parts.length) append(bubble, 'p', `Still needed: ${suggestion.review.missing_parts.join(', ')}.`);
            if (suggestion.review.missing_prices.length) append(bubble, 'p', 'Some prices are missing; this total is incomplete.');
            const issues = Object.values(suggestion.review.checks).some(check => check.status !== 'compatible');
            append(bubble, 'p', issues ? 'Compatibility issues or unknowns need attention. Check the results before selecting parts.' : 'Basic checks passed. BIOS, connectors and unrecorded requirements still need verification.', issues ? 'chat-warning' : '');
            checks(bubble, suggestion.review.checks, 'Suggested build compatibility');
        }
        scroll();
    };
    const welcome = () => {
        message('Hi! I’m your PCForge assistant. Ask me about your selected parts, compatibility, or your next upgrade.', 'agent');
        quick.hidden = false;
    };
    const history = JSON.parse(widget.querySelector('[data-ai-history]').textContent || '[]');
    if (history.length) {
        history.forEach(turn => { message(turn.question, 'user', turn.time); answer(turn.response, turn.time); });
        quick.hidden = true;
    } else welcome();
    const updateControls = () => {
        const remaining = Math.max(0, Math.ceil((retryAt - Date.now()) / 1000));
        form.querySelector('[type="submit"]').disabled = busy || remaining > 0;
        reset.disabled = busy;
        quick.querySelectorAll('button').forEach(button => { button.disabled = busy || remaining > 0; });
        if (remaining && !busy) status.textContent = `You can send another message in ${remaining}s.`;
        else if (!busy) status.textContent = '';
        if (!remaining) clearInterval(cooldownTimer);
    };
    const cooldown = seconds => {
        retryAt = Date.now() + seconds * 1000;
        clearInterval(cooldownTimer);
        cooldownTimer = setInterval(updateControls, 1000);
        updateControls();
    };
    const request = async body => {
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 40000);
        try {
            const response = await fetch(form.action, { method: 'POST', body, credentials: 'same-origin', signal: controller.signal });
            const data = await response.json().catch(() => { throw new Error('The assistant is unavailable. Reload the page and try again.'); });
            if (!response.ok) {
                if (data.retry_after) cooldown(Number(data.retry_after));
                throw new Error(data.error || 'The assistant is unavailable. Please try again.');
            }
            return data;
        } finally { clearTimeout(timeout); }
    };
    quick.addEventListener('click', event => {
        const button = event.target.closest('[data-ai-prompt]');
        if (!button) return;
        input.value = button.dataset.aiPrompt;
        form.requestSubmit();
    });
    form.addEventListener('submit', async event => {
        event.preventDefault();
        const text = input.value.trim();
        if (!text || busy || Date.now() < retryAt) return;
        const body = new FormData(form);
        body.set('question', text);
        busy = true;
        updateControls();
        message(text, 'user');
        input.value = '';
        input.style.height = 'auto';
        quick.hidden = true;
        form.querySelector('details').open = false;
        const typing = append(messages, 'div', '', 'msg msg-agent typing-indicator');
        typing.setAttribute('aria-hidden', 'true');
        const dots = append(typing, 'div', '', 'msg-bubble');
        for (let i = 0; i < 3; i++) append(dots, 'span', '', 'typing-dot');
        status.textContent = 'Thinking about your build…';
        scroll();
        try {
            const data = await request(body);
            typing.remove();
            answer(data);
            cooldown(10);
            if (!open) badge.hidden = false;
        } catch (error) {
            typing.remove();
            const bubble = message(error.name === 'AbortError' ? 'That took too long. Please try again.' : error.message, 'agent');
            const retry = append(bubble, 'button', 'Try this message again', 'qr-btn');
            retry.type = 'button';
            retry.addEventListener('click', () => { input.value = text; input.focus(); form.requestSubmit(); });
            scroll();
        } finally {
            busy = false;
            updateControls();
        }
    });
    reset.addEventListener('click', async () => {
        if (busy) return;
        busy = true;
        updateControls();
        const body = new FormData();
        body.set('csrf_token', form.elements.csrf_token.value);
        body.set('action', 'reset');
        try {
            await request(body);
            messages.replaceChildren();
            form.reset();
            input.style.height = 'auto';
            badge.hidden = true;
            welcome();
            input.focus();
        } catch (error) {
            message(error.name === 'AbortError' ? 'Could not clear the chat. Please try again.' : error.message, 'agent');
        } finally { busy = false; updateControls(); }
    });
})();
