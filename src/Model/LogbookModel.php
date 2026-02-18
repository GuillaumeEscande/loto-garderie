<?php

declare(strict_types=1);

namespace App\Model;

use PDO;

/**
 * Modèle : journal des entrées / sorties.
 */
class LogbookModel
{
    public function __construct(private PDO $pdo) {}

    public function getByChildId(int $childId): array
    {
        $st = $this->pdo->prepare("SELECT type, created_at FROM logbook WHERE child_id = ? ORDER BY created_at DESC");
        $st->execute([$childId]);
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function add(int $childId, string $type): void
    {
        $st = $this->pdo->prepare("INSERT INTO logbook (child_id, type) VALUES (?, ?)");
        $st->execute([$childId, $type]);
    }
}
