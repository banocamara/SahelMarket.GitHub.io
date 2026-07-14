<?php
require_once '../config/connexion.php';
require_once '../includes/fonctions.php';

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = secure($_POST['email']);
    $password = secure($_POST['mot_de_passe']);

    if (empty($email) || empty($password)) {
        $erreur = "Veuillez remplir tous les champs.";
    } else {
        // Préparation de la requête pour bloquer les injections SQL
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $utilisateur = $stmt->fetch();

        // Analyse du mot de passe haché
        if ($utilisateur && password_verify($password, $utilisateur['mot_de_passe'])) {
            
            // SÉCURITÉ : On enregistre le succès dans le journal de logs
            enregistrerLog($email, "SUCCES");

            // Initialisation de la session utilisateur
            $_SESSION['utilisateur_id'] = $utilisateur['id'];
            $_SESSION['nom'] = $utilisateur['nom'];
            $_SESSION['role'] = $utilisateur['role'];

            // Redirection vers le tableau de bord
            header("Location: ../dashboard.php");
            exit();
        } else {
            
            // SÉCURITÉ : On journalise l'échec pour surveiller d'éventuelles attaques par force brute
            enregistrerLog($email, "ECHEC - Identifiants incorrects");

            // Message d'erreur générique volontairement imprécis (bonne pratique OWASP)
            $erreur = "Identifiants incorrects.";
        }
    }
}

require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<main>
    <section class="publier">
        <h2>Connexion à votre espace</h2>
        
        <?php if(!empty($erreur)): ?>
            <p style="color: red; font-weight: bold;"><?= $erreur ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="Adresse Email" required>
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
        <p style="margin-top: 15px;">Pas encore de compte ? <a href="register.php">Inscrivez-vous ici</a></p>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>