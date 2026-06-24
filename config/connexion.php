<?php
$host = 'localhost';
$dbname = 'sahelmarket';
$username = 'root';
$password = ''; // Mets ton mot de passe s'il y en a un

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname; port=3307;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>