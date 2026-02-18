<?php
/**
 * Création des tables si besoin (utilise $pdo déjà défini par inc/db.php).
 */
$pdo->exec("
    CREATE TABLE IF NOT EXISTS children (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        lastname TEXT NOT NULL CHECK (length(lastname) <= 100),
        firstname TEXT NOT NULL CHECK (length(firstname) <= 100),
        created_at TEXT NOT NULL DEFAULT (datetime('now', 'localtime'))
    )
");

$pdo->exec("
    CREATE TABLE IF NOT EXISTS contacts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        child_id INTEGER NOT NULL,
        lastname TEXT NOT NULL CHECK (length(lastname) <= 100),
        firstname TEXT NOT NULL CHECK (length(firstname) <= 100),
        phone TEXT NOT NULL CHECK (length(phone) <= 30),
        FOREIGN KEY (child_id) REFERENCES children(id) ON DELETE CASCADE
    )
");

$pdo->exec("
    CREATE TABLE IF NOT EXISTS logbook (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        child_id INTEGER NOT NULL,
        type TEXT NOT NULL CHECK (type IN ('entree', 'sortie')),
        created_at TEXT NOT NULL DEFAULT (datetime('now', 'localtime')),
        FOREIGN KEY (child_id) REFERENCES children(id) ON DELETE CASCADE
    )
");
