<?php
// Run each script in a separate process: tests define their own helper functions.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }

$suites = [
    'offline' => ['order-workflow.php', 'ai-assistant.php', 'ai-endpoint.php', 'profile-settings.php'],
    'database' => ['catalog-install.php', 'web-catalog-import.php', 'admin-access.php', 'admin-database.php'],
    'http' => ['admin-workflows.php', 'checkout-builds.php'],
];
$option = $argv[1] ?? '--offline';
if ($argc > 2 || !in_array($option, ['--offline', '--database', '--http', '--all', '--help'], true)) {
    fwrite(STDERR, "Usage: php tests/run.php [--offline|--database|--http|--all|--help]\n");
    exit(2);
}
if ($option === '--help') {
    echo "Usage: php tests/run.php [--offline|--database|--http|--all]\n\n";
    echo "--offline   Isolated checks using SQLite and test doubles (default).\n";
    echo "--database  MariaDB checks using temporary databases or rolled-back records.\n";
    echo "--http      Development server checks using disposable database records.\n";
    echo "--all       Run all three suites.\n\n";
    echo "See tests/README.md for requirements and PCFORGE_TEST_URL.\n";
    exit;
}

$tests = $option === '--all'
    ? array_merge(...array_values($suites))
    : $suites[substr($option, 2)];
$failed = [];
foreach ($tests as $test) {
    echo "\nRunning $test\n";
    $process = proc_open(
        [PHP_BINARY, __DIR__ . '/' . $test],
        [STDIN, STDOUT, STDERR],
        $pipes,
        dirname(__DIR__)
    );
    if (!is_resource($process)) {
        fwrite(STDERR, "Unable to start $test.\n");
        $failed[] = $test;
        continue;
    }
    if (proc_close($process) !== 0) $failed[] = $test;
}

echo "\n";
echo (count($tests) - count($failed)) . '/' . count($tests) . " test scripts passed.\n";
if ($failed) fwrite(STDERR, 'Failed: ' . implode(', ', $failed) . PHP_EOL);
exit($failed ? 1 : 0);
