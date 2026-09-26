<?php
require_once __DIR__ . '/compatibility.php';

function admin_build_summary(string $json): array
{
    static $productCache = [], $supportCache = [];
    $ids = json_decode($json,true); $parts = []; $missing = []; $cents = 0; $missingPrice = false;
    if (!is_array($ids)) $ids = [];
    $categories = component_categories(); unset($categories['fans'], $categories['monitor']);
    foreach ($categories as $type=>$label) {
        $id = filter_var($ids[$type] ?? null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
        $key = $type . ':' . $id;
        if (!array_key_exists($key,$productCache)) $productCache[$key] = $id ? admin_query("SELECT * FROM `$type` WHERE id = ?",[$id])->fetch() : null;
        $part = $productCache[$key];
        if (!$part) { $missing[] = $label; continue; }
        $parts[$type] = $part;
        if ($part['price'] === null) $missingPrice = true;
        else $cents += price_cents($part['price']);
    }
    $watts = 0;
    foreach (['cpu'=>65,'gpu'=>150,'mb'=>50,'memory'=>10,'storage'=>5,'cooling'=>5] as $type=>$fallback) {
        if (!isset($parts[$type])) continue;
        $field = in_array($type,['cpu','gpu'],true) ? 'tdp' : 'power_watts';
        $value = (int)($parts[$type][$field] ?? 0);
        $watts += $value > 0 ? $value : $fallback;
    }
    $recommended = max((int)ceil($watts * 1.35 / 50) * 50, (int)($parts['gpu']['recommended_psu_watts'] ?? 0));
    $relations = ['case_form_factors'=>[],'cooling_sockets'=>[]];
    if (isset($parts['case_box'])) {
        $key = 'case:' . $parts['case_box']['id'];
        $relations['case_form_factors'] = $supportCache[$key] ??= admin_query('SELECT form_factor FROM case_motherboard_support WHERE case_id = ?',[$parts['case_box']['id']])->fetchAll(PDO::FETCH_COLUMN);
    }
    if (isset($parts['cooling'])) {
        $key = 'cooling:' . $parts['cooling']['id'];
        $relations['cooling_sockets'] = $supportCache[$key] ??= admin_query('SELECT socket FROM cooling_socket_support WHERE cooling_id = ?',[$parts['cooling']['id']])->fetchAll(PDO::FETCH_COLUMN);
    }
    $checks = checkBuildCompatibility($parts + ['motherboard'=>$parts['mb'] ?? [],'case'=>$parts['case_box'] ?? [],'estimated_watts'=>$watts,'recommended_psu_watts'=>$recommended],$relations);
    return compact('parts','missing','cents','missingPrice','watts','recommended','checks');
}
