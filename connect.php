<?php
require_once __DIR__.'/config/config.php';

$db = new PDO(
  'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
  DB_USER,
  DB_PASS,
  [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Gestion des erreurs qui sert à afficher les erreurs SQL
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Gestion du mode de récupération des données
  ]
  // les deux lignes ci-dessous me permettent de gérer les erreurs et de récupérer les données sous forme de tableau associatif
);
> 