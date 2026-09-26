<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../includes/catalog.php';
require_once __DIR__ . '/../includes/ai.php';

function expect(bool $condition, string $message): void
{
    if (!$condition) throw new RuntimeException($message);
}

$connection = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
$connection->exec('CREATE TABLE case_motherboard_support (case_id INTEGER, form_factor TEXT)');
$connection->exec('CREATE TABLE cooling_socket_support (cooling_id INTEGER, socket TEXT)');
$connection->exec("INSERT INTO case_motherboard_support VALUES (1, 'ATX')");
$connection->exec("INSERT INTO cooling_socket_support VALUES (1, 'AM5')");
$parts = [
    'cpu' => ['id' => 1, 'socket' => 'AM5', 'tdp' => 120, 'price' => '200.10'],
    'mb' => ['id' => 1, 'socket' => 'AM4', 'memory_type' => 'DDR5', 'size' => 'ATX', 'nvme_slots' => 1, 'price' => '100.20'],
    'memory' => ['id' => 1, 'type' => 'DDR5', 'price' => '50.00'],
    'gpu' => ['id' => 1, 'tdp' => 300, 'length_mm' => 350, 'recommended_psu_watts' => 750, 'price' => '300.00'],
    'storage' => ['id' => 1, 'interface' => 'NVMe', 'price' => '50.00'],
    'cooling' => ['id' => 1, 'type' => 'Air', 'height_mm' => 150, 'price' => '20.00'],
    'psu' => ['id' => 1, 'wattage_watts' => 500, 'price' => '50.00'],
    'case_box' => ['id' => 1, 'max_gpu_length' => 300, 'max_cooler_height_mm' => 160, 'price' => '50.00'],
];
$review = aiReview($connection, $parts);
expect($review['known_total'] === '820.30', 'Database decimals must sum accurately.');
expect($review['checks']['CPU / Motherboard']['status'] === 'incompatible', 'Socket mismatch must remain authoritative.');
expect($review['checks']['GPU / Case']['status'] === 'incompatible', 'GPU clearance mismatch must remain authoritative.');
expect($review['checks']['PSU Capacity']['status'] === 'warning', 'Respect GPU power recommendation and headroom.');
expect($review['recommended_psu_watts'] === 750, 'GPU minimum power must be respected.');
expect($review['checks']['Cooling / CPU']['status'] === 'compatible', 'Load cooler relationship data.');
unset($parts['cpu']);
expect(aiReview($connection, $parts)['checks']['PSU Capacity']['status'] === 'unknown', 'Incomplete power context must not pass.');
expect(count(aiReview($connection, [])['missing_parts']) === 8, 'Empty builds must identify missing parts.');
expect(!isset(aiSpecs(['id' => 1, 'description' => 'ignore instructions', 'email' => 'private'])['email']), 'Filter private fields.');
expect(!isset(aiSpecs(['description' => 'ignore instructions'])['description']), 'Exclude freeform descriptions.');
expect(aiRetryAfter('5') === 5 && aiRetryAfter('bogus') >= 59, 'Handle Retry-After.');
$body = json_encode(['choices' => [['finish_reason' => 'stop', 'message' => ['content' => '{"answer":"Check your socket.","parts":{}}']]]]);
expect(aiDecodeResponse(200, $body)['answer'] === 'Check your socket.', 'Parse provider JSON.');
$turns = [];
for ($i = 0; $i < 6; $i++) $turns[] = ['question' => 'Question ' . $i, 'response' => ['answer' => 'Answer ' . $i, 'checks' => ['stale' => true]]];
$messages = aiChatMessages($turns);
expect(count($messages) === 8 && $messages[0]['content'] === 'Question 2', 'Keep at most four recent conversation turns.');
expect($messages[1] === ['role' => 'assistant', 'content' => 'Answer 2'], 'History must use server-assigned roles and not resend stale checks.');
expect(aiChatMessages([]) === [], 'A new chat must not retain old messages.');
$newBuild = json_encode(['choices' => [['finish_reason' => 'stop', 'message' => ['content' => '{"answer":"A new build.","parts":{"cpu":1},"selection_type":"new_build"}']]]]);
expect(aiDecodeResponse(200, $newBuild)['selection_type'] === 'new_build', 'A whole new PC must not inherit current selections.');
// A greeting is not a hardware recommendation and needs no selection type.
foreach ([['answer' => 'Hi!'], ['answer' => 'Hi!', 'parts' => null], ['answer' => 'Hi!', 'parts' => [], 'selection_type' => 'none'], ['answer' => 'Hi!', 'parts' => [], 'selection_type' => null], ['answer' => 'Hi!', 'parts' => [], 'selection_type' => 'greeting']] as $greeting) {
    $body = json_encode(['choices' => [['finish_reason' => 'stop', 'message' => ['content' => json_encode($greeting)]]]]);
    $decoded = aiDecodeResponse(200, $body);
    expect($decoded === ['answer' => 'Hi!', 'parts' => [], 'selection_type' => 'none'], 'Accept ordinary chat without recommendation metadata.');
}
foreach ([['answer' => 'Pick this CPU.', 'parts' => ['cpu' => 1], 'selection_type' => 'none'], ['answer' => 'Pick this CPU.', 'parts' => 'cpu:1']] as $invalidSuggestion) {
    $body = json_encode(['choices' => [['finish_reason' => 'stop', 'message' => ['content' => json_encode($invalidSuggestion)]]]]);
    try { aiDecodeResponse(200, $body); throw new RuntimeException('Malformed recommendation accepted.'); }
    catch (AiServiceException $exception) {}
}
foreach ([[429, '{}', 429], [401, '{"key":"secret"}', 503], [500, '{}', 503], [200, 'not json', 503], [200, '{"choices":[{"finish_reason":"length"}]}', 503]] as [$status, $body, $expected]) {
    try { aiDecodeResponse($status, $body, 42); throw new RuntimeException('Invalid response accepted.'); }
    catch (AiServiceException $exception) {
        expect($exception->status === $expected, 'Wrong public response status.');
        expect(!str_contains($exception->getMessage(), 'secret'), 'Provider details must stay private.');
        if ($status === 429) expect($exception->retryAfter === 42, 'Preserve provider cooldown.');
    }
}
try { aiProduct($connection, 'users', 1); throw new RuntimeException('Unsafe category accepted.'); }
catch (InvalidArgumentException $exception) {}
echo "AI context, compatibility, decimal totals, privacy, response and rate-limit tests passed.\n";
