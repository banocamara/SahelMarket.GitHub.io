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

// NOUVELLE FONCTION : Vérifie la robustesse du mot de passe à l'inscription
function isPasswordSecure($password) {
    // Minimum 8 caractères, au moins une majuscule, une minuscule et un chiffre
    return strlen($password) >= 8 
        && preg_match('/[A-Z]/', $password) 
        && preg_match('/[a-z]/', $password) 
        && preg_match('/[0-9]/', $password);
}
?>