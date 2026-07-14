<?php
// On s'assure qu'une session est démarrée proprement
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Nettoie et sécurise les entrées utilisateur pour bloquer les failles XSS.
 * * @param string $donnee La chaîne brute envoyée par l'utilisateur.
 * @return string La chaîne nettoyée et échappée.
 */
function secure($donnee) {
    return htmlspecialchars(trim($donnee));
}

/**
 * Interdit l'accès aux utilisateurs non connectés.
 */
function verifierConnexion() {
    if (!isset($_SESSION['utilisateur_id'])) {
        header("Location: ../auth/login.php");
        exit();
    }
}

/**
 * Interdit l'accès aux utilisateurs qui ne sont pas administrateurs.
 */
function verifierAdmin() {
    verifierConnexion();
    if ($_SESSION['role'] !== 'admin') {
        header("Location: ../index.php");
        exit();
    }
}

/**
 * Valide la robustesse d'un mot de passe (Politique de sécurité).
 * Exige au minimum : 8 caractères, 1 majuscule, 1 minuscule, 1 chiffre.
 * * @param string $password Le mot de passe en clair à tester.
 * @return bool True si le mot de passe respecte les critères, sinon False.
 */
function isPasswordSecure($password) {
    return strlen($password) >= 8 
        && preg_match('/[A-Z]/', $password) 
        && preg_match('/[a-z]/', $password) 
        && preg_match('/[0-9]/', $password);
}

/**
 * Enregistre de manière asynchrone un événement de connexion dans les logs.
 * Crée automatiquement le dossier "logs" à la racine s'il n'existe pas.
 * * @param string $email L'adresse email de la tentative.
 * @param string $statut Le résultat de la tentative (SUCCES, ECHEC, etc.).
 */
function enregistrerLog($email, $statut) {
    // __DIR__ fait référence à "includes/". On remonte d'un dossier pour écrire à la racine du projet.
    $cheminDossier = __DIR__ . '/../logs';
    $cheminFichier = $cheminDossier . '/connexions.log';

    // Création automatique du dossier si inexistant (avec droits d'écriture sécurisés)
    if (!file_exists($cheminDossier)) {
        mkdir($cheminDossier, 0755, true);
    }

    $date = date('Y-m-d H:i:s');
    // Récupération sécurisée de l'adresse IP de l'utilisateur
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'IP_INCONNUE';

    // Formatage standardisé du log (idéal pour être analysé par un outil de sécurité ou un SIEM)
    $ligne = "[$date] IP: $ip | Compte: $email | Action: Connexion | Statut: $statut" . PHP_EOL;

    // Écrit la ligne à la suite du fichier existant (FILE_APPEND) de manière sécurisée
    file_put_contents($cheminFichier, $ligne, FILE_APPEND);
}
?>