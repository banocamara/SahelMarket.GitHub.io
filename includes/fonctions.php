<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Fonction pour sécuriser les données entrantes (XSS)
function secure($donnee) {
    return htmlspecialchars(trim($donnee));
}

// Vérifie si l'utilisateur est connecté, sinon redirection
function verifierConnexion() {
    if (!isset($_SESSION['utilisateur_id'])) {
        header("Location: ../auth/login.php");
        exit();
    }
}

// Vérifie si l'utilisateur est un administrateur
function verifierAdmin() {
    verifierConnexion();
    if ($_SESSION['role'] !== 'admin') {
        header("Location: ../index.php");
        exit();
    }
}
?>