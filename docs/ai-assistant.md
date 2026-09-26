# Groq assistant

The assistant opens from a floating chat button at the bottom-right of the builder and completed-build review. It follows the supplied chat template with PCForge charcoal colors and light/dark theme support. Saved builds use it after **Open in builder**. Type naturally, use a quick prompt, or expand **Budget & comparison options**. Enter sends a message; Shift+Enter adds a line. Close the chat with the close button or Escape. The widget stays open through builder step changes. Drag the top-left corner to resize it, or focus that handle and use the arrow keys (left/up enlarge; right/down shrink). The header expand button opens a wider reading view; restore returns to the previous size. Sizes stay within the viewport and remain while switching builder steps or closing/reopening the chat; a full page reload returns to the default size. **New chat** clears the conversation without resetting rate limits or the selected build.

## Server setup

1. Create an API key at https://console.groq.com/keys using Groq's free plan. Never paste it into chat, HTML, JavaScript, or committed files.
2. Set `GROQ_API_KEY` in the environment of the Apache/PHP process. For XAMPP, an Apache `SetEnv GROQ_API_KEY "your-key"` directive may be placed in private Apache configuration **outside `htdocs`**. Protect that configuration and restart Apache. Do not put the key in a project `.env`, `.htaccess`, or PHP file.
3. Optionally set `GROQ_MODEL`. The default is `openai/gpt-oss-20b`, listed in Groq's free-plan limits when implemented. A replacement must support Chat Completions JSON object responses. Availability and quotas depend on your account: https://console.groq.com/docs/rate-limits and https://console.groq.com/docs/models.
4. Enable PHP cURL and mbstring (both available in this XAMPP installation), keep TLS certificate verification enabled, and allow outbound HTTPS to `api.groq.com`.
5. Open the builder and ask a question. Without a key the existing site still works and the assistant reports that it is not configured.

## Flow and limits

`assets/js/ai-assistant.js` posts to `ai-assistant.php` using the existing session and CSRF token. Guests may use it, matching the builder. The server reloads the session's product IDs and selected comparison IDs from the database; clients cannot supply authoritative specs, prices, compatibility results, or another user's saved build. No customer account details are sent to Groq. Questions and catalog facts are sent only when a message is submitted. The last four successful turns are kept in the PHP session and sent as conversation context for follow-up questions. This recent history also restores the chat on page reloads and between builder/review pages. New chat clears it. History is scoped to the current signed-in user (or guest session), and newer database facts always override older chat statements. There is no browser localStorage transcript.

`includes/ai.php` calls the existing compatibility functions, with the completed-build page's power estimation and support-table relationships. It does not modify those functions or the build session. Missing information remains unknown. Existing pages, checkout and compatibility badges continue to use PHP.

Chat, upgrade and budget tasks send up to three price-relevant in-stock records per category to keep context bounded. This shortlist is not an exhaustive optimizer; a compatible complete build may not exist within it. Change the budget or select parts in the builder to refine advice. Comparison selectors show up to 100 active products per category. For an unlisted product select it in the builder first.

Groq returns short advice and structured recommended product IDs. PHP rejects IDs outside the supplied shortlist, rechecks current stock/prices, computes totals, and runs the compatibility checker over the proposed selection. Those results appear within the reply with expandable checks, including over-budget, incomplete and incompatible proposals. PHP checks the optional budget field precisely; budgets described only in conversation are advisory model context. Historical replies label checks as snapshots at the time of the answer. The model identifies new builds versus replacements so new builds do not inherit the current parts. The assistant never applies a suggestion or purchases anything. Model prose is advisory and can be mistaken; the separately displayed PHP results are authoritative. BIOS support, connectors, benchmarks and other unrecorded facts are not guaranteed by basic compatibility checks.

Requests have a 5-second connection timeout and 30-second total timeout. The UI handles failures, malformed replies and timeouts without rendering model HTML. Provider errors are sanitized; only status/error codes are logged. HTTP 429 respects `Retry-After` and saves a session cooldown; there are no automatic paid/repeated retries. Local throttling permits one request per 10 seconds and 20 per hour per session. This is a simple session-level safeguard, not protection against a user creating fresh sessions; for a public high-traffic deployment add a shared reverse-proxy/IP limit and monitor Groq account quotas. Free tier access does not mean unlimited requests; account settings determine billing.

## Verification

Run `php tests/ai-assistant.php` for isolated SQLite compatibility/context and provider-response tests, and `php tests/ai-endpoint.php` for method, CSRF, input-validation and missing-key checks. PHP lint should pass for the new files and both integrated pages. A real answer requires a configured Groq key; mocked error tests cannot verify account access or model availability.


