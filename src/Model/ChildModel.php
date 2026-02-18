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

    /**
     * Liste des enfants avec statut, filtrage et tri.
     * @param string $filter 'all' | 'present' | 'absent'
     * @param string $search Recherche dans prénom et nom (tronquée à 100 car.)
     * @return array
     */
    public function getAllWithStatusFiltered(string $filter = 'all', string $search = ''): array
    {
        $search = mb_substr(trim($search), 0, 100, 'UTF-8');
        $params = [];

        $sql = "
            SELECT * FROM (
                SELECT c.id, c.lastname, c.firstname, c.created_at,
                       (SELECT type FROM logbook WHERE child_id = c.id ORDER BY created_at DESC LIMIT 1) AS last_status
                FROM children c
            ) AS t
            WHERE 1=1
        ";

        if ($filter === 'present') {
            $sql .= " AND last_status = 'entree'";
        } elseif ($filter === 'absent') {
            $sql .= " AND (last_status IS NULL OR last_status = 'sortie')";
        }

        if ($search !== '') {
            $sql .= " AND (firstname LIKE ? OR lastname LIKE ?)";
            $term = '%' . $search . '%';
            $params[] = $term;
            $params[] = $term;
        }

        $sql .= " ORDER BY firstname, lastname";

        if ($params === []) {
            $st = $this->pdo->query($sql);
        } else {
            $st = $this->pdo->prepare($sql);
            $st->execute($params);
        }
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
