<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\ChildModel;
use App\Model\LogbookModel;

/**
 * Contrôleur : enregistrement entrée / sortie (logbook).
 */
class LogController
{
    public function __construct(
        private ChildModel $childModel,
        private LogbookModel $logbookModel,
    ) {}

    public function log(): void
    {
        $childId = isset($_POST['child_id']) ? (int) $_POST['child_id'] : 0;
        $type = (string) ($_POST['type'] ?? '');

        if ($childId <= 0 || !in_array($type, ['entree', 'sortie'], true)) {
            $this->redirect('index.php');
            return;
        }

        if (!$this->childModel->getById($childId)) {
            $this->redirect('index.php');
            return;
        }

        $this->logbookModel->add($childId, $type);
        $this->redirect('index.php?action=child&id=' . $childId);
    }

    private function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}
