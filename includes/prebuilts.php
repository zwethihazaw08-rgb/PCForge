<?php

// Catalogue IDs are shared by the prebuilt gallery and the cart handler.
function prebuilt_templates(): array
{
    return [
        'nova' => [
            'name' => 'Forge Nova', 'use' => 'Gaming', 'number' => '04', 'is_new' => true,
            'description' => 'A streamlined AMD gaming build with a Ryzen 7 7700X, Radeon RX 7900 XTX, 32 GB of DDR5, and 2 TB of NVMe storage.',
            'ids' => ['cpu' => 32, 'mb' => 28, 'memory' => 26, 'gpu' => 24, 'storage' => 26, 'cooling' => 22, 'psu' => 24, 'case_box' => 21],
        ],
        'glacier' => [
            'name' => 'Forge Glacier', 'use' => 'Gaming', 'number' => '05', 'is_new' => true,
            'description' => 'A glass-panel NZXT H9 build with a Ryzen 7 7800X3D, RTX 5080, 64 GB of DDR5, and 360 mm liquid cooling.',
            'ids' => ['cpu' => 24, 'mb' => 28, 'memory' => 15, 'gpu' => 18, 'storage' => 26, 'cooling' => 17, 'psu' => 20, 'case_box' => 17],
        ],
        'pulse' => [
            'name' => 'Forge Pulse', 'use' => 'Gaming', 'number' => '06', 'is_new' => true,
            'description' => 'An Intel-powered gaming setup combining the Core i7-14700K, RTX 5080, 32 GB of DDR5, and liquid cooling in a Lian Li O11 case.',
            'ids' => ['cpu' => 27, 'mb' => 27, 'memory' => 19, 'gpu' => 18, 'storage' => 26, 'cooling' => 13, 'psu' => 20, 'case_box' => 12],
        ],
        'apex' => [
            'name' => 'Forge Apex', 'use' => 'Gaming', 'number' => '01',
            'description' => 'A high-refresh gaming build pairing the Ryzen 7 7800X3D with an RTX 5080 and fast 2 TB NVMe storage.',
            'ids' => ['cpu' => 24, 'mb' => 28, 'memory' => 15, 'gpu' => 18, 'storage' => 26, 'cooling' => 22, 'psu' => 24, 'case_box' => 24],
        ],
        'creator' => [
            'name' => 'Forge Creator', 'use' => 'Creator', 'number' => '02',
            'description' => 'A capable production system with 96 GB of memory, 4 TB of NVMe storage, and the Ryzen 9 7950X.',
            'ids' => ['cpu' => 22, 'mb' => 26, 'memory' => 16, 'gpu' => 24, 'storage' => 14, 'cooling' => 12, 'psu' => 20, 'case_box' => 15],
        ],
        'titan' => [
            'name' => 'Forge Titan', 'use' => 'Workstation', 'number' => '03',
            'description' => 'A flagship workstation built around the Core i9-14900KS, RTX 5090, 64 GB of DDR5, and 4 TB of NVMe storage.',
            'ids' => ['cpu' => 19, 'mb' => 14, 'memory' => 12, 'gpu' => 14, 'storage' => 16, 'cooling' => 13, 'psu' => 17, 'case_box' => 12],
        ],
    ];
}

function prebuilt_part_labels(): array
{
    return [
        'cpu' => 'Processor', 'mb' => 'Motherboard', 'memory' => 'Memory', 'gpu' => 'Graphics card',
        'storage' => 'Storage', 'cooling' => 'Cooling', 'psu' => 'Power supply', 'case_box' => 'Case',
    ];
}

function prebuiltCents($price): ?int
{
    if (!preg_match('/^(\d+)\.(\d{2})$/', (string) $price, $matches)) return null;
    return (int) $matches[1] * 100 + (int) $matches[2];
}

function prebuiltMoney(int $cents): string
{
    return money(intdiv($cents, 100) . '.' . str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT));
}

function prebuiltImage(?array $part): ?string
{
    $stored = (string) ($part['image_url'] ?? '');
    if (filter_var($stored, FILTER_VALIDATE_URL) && strtolower(parse_url($stored, PHP_URL_SCHEME) ?? '') === 'https') return $stored;
    if ($stored !== '' && is_file(__DIR__ . '/../assets/images/' . basename($stored))) return url('assets/images/' . rawurlencode(basename($stored)));
    return null;
}

function prebuiltIllustration(): void
{
    // A neutral concept illustration, not a photograph of the selected case.
    ?>
    <svg class="prebuilt-tower" viewBox="0 0 360 260" fill="none" aria-hidden="true">
        <ellipse cx="183" cy="238" rx="112" ry="10" fill="currentColor" opacity=".07"/>
        <path d="M88 46 223 23 283 57 149 82Z" fill="var(--forge-surface-raised)" stroke="currentColor" stroke-width="2"/>
        <path d="M88 46 223 23V212L88 234Z" fill="var(--forge-surface-raised)" stroke="currentColor" stroke-width="2"/>
        <path d="M223 23 283 57V224L223 212Z" fill="var(--forge-surface)" stroke="currentColor" stroke-width="2"/>
        <path d="M101 59 209 41V182L101 200Z" fill="var(--forge-surface)" stroke="currentColor" opacity=".7"/>
        <path d="M110 72 190 59V162L110 175Z" stroke="currentColor" opacity=".25"/>
        <path d="M172 71v46m10-48v46m10-48v46" stroke="currentColor" stroke-width="5" opacity=".5"/>
        <ellipse cx="139" cy="104" rx="21" ry="26" stroke="currentColor" stroke-width="2"/>
        <path d="m139 81 6 16-6 7-13-7m32 5-14 9-5-7-1-18m-14 33 11-12 4-3 7 17" stroke="currentColor" stroke-width="4" opacity=".45"/>
        <circle cx="139" cy="104" r="4" fill="currentColor"/>
        <path d="m108 149 95-16v26l-95 16Z" fill="var(--forge-surface-raised)" stroke="currentColor"/>
        <ellipse cx="135" cy="157" rx="10" ry="11" stroke="currentColor"/>
        <ellipse cx="177" cy="149" rx="10" ry="11" stroke="currentColor"/>
        <path d="m102 209 106-17v13l-106 17Z" fill="currentColor" opacity=".25"/>
        <?php foreach ([91, 139, 187] as $y): ?>
            <ellipse cx="251" cy="<?= $y ?>" rx="18" ry="23" stroke="currentColor" stroke-width="2" opacity=".6"/>
            <ellipse cx="251" cy="<?= $y ?>" rx="5" ry="7" fill="currentColor" opacity=".6"/>
        <?php endforeach; ?>
        <circle cx="251" cy="60" r="3" fill="currentColor"/>
    </svg>
    <?php
}

function prebuilt_builds(PDO $connection): array
{
    require_once __DIR__ . '/compatibility.php';
    $partLabels = prebuilt_part_labels();
    $templates = prebuilt_templates();
    $builds = [];
    $catalog = [];
    foreach ($partLabels as $category => $label) {
        $ids = array_unique(array_map(fn($template) => $template['ids'][$category], $templates));
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        // The table name comes only from the fixed list above.
        $query = $connection->prepare("SELECT * FROM `$category` WHERE id IN ($placeholders) AND status = 'active'");
        $query->execute(array_values($ids));
        $catalog[$category] = array_column($query->fetchAll(), null, 'id');
    }
    $caseSupport = [];
    foreach ($connection->query('SELECT case_id, form_factor FROM case_motherboard_support') as $row) $caseSupport[$row['case_id']][] = $row['form_factor'];
    $coolerSupport = [];
    foreach ($connection->query('SELECT cooling_id, socket FROM cooling_socket_support') as $row) $coolerSupport[$row['cooling_id']][] = $row['socket'];

    foreach ($templates as $key => $template) {
        $build = $template + ['parts' => [], 'cents' => 0, 'missing' => [], 'missing_prices' => false, 'stock' => 'Parts in stock', 'watts' => 0];
        $fallbackWatts = ['cpu' => 65, 'mb' => 50, 'memory' => 10, 'gpu' => 150, 'storage' => 5, 'cooling' => 5];
        foreach ($partLabels as $category => $label) {
            $part = $catalog[$category][$template['ids'][$category]] ?? null;
            $build['parts'][$category] = $part;
            if (!$part) {
                $build['missing'][] = $label;
                continue;
            }
            $cents = prebuiltCents($part['price']);
            $build['missing_prices'] = $build['missing_prices'] || $cents === null;
            $build['cents'] += $cents ?? 0;
            if ($part['stock'] !== null && (int) $part['stock'] === 0) $build['stock'] = 'Some parts out of stock';
            elseif ($part['stock'] === null && $build['stock'] === 'Parts in stock') $build['stock'] = 'Stock needs checking';
            if (isset($fallbackWatts[$category])) {
                $field = in_array($category, ['cpu', 'gpu'], true) ? 'tdp' : 'power_watts';
                $build['watts'] += (int) ($part[$field] ?? $fallbackWatts[$category]);
            }
        }
        $recommended = max((int) ceil($build['watts'] * 1.35 / 50) * 50, (int) ($build['parts']['gpu']['recommended_psu_watts'] ?? 0));
        $build['checks'] = checkBuildCompatibility(array_merge($build['parts'], [
            'motherboard' => $build['parts']['mb'] ?? [], 'case' => $build['parts']['case_box'] ?? [],
            'estimated_watts' => $build['watts'], 'recommended_psu_watts' => $recommended,
        ]), [
            'case_form_factors' => $caseSupport[$template['ids']['case_box']] ?? [],
            'cooling_sockets' => $coolerSupport[$template['ids']['cooling']] ?? [],
        ]);
        $statuses = array_column($build['checks'], 'status');
        $build['fit'] = in_array('incompatible', $statuses, true) ? 'incompatible'
            : (($build['missing'] || count($statuses) < 8 || array_diff($statuses, ['compatible'])) ? 'unknown' : 'compatible');
        $build['fit_label'] = ['compatible' => 'Basic checks passed', 'unknown' => 'Review compatibility', 'incompatible' => 'Fit issue found'][$build['fit']];
        $build['can_add'] = !$build['missing'] && !$build['missing_prices'] && $build['stock'] !== 'Some parts out of stock';
        $builds[$key] = $build;
    }

    return $builds;
}
