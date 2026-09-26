<?php
// SQL identifiers must come from this fixed mapping, never directly from a URL.
function component_categories(): array
{
    return ['cpu'=>'CPU', 'gpu'=>'GPU', 'mb'=>'Motherboard', 'memory'=>'RAM', 'storage'=>'Storage', 'psu'=>'PSU', 'case_box'=>'Case', 'cooling'=>'CPU Cooler', 'fans'=>'Case Fan', 'monitor'=>'Monitor'];
}

function component_type($value): string
{
    if (!is_string($value) || !isset(component_categories()[$value])) {
        throw new InvalidArgumentException('Choose a valid component category.');
    }
    return $value;
}

function store_settings(): array
{
    static $settings;
    if ($settings === null) {
        $settings = db()->query('SELECT store_name, store_email, currency, low_stock_threshold, maintenance_mode FROM store_settings WHERE id = 1')->fetch();
        if (!$settings) throw new RuntimeException('Store settings are missing.');
    }
    return $settings;
}

function price_cents($amount): int
{
    if (!is_scalar($amount) || !preg_match('/^(\d{1,8})(?:\.(\d{1,2}))?$/D', (string) $amount, $match)) {
        throw new InvalidArgumentException('Enter a valid price with at most two decimal places.');
    }
    return (int) $match[1] * 100 + (int) str_pad($match[2] ?? '', 2, '0');
}

function cents_decimal(int $cents): string
{
    return intdiv($cents, 100) . '.' . str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT);
}
