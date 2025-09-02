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
  if (!is_file($path)) return;
  foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#') continue;
    [$k,$v] = array_map('trim', explode('=', $line, 2));
    putenv("$k=$v");
  }
}

loadEnv(__DIR__.'/config/.env');

$dsn = sprintf(
  'mysql:host=%s;dbname=%s;charset=utf8mb4',
  getenv('DB_HOST') ?: 'localhost',
  getenv('DB_NAME') ?: 'phpsec'
);

$db = new PDO(
  $dsn,
  getenv('DB_USER') ?: 'root',
 (getenv('DB_PASS') !== false ? getenv('DB_PASS') : null),
  [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]
);