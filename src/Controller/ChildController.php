<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\ChildModel;
use App\Model\LogbookModel;

/**
 * Contrôleur : fiches enfants (liste, détail, création, suppression).
 */
class ChildController
{
    public function __construct(
        private ChildModel $childModel,
        private LogbookModel $logbookModel,
    ) {}

    public function list(): void
    {
        $filter = isset($_GET['filter']) && in_array($_GET['filter'], ['all', 'present', 'absent'], true)
            ? $_GET['filter'] : 'all';
        $search = trim((string) ($_GET['search'] ?? ''));

        $children = $this->childModel->getAllWithStatusFiltered($filter, $search);
        $this->render('children/list', [
            'children' => $children,
            'filter' => $filter,
            'search' => $search,
        ]);
    }

    public function show(int $id): void
    {
        $child = $this->childModel->getById($id);
        if (!$child) {
            $this->redirect('index.php');
            return;
        }
        $contacts = $this->childModel->getContacts($id);
        $logbook = $this->logbookModel->getByChildId($id);
        $currentStatus = $this->childModel->getCurrentStatus($id);
        $this->render('children/show', [
            'child' => $child,
            'contacts' => $contacts,
            'logbook' => $logbook,
            'currentStatus' => $currentStatus,
            'canEntree' => $currentStatus !== 'entree',
            'canSortie' => $currentStatus === 'entree',
        ]);
    }

    public function newForm(): void
    {
        $this->render('children/new', [
            'formAction' => 'index.php',
            'backUrl' => 'index.php',
            'backLabel' => 'Retour',
            'isPublic' => false,
        ]);
    }

    /** Formulaire public (inscription) : pas de lien vers l’admin. */
    public function newFormPublic(): void
    {
        $this->render('children/new', [
            'formAction' => 'inscription.php',
            'backUrl' => 'inscription.php',
            'backLabel' => 'Annuler',
            'isPublic' => true,
        ]);
    }

    /**
     * @param string|null $redirectUrl Si fourni, redirection après création (ex. inscription.php?created=1)
     */
    public function create(?string $redirectUrl = null): void
    {
        $firstname = truncate_to(trim((string) ($_POST['firstname'] ?? '')), CHILD_FIRSTNAME_MAX);
        $lastname = truncate_to(trim((string) ($_POST['lastname'] ?? '')), CHILD_LASTNAME_MAX);

        if ($firstname === '' || $lastname === '') {
            $target = $redirectUrl ? 'inscription.php?error=nom' : 'index.php?action=child_new&error=nom';
            $this->redirect($target);
            return;
        }

        $childId = $this->childModel->create($firstname, $lastname);

        $lastnames = $_POST['contact_lastname'] ?? [];
        $firstnames = $_POST['contact_firstname'] ?? [];
        $phones = $_POST['contact_phone'] ?? [];
        $contacts = [];
        for ($i = 0, $n = max(count($lastnames), count($firstnames), count($phones)); $i < $n; $i++) {
            $ln = truncate_to(trim((string) ($lastnames[$i] ?? '')), CONTACT_LASTNAME_MAX);
            $fn = truncate_to(trim((string) ($firstnames[$i] ?? '')), CONTACT_FIRSTNAME_MAX);
            $ph = truncate_to(trim((string) ($phones[$i] ?? '')), CONTACT_PHONE_MAX);
            if ($ln !== '' || $fn !== '' || $ph !== '') {
                $contacts[] = ['lastname' => $ln, 'firstname' => $fn, 'phone' => $ph];
            }
        }
        $this->childModel->addContacts($childId, $contacts);

        if ($redirectUrl !== null) {
            $this->redirect($redirectUrl);
            return;
        }
        $this->redirect('index.php?action=child&id=' . $childId);
    }

    public function delete(): void
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id > 0) {
            $this->childModel->delete($id);
        }
        $this->redirect('index.php');
    }

    private function render(string $view, array $data): void
    {
        extract($data, EXTR_SKIP);
        $viewPath = dirname(__DIR__, 2) . '/src/View/' . $view . '.php';
        if (!is_file($viewPath)) {
            throw new \RuntimeException("Vue introuvable : {$view}");
        }
        require $viewPath;
    }

    private function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}
