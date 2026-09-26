<?php

require_once __DIR__ . '/ai.php';
require_once __DIR__ . '/saved-builds.php';

function aiBuildProposal(array $proposals, $token, int $owner, int $now): array
{
    if (!is_string($token) || !isset($proposals[$token])
        || $proposals[$token]['owner'] !== $owner || $proposals[$token]['expires'] < $now) {
        throw new InvalidArgumentException('This build card has expired. Ask the assistant for a fresh suggestion.');
    }
    return $proposals[$token];
}

function aiBuildParts(PDO $connection, array $ids): array
{
    $parts = [];
    foreach (savedBuildIds($ids) as $category => $id) {
        $part = aiProduct($connection, $category, $id);
        if (!$part || $part['price'] === null || (int) $part['stock'] <= 0) {
            throw new InvalidArgumentException('A part in this build is unavailable or out of stock. Ask for an updated build.');
        }
        $parts[$category] = $part;
    }
    if (!$parts) throw new InvalidArgumentException('This suggestion has no parts to load.');
    return $parts;
}

function aiBuildSave(PDO $connection, int $userId, array $proposal, string $name): int
{
    // A repeated click or a retry after a lost response must not create duplicates.
    if (!empty($proposal['saved_id'])) {
        $query = $connection->prepare('SELECT id FROM saved_builds WHERE id = ? AND user_id = ?');
        $query->execute([$proposal['saved_id'], $userId]);
        if ($query->fetchColumn()) return (int) $proposal['saved_id'];
    }
    return savedBuildCreate($connection, $userId, $name, $proposal['ids']);
}
