<?php
// -- CAS 1 --
// require_once __DIR__.'/config/config.php';

// $db = new PDO(
//   'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
//   DB_USER,
//   DB_PASS,
//   [
//     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Gestion des erreurs qui sert à afficher les erreurs SQL
//     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Gestion du mode de récupération des données
//   ]
//   // les deux lignes ci-dessous me permettent de gérer les erreurs et de récupérer les données sous forme de tableau associatif
// );


// -- CAS 2 --
function loadEnv(string $path): void {
    if (!is_file($path)) {
        throw new Exception("Le fichier .env est introuvable à l'emplacement : $path");
    }

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) {
            continue; // Ignorer les lignes vides et les commentaires
        }

        [$key, $value] = array_map('trim', explode('=', $line, 2));
        if (!empty($key)) {
            putenv("$key=$value");
        }
    }
}

// Charger le fichier .env
try {
    loadEnv(__DIR__ . '/config/.env');
} catch (Exception $e) {
    die("Erreur de configuration : " . $e->getMessage());
}

// Configuration de la connexion PDO
$dsn = sprintf(
    'mysql:host=%s;dbname=%s;charset=utf8mb4',
    getenv('DB_HOST') ?: die("DB_HOST non défini dans .env"),
    getenv('DB_NAME') ?: die("DB_NAME non défini dans .env"), 
);

// Connexion à la base de données
try {
    $db = new PDO(
        $dsn,
        getenv('DB_USER'),
        getenv('DB_PASS'),
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false, // Désactiver l'émulation des requêtes préparées
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}