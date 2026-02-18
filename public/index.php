<?php

declare(strict_types=1);

/**
 * Point d'entrée sécurisé : liste, logbook, entrée/sortie.
 * Authentification par secret (config ADMIN_SECRET).
 */
session_start();

$projectRoot = dirname(__DIR__);

require_once $projectRoot . '/config.php';
require_once $projectRoot . '/inc/db.php';
require_once $projectRoot . '/src/functions.php';

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    $baseDir = dirname(__DIR__) . '/src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

if (!isset($pdo)) {
    die('Erreur : base de données non initialisée.');
}

$childModel = new \App\Model\ChildModel($pdo);
$logbookModel = new \App\Model\LogbookModel($pdo);
$childController = new \App\Controller\ChildController($childModel, $logbookModel);
$logController = new \App\Controller\LogController($childModel, $logbookModel);

$action = $_POST['action'] ?? $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Déconnexion
if ($action === 'logout') {
    $_SESSION['admin_authenticated'] = false;
    session_destroy();
    header('Location: index.php?action=login');
    exit;
}

// Connexion : formulaire ou vérification du secret
if ($action === 'login') {
    if (is_authenticated()) {
        header('Location: index.php');
        exit;
    }
    $loginError = isset($_GET['error']);
    $pageTitle = 'Garderie — Connexion';
    require $projectRoot . '/src/View/auth/login.php';
    exit;
}

if ($action === 'login_check') {
    $secret = truncate_to(trim((string) ($_POST['secret'] ?? '')), ADMIN_SECRET_MAX);
    if ($secret !== '' && $secret === ADMIN_SECRET) {
        $_SESSION['admin_authenticated'] = true;
        header('Location: index.php');
        exit;
    }
    header('Location: index.php?action=login&error=1');
    exit;
}

// Toutes les autres actions nécessitent une authentification
require_admin();

match ($action) {
    'list' => $childController->list(),
    'child' => $childController->show($id),
    'child_new' => $childController->newForm(),
    'child_create' => $childController->create(),
    'child_delete' => $childController->delete(),
    'log' => $logController->log(),
    default => $childController->list(),
};
