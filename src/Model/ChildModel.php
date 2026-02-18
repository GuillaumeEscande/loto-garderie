<?php

declare(strict_types=1);

namespace App\Model;

use PDO;

/**
 * Modèle : accès aux données des enfants (fiches).
 */
class ChildModel
{
    public function __construct(private PDO $pdo) {}

    public function getAllWithStatus(): array
    {
        $sql = "
            SELECT c.id, c.lastname, c.firstname, c.created_at,
                   (SELECT type FROM logbook WHERE child_id = c.id ORDER BY created_at DESC LIMIT 1) AS last_status
            FROM children c
            ORDER BY c.lastname, c.firstname
        ";
        $st = $this->pdo->query($sql);
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getById(int $id): ?array
    {
        $st = $this->pdo->prepare("SELECT id, firstname, lastname, created_at FROM children WHERE id = ?");
        $st->execute([$id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(string $firstname, string $lastname): int
    {
        $st = $this->pdo->prepare("INSERT INTO children (firstname, lastname) VALUES (?, ?)");
        $st->execute([$firstname, $lastname]);
        return (int) $this->pdo->lastInsertId();
    }

    public function delete(int $id): void
    {
        $this->pdo->prepare("DELETE FROM children WHERE id = ?")->execute([$id]);
    }

    public function getContacts(int $childId): array
    {
        $st = $this->pdo->prepare("SELECT lastname, firstname, phone FROM contacts WHERE child_id = ? ORDER BY id");
        $st->execute([$childId]);
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function addContacts(int $childId, array $contacts): void
    {
        $st = $this->pdo->prepare("INSERT INTO contacts (child_id, lastname, firstname, phone) VALUES (?, ?, ?, ?)");
        foreach ($contacts as $c) {
            $st->execute([$childId, $c['lastname'] ?: '-', $c['firstname'] ?: '-', $c['phone'] ?: '-']);
        }
    }

    /** Dernier statut logbook : 'entree' | 'sortie' | null */
    public function getCurrentStatus(int $childId): ?string
    {
        $st = $this->pdo->prepare("SELECT type FROM logbook WHERE child_id = ? ORDER BY created_at DESC LIMIT 1");
        $st->execute([$childId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['type'] : null;
    }
}
