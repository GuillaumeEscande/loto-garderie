<?php

declare(strict_types=1);

/**
 * Point d'accès public : inscription d'un enfant uniquement (aucune authentification).
 * Pas d'accès à la liste ni au logbook.
 */
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['firstname'])) {
    $childController->create('inscription.php?created=1');
    exit;
}

$childController->newFormPublic();
