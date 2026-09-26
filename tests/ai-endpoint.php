<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$scenario = $argv[1] ?? '';
if ($scenario === '') {
    foreach (['method' => 405, 'csrf' => 403, 'question' => 422, 'budget' => 422, 'unconfigured' => 503, 'reset' => 200] as $case => $status) {
        $lines = [];
        exec(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(__FILE__) . ' ' . escapeshellarg($case), $lines, $code);
        $output = implode("\n", $lines);
        $json = json_decode(explode("\nSTATUS:", $output)[0], true);
        if ($code !== 0 || !is_array($json) || !isset($json[$case === 'reset' ? 'cleared' : 'error']) || !str_ends_with($output, 'STATUS:' . $status)) {
            throw new RuntimeException('Endpoint scenario failed: ' . $case . ': ' . $output);
        }
        echo "PASS $case ($status)\n";
    }
    exit;
}
// Invalid/unconfigured requests stop before database or outbound network access.
putenv('GROQ_API_KEY=');
session_save_path(sys_get_temp_dir());
require_once __DIR__ . '/../includes/functions.php';
$_SESSION['csrf_token'] = 'test-token';
$_SERVER['REQUEST_METHOD'] = $scenario === 'method' ? 'GET' : 'POST';
$_POST = ['csrf_token' => $scenario === 'csrf' ? 'wrong-token' : 'test-token', 'question' => 'Explain RAM', 'mode' => 'question'];
if ($scenario === 'question') $_POST['question'] = ['invalid'];
if ($scenario === 'budget') $_POST['mode'] = 'budget';
if ($scenario === 'reset') {
    $_POST['action'] = 'reset';
    $_SESSION['ai_chat_owner'] = 0;
    $_SESSION['ai_chat'] = [['question' => 'old question']];
    $_SESSION['ai_requests'] = [time()];
}
register_shutdown_function(static function () use ($scenario) {
    if ($scenario === 'reset' && ($_SESSION['ai_chat'] !== [] || count($_SESSION['ai_requests']) !== 1)) exit(2);
    if (session_status() === PHP_SESSION_ACTIVE) session_destroy();
    echo "\nSTATUS:" . http_response_code();
});
require __DIR__ . '/../ai-assistant.php';
