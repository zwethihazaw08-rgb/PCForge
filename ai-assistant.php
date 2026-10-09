<?php

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/ai.php';
require_once __DIR__ . '/includes/ai-builds.php';
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function aiReply(int $status, array $data): never
{
    http_response_code($status);
    echo json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    aiReply(405, ['error' => 'Use the assistant form to ask a question.']);
}
if ((int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 8192) aiReply(413, ['error' => 'Your question is too long.']);
if (!is_string($_POST['csrf_token'] ?? null) || !hash_equals(csrf_token(), $_POST['csrf_token'])) aiReply(403, ['error' => 'Your session expired. Reload the page and try again.']);
csrf_verify();

try {
    $owner = (int) ($_SESSION['user_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if (in_array($action, ['load_build', 'save_build', 'download_build'], true)) {
        $token = $_POST['proposal_token'] ?? null;
        $proposal = aiBuildProposal($_SESSION['ai_build_proposals'] ?? [], $token, $owner, time());
        $buildParts = aiBuildParts(db(), $proposal['ids']);
        $buildReview = aiReview(db(), $buildParts);
        if ($action === 'download_build') {
            aiReply(200, ['filename' => 'pcforge-build.json', 'document' => [
                'format' => 'pcforge-build', 'version' => 1, 'name' => $proposal['name'],
                'build_data' => $proposal['ids'], 'currency' => store_settings()['currency'],
                'products' => array_map('aiSpecs', $buildParts), 'review' => $buildReview,
                'note' => 'Product IDs belong to this PCForge catalog. Prices, stock and checks are a snapshot; recheck before purchasing.',
            ]]);
        }
        if ($action === 'load_build') {
            $_SESSION['build'] = $proposal['ids'];
            aiReply(200, ['redirect' => url('builder.php')]);
        }
        $name = savedBuildName($_POST['build_name'] ?? $proposal['name']);
        $user = auth_user();
        if (!$user) {
            // Reuse the existing protected save page and login return flow.
            $_SESSION['build'] = $proposal['ids'];
            aiReply(200, ['redirect' => url('saved-builds.php')]);
        }
        $savedId = aiBuildSave(db(), (int) $user['id'], $proposal, $name);
        $_SESSION['ai_build_proposals'][$token]['saved_id'] = $savedId;
        aiReply(200, ['saved' => true, 'url' => url('saved-builds.php'), 'message' => 'Build saved to your account.']);
    }
    if (($_SESSION['ai_chat_owner'] ?? null) !== $owner) {
        $_SESSION['ai_chat'] = [];
        $_SESSION['ai_chat_owner'] = $owner;
        $_SESSION['ai_chat_version'] = bin2hex(random_bytes(12));
    }
    if (($_POST['action'] ?? '') === 'reset') {
        $_SESSION['ai_chat'] = [];
        $_SESSION['ai_chat_version'] = bin2hex(random_bytes(12));
        aiReply(200, ['cleared' => true]);
    }
    $chatVersion = $_SESSION['ai_chat_version'] ?? '';
    $history = $_SESSION['ai_chat'] ?? [];
    $question = $_POST['question'] ?? '';
    $purpose = $_POST['purpose'] ?? '';
    $mode = $_POST['mode'] ?? 'review';
    if (!is_string($question) || trim($question) === '' || mb_strlen($question) > 1000) throw new InvalidArgumentException('Enter a question between 1 and 1,000 characters.');
    if (!in_array($mode, ['chat', 'review', 'upgrade', 'compare', 'budget', 'question'], true)) throw new InvalidArgumentException('Choose a valid assistant task.');
    if (!in_array($purpose, ['', 'gaming', 'school', 'editing', 'general use'], true)) throw new InvalidArgumentException('Choose a valid purpose.');
    $budget = ($_POST['budget'] ?? '') === '' ? null : price_cents($_POST['budget']);
    if ($budget !== null && $budget <= 0) throw new InvalidArgumentException('Enter a budget greater than zero.');
    if ($mode === 'budget' && ($budget === null || $purpose === '')) throw new InvalidArgumentException('Choose a budget and purpose for a new build.');
    $config = require __DIR__ . '/config/ai.php';
    if ($config['key'] === '') throw new AiServiceException('The AI assistant is not configured yet. Please contact the site administrator.');

    // Session locking makes this limit atomic, including simultaneous tabs.
    $now = time();
    $recent = array_values(array_filter($_SESSION['ai_requests'] ?? [], fn ($time) => $time > $now - 3600));
    $wait = max(0, (int) ($_SESSION['ai_retry_at'] ?? 0) - $now);
    if ($recent) $wait = max($wait, end($recent) + 10 - $now);
    if (count($recent) >= 20) $wait = max($wait, $recent[0] + 3600 - $now);
    if ($wait > 0) throw new AiServiceException('Please wait before asking another question.', 429, $wait);

    $connection = db();
    $parts = [];
    $unavailable = [];
    foreach (aiCategories() as $category => $label) {
        $id = filter_var($_SESSION['build'][$category] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$id) continue;
        $part = aiProduct($connection, $category, $id);
        if ($part) $parts[$category] = $part;
        else $unavailable[] = $label;
    }
    $context = ['task' => $mode, 'currency' => store_settings()['currency'], 'budget' => $budget === null ? null : cents_decimal($budget), 'purpose' => $purpose, 'selected_parts' => array_map('aiSpecs', $parts), 'unavailable_selections' => $unavailable, 'php_review' => aiReview($connection, $parts)];
    $context['compare'] = [];
    foreach (['compare_a', 'compare_b'] as $field) {
        $reference = $_POST[$field] ?? '';
        if ($reference === '') continue;
        if (!is_string($reference) || !preg_match('/^([a-z_]+):([1-9][0-9]*)$/D', $reference, $match)) throw new InvalidArgumentException('Choose valid comparison products.');
        $part = aiProduct($connection, $match[1], (int) $match[2]);
        if (!$part) throw new InvalidArgumentException('A comparison product is no longer available. Reload the page.');
        $trial = $parts;
        $trial[$match[1]] = $part;
        $context['compare'][] = ['category' => $match[1], 'product' => aiSpecs($part), 'checks_if_selected' => aiReview($connection, $trial)['checks']];
    }
    if ($mode === 'compare' && count($context['compare']) !== 2) throw new InvalidArgumentException('Select two products to compare.');
    $catalog = [];
    if (in_array($mode, ['chat', 'upgrade', 'budget'], true)) {
        $shares = ['cpu' => .20, 'gpu' => .32, 'mb' => .13, 'memory' => .08, 'storage' => .08, 'psu' => .08, 'case_box' => .07, 'cooling' => .04];
        foreach (aiCategories() as $category => $label) {
            // Small, price-relevant shortlist keeps free-tier token use bounded.
            $target = $budget === null ? (float) ($parts[$category]['price'] ?? 150) * 1.5 : $budget / 100 * (in_array($mode, ['budget', 'chat'], true) ? $shares[$category] : 1);
            $query = $connection->prepare("SELECT * FROM `$category` WHERE status = 'active' AND stock > 0 AND price IS NOT NULL ORDER BY ABS(price - ?), id LIMIT 3");
            $query->execute([$target]);
            foreach ($query->fetchAll() as $part) $catalog[$category][(int) $part['id']] = $part;
        }
        foreach ($catalog as $category => $products) {
            foreach ($products as $part) {
                $specs = aiSpecs($part);
                if (in_array($category, ['case_box', 'cooling'], true)) {
                    $table = $category === 'case_box' ? 'case_motherboard_support' : 'cooling_socket_support';
                    $field = $category === 'case_box' ? 'form_factor' : 'socket';
                    $idField = $category === 'case_box' ? 'case_id' : 'cooling_id';
                    $support = $connection->prepare("SELECT `$field` FROM `$table` WHERE `$idField` = ?");
                    $support->execute([$part['id']]);
                    $specs['supported_' . $field] = $support->fetchAll(PDO::FETCH_COLUMN);
                }
                $context['catalog'][$category][] = $specs;
            }
        }
        $context['catalog_scope'] = 'At most three price-relevant in-stock products per category. No guarantee the shortlist contains the best or a compatible complete build.';
    }
    $_SESSION['ai_requests'] = array_merge($recent, [$now]);
    session_write_close();
    $answer = aiAsk($config, $context, trim($question), $history);
    $suggestion = null;
    if ($answer['parts']) {
        if (!$catalog || count($answer['parts']) > 8) throw new AiServiceException('The AI suggested parts outside this request. Please try again.');
        $proposed = $mode === 'budget' || ($mode === 'chat' && $answer['selection_type'] === 'new_build') ? [] : $parts;
        $recommended = [];
        $cost = 0;
        foreach ($answer['parts'] as $category => $id) {
            if (!is_int($id) || !isset($catalog[$category][$id])) throw new AiServiceException('The AI suggested a product outside the available shortlist. Please try again.');
            $part = aiProduct($connection, $category, $id);
            if (!$part || (int) $part['stock'] <= 0 || $part['price'] === null) throw new AiServiceException('A suggested product is no longer available. Please ask again.');
            $proposed[$category] = $part;
            $cost += price_cents($part['price']);
            $recommended[] = ['name' => $part['name'], 'category' => aiCategories()[$category], 'price' => money($part['price']), 'url' => url('product.php?category=' . $category . '&id=' . $id)];
        }
        $review = aiReview($connection, $proposed);
        $suggestion = ['parts' => $recommended, 'review' => $review, 'total' => money($review['known_total']), 'replacement_cost' => money(cents_decimal($cost)), 'over_budget' => $budget !== null && $cost > $budget];
    }
    $response = ['answer' => $answer['answer'], 'checks' => $context['php_review']['checks'], 'suggestion' => $suggestion];
    session_start();
    // A reset or account change in another tab must not resurrect an older chat.
    if (($_SESSION['ai_chat_version'] ?? '') === $chatVersion && (int) ($_SESSION['user_id'] ?? 0) === $owner) {
        if ($suggestion) {
            $token = bin2hex(random_bytes(24));
            $ids = array_map(static fn ($part) => (int) $part['id'], $proposed);
            $name = 'AI ' . ($purpose !== '' ? $purpose . ' ' : '') . 'build';
            $proposals = array_filter($_SESSION['ai_build_proposals'] ?? [], static fn ($item) => $item['expires'] >= time() && $item['owner'] === $owner);
            $proposals[$token] = ['ids' => $ids, 'name' => $name, 'owner' => $owner, 'expires' => time() + 3600];
            $_SESSION['ai_build_proposals'] = array_slice($proposals, -8, null, true);
            $response['suggestion']['build'] = [
                'token' => $token, 'name' => $name, 'count' => count($ids),
                'parts' => array_map(static fn ($part) => $part['name'], $proposed),
                'signed_in' => $owner > 0,
            ];
        }
        $_SESSION['ai_chat'][] = ['question' => trim($question), 'response' => $response, 'time' => time() * 1000];
        $_SESSION['ai_chat'] = array_slice($_SESSION['ai_chat'], -4);
    }
    session_write_close();
    aiReply(200, $response);
} catch (InvalidArgumentException $exception) {
    aiReply(422, ['error' => $exception->getMessage()]);
} catch (AiServiceException $exception) {
    if ($exception->retryAfter) {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $_SESSION['ai_retry_at'] = time() + $exception->retryAfter;
        header('Retry-After: ' . $exception->retryAfter);
    }
    aiReply($exception->status, ['error' => $exception->getMessage(), 'retry_after' => $exception->retryAfter]);
} catch (Throwable $exception) {
    error_log('PCForge AI request failed: ' . get_class($exception));
    aiReply(503, ['error' => 'The assistant could not load your build or contact Groq. Please try again later.']);
}