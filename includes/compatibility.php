<?php

// Compatibility checks return the same small structure everywhere.
// A missing required value is unknown; it is never treated as compatible.

function compatibilityResult(string $status, string $message): array
{
    return [
        'status' => $status,
        'message' => $message,
    ];
}

function normalizedValue($value): ?string
{
    if ($value === null || trim((string) $value) === '') {
        return null;
    }

    return strtolower(preg_replace('/\s+/', '', trim((string) $value)));
}

function checkCpuMotherboard(array $cpu, array $motherboard): array
{
    $cpuSocket = normalizedValue($cpu['socket'] ?? null);
    $boardSocket = normalizedValue($motherboard['socket'] ?? null);

    if ($cpuSocket === null || $boardSocket === null) {
        return compatibilityResult('unknown', 'Unknown — CPU or motherboard socket information is missing.');
    }
    if ($cpuSocket !== $boardSocket) {
        return compatibilityResult('incompatible', 'Incompatible — the CPU socket does not match the motherboard socket.');
    }

    return compatibilityResult('compatible', 'Compatible — CPU and motherboard sockets match.');
}

function checkMemoryMotherboard(array $memory, array $motherboard): array
{
    $memoryType = normalizedValue($memory['type'] ?? null);
    $boardType = normalizedValue($motherboard['memory_type'] ?? null);

    if ($memoryType === null || $boardType === null) {
        return compatibilityResult('unknown', 'Unknown — memory type information is missing.');
    }
    if ($memoryType !== $boardType) {
        return compatibilityResult('incompatible', 'Incompatible — memory type does not match the motherboard.');
    }

    return compatibilityResult('compatible', 'Compatible — memory type matches the motherboard.');
}

function checkGpuCase(array $gpu, array $case): array
{
    $gpuLength = filter_var($gpu['length_mm'] ?? null, FILTER_VALIDATE_INT);
    $caseClearance = filter_var($case['max_gpu_length'] ?? null, FILTER_VALIDATE_INT);

    if ($gpuLength === false || $caseClearance === false || $gpuLength === null || $caseClearance === null) {
        return compatibilityResult('unknown', 'Unknown — GPU length or case clearance information is missing.');
    }
    if ($gpuLength > $caseClearance) {
        return compatibilityResult('incompatible', "Incompatible — GPU is {$gpuLength} mm long but this case supports {$caseClearance} mm.");
    }

    return compatibilityResult('compatible', "Compatible — GPU length fits within the case's {$caseClearance} mm clearance.");
}

function checkMotherboardCase(array $motherboard, array $supportedFormFactors = []): array
{
    $formFactor = normalizedValue($motherboard['size'] ?? null);
    $supported = array_values(array_filter(array_map('normalizedValue', $supportedFormFactors)));

    if ($formFactor === null || !$supported) {
        return compatibilityResult('unknown', 'Unknown — motherboard or case form-factor support is missing.');
    }
    if (!in_array($formFactor, $supported, true)) {
        return compatibilityResult('incompatible', 'Incompatible — this case does not list support for the motherboard form factor.');
    }

    return compatibilityResult('compatible', 'Compatible — case support includes the motherboard form factor.');
}

function checkCoolingCpu(array $cooling, array $cpu, array $supportedSockets = []): array
{
    $cpuSocket = normalizedValue($cpu['socket'] ?? null);
    $supported = array_values(array_filter(array_map('normalizedValue', $supportedSockets)));

    if ($cpuSocket === null || !$supported) {
        return compatibilityResult('unknown', 'Unknown — cooler socket support information is missing.');
    }
    if (!in_array($cpuSocket, $supported, true)) {
        return compatibilityResult('incompatible', 'Incompatible — this cooler does not list support for the CPU socket.');
    }

    return compatibilityResult('compatible', 'Compatible — cooler support includes the CPU socket.');
}

function checkCoolingCase(array $cooling, array $case): array
{
    $coolerType = normalizedValue($cooling['type'] ?? null);
    $radiatorSize = filter_var($cooling['radiator_size_mm'] ?? null, FILTER_VALIDATE_INT);
    $caseSupport = filter_var($case['max_radiator_size'] ?? null, FILTER_VALIDATE_INT);

    if ($coolerType === null) {
        return compatibilityResult('unknown', 'Unknown — cooler type information is missing.');
    }
    if (str_contains($coolerType, 'liquid') || str_contains($coolerType, 'aio')) {
        if ($radiatorSize === false || $caseSupport === false || $radiatorSize === null || $caseSupport === null) {
            return compatibilityResult('unknown', 'Unknown — radiator size or case radiator support is missing.');
        }
        if ($radiatorSize > $caseSupport) {
            return compatibilityResult('incompatible', "Incompatible — {$radiatorSize} mm radiator exceeds the case's {$caseSupport} mm support.");
        }

        return compatibilityResult('compatible', 'Compatible — radiator size fits the case support.');
    }

    $coolerHeight = filter_var($cooling['height_mm'] ?? null, FILTER_VALIDATE_INT);
    $caseHeight = filter_var($case['max_cooler_height_mm'] ?? null, FILTER_VALIDATE_INT);
    if ($coolerHeight === false || $caseHeight === false || $coolerHeight === null || $caseHeight === null) {
        return compatibilityResult('unknown', 'Unknown — air-cooler height or case clearance is missing.');
    }
    if ($coolerHeight > $caseHeight) {
        return compatibilityResult('incompatible', "Incompatible — cooler height exceeds the case's {$caseHeight} mm clearance.");
    }

    return compatibilityResult('compatible', 'Compatible — cooler height fits the case clearance.');
}

function checkStorageMotherboard(array $storage, array $motherboard): array
{
    $interface = normalizedValue($storage['interface'] ?? null);
    if ($interface === null) {
        return compatibilityResult('unknown', 'Unknown — storage interface information is missing.');
    }

    if ($interface === 'nvme') {
        $slots = filter_var($motherboard['nvme_slots'] ?? null, FILTER_VALIDATE_INT);
        if ($slots === false || $slots === null) {
            return compatibilityResult('unknown', 'Unknown — motherboard M.2 slot information is missing.');
        }
        return $slots > 0
            ? compatibilityResult('compatible', 'Compatible — motherboard has an available M.2 slot.')
            : compatibilityResult('incompatible', 'Incompatible — motherboard does not list an available M.2 slot.');
    }

    if ($interface === 'sata') {
        $ports = filter_var($motherboard['sata_ports'] ?? null, FILTER_VALIDATE_INT);
        if ($ports === false || $ports === null) {
            return compatibilityResult('unknown', 'Unknown — motherboard SATA port information is missing.');
        }
        return $ports > 0
            ? compatibilityResult('compatible', 'Compatible — motherboard has an available SATA port.')
            : compatibilityResult('incompatible', 'Incompatible — motherboard does not list an available SATA port.');
    }

    return compatibilityResult('unknown', 'Unknown — storage interface is not supported by the basic checker.');
}

function checkPsuCapacity(array $psu, int $estimatedWatts, ?int $recommendedWatts = null): array
{
    $psuWatts = filter_var($psu['wattage_watts'] ?? null, FILTER_VALIDATE_INT);
    if ($psuWatts === false || $psuWatts === null) {
        return compatibilityResult('unknown', 'Unknown — PSU wattage information is missing.');
    }
    if ($psuWatts < $estimatedWatts) {
        return compatibilityResult('incompatible', "Insufficient — selected PSU provides {$psuWatts} W for an estimated {$estimatedWatts} W load.");
    }
    if ($recommendedWatts !== null && $psuWatts < $recommendedWatts) {
        return compatibilityResult('warning', "Warning — {$psuWatts} W meets the estimated load but is below the recommended {$recommendedWatts} W headroom.");
    }

    return compatibilityResult('compatible', "Compatible — selected PSU provides {$psuWatts} W.");
}

function checkBuildCompatibility(array $build, array $relationships = []): array
{
    $checks = [];

    if (!empty($build['cpu']) && !empty($build['motherboard'])) {
        $checks['CPU / Motherboard'] = checkCpuMotherboard($build['cpu'], $build['motherboard']);
    }
    if (!empty($build['memory']) && !empty($build['motherboard'])) {
        $checks['Memory / Motherboard'] = checkMemoryMotherboard($build['memory'], $build['motherboard']);
    }
    if (!empty($build['gpu']) && !empty($build['case'])) {
        $checks['GPU / Case'] = checkGpuCase($build['gpu'], $build['case']);
    }
    if (!empty($build['motherboard']) && array_key_exists('case_form_factors', $relationships) && !empty($build['case'])) {
        $checks['Motherboard / Case'] = checkMotherboardCase($build['motherboard'], $relationships['case_form_factors']);
    }
    if (!empty($build['cooling']) && !empty($build['cpu'])) {
        $checks['Cooling / CPU'] = checkCoolingCpu($build['cooling'], $build['cpu'], $relationships['cooling_sockets'] ?? []);
    }
    if (!empty($build['cooling']) && !empty($build['case'])) {
        $checks['Cooling / Case'] = checkCoolingCase($build['cooling'], $build['case']);
    }
    if (!empty($build['storage']) && !empty($build['motherboard'])) {
        $checks['Storage / Motherboard'] = checkStorageMotherboard($build['storage'], $build['motherboard']);
    }
    if (!empty($build['psu']) && isset($build['estimated_watts'])) {
        $checks['PSU Capacity'] = checkPsuCapacity($build['psu'], (int) $build['estimated_watts'], $build['recommended_psu_watts'] ?? null);
    }

    return $checks;
}
