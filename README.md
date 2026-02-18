# Garderie

Application web minimaliste pour gérer les entrées et sorties d’enfants à la garderie (soirée, événement).  
Conçue pour un hébergement PHP gratuit et une utilisation sur tablette ou téléphone.

## Fonctionnalités

- **Deux points d'accès** :
  - **Public** (`inscription.php`) : formulaire d'inscription d'un enfant uniquement, sans authentification. Idéal pour partager le lien aux familles.
  - **Sécurisé** (`index.php`) : liste des enfants, fiches, logbook, entrée/sortie, suppression. Protégé par un **secret** défini dans `config.php` (`ADMIN_SECRET`).
- Liste des enfants avec statut (présent / sorti), fiche avec contacts, logbook des entrées/sorties, boutons Entrée/Sortie.
- Stockage **SQLite** (fichier local), pas de MySQL requis.

## Démarrage rapide avec Docker

Prérequis : [Docker](https://docs.docker.com/get-docker/) et [Docker Compose](https://docs.docker.com/compose/install/).

```bash
git clone https://github.com/VOTRE_USER/garderie.git
cd garderie
cp config.php.exemple config.php   # puis éditer config.php et définir ADMIN_SECRET
docker compose up --build
```

Ouvrir dans le navigateur :
- **Accès public (inscription)** : http://localhost:8080/inscription.php  
- **Espace organisateur (liste, logbook)** : http://localhost:8080/ — saisir le secret défini dans `config.php` (`ADMIN_SECRET`).

Pour arrêter : `docker compose down`. Les données restent dans le volume `garderie-data`.

## Installation manuelle (PHP + Apache)

- PHP 8.x avec extensions **PDO** et **pdo_sqlite**
- Copier **`config.php.exemple`** en **`config.php`**, définir `ADMIN_SECRET` (et adapter les chemins si besoin).
- Pointer la racine web vers le répertoire **public/** (ou placer le contenu de `public/` à la racine et adapter les chemins dans `config.php`).
- Le dossier **data/** à la racine du projet doit être accessible en écriture (création automatique de la base au premier chargement).

## Structure du projet (MVC)

```
garderie/
├── config.php.exemple    # Modèle de configuration (copier en config.php)
├── config.php            # Configuration réelle (ignoré par git)
├── init_db.php           # Création des tables SQLite (contraintes CHECK sur les champs)
├── inc/
│   └── db.php            # Connexion PDO + chargement init_db
├── src/
│   ├── Controller/
│   │   ├── ChildController.php
│   │   └── LogController.php
│   ├── Model/
│   │   ├── ChildModel.php
│   │   └── LogbookModel.php
│   ├── View/
│   │   ├── partials/     # header.php, footer.php
│   │   ├── auth/         # login.php
│   │   └── children/     # list.php, show.php, new.php
│   └── functions.php     # h(), format_datetime(), is_authenticated(), require_admin(), truncate_to()
├── public/               # Racine web (document root)
│   ├── index.php         # Espace sécurisé (routage + auth par secret)
│   ├── inscription.php   # Accès public (formulaire inscription uniquement)
│   └── assets/
│       └── style.css
└── data/                 # Base SQLite (créée à l’exécution)
```

**Routage** (`public/index.php`) : le paramètre `action` (GET ou POST) détermine l’action. Sans authentification : `login`, `login_check`, `logout`. Avec authentification : `list`, `child`, `child_new`, `child_create`, `child_delete`, `log`.

## Licence

MIT — voir [LICENSE](LICENSE).
