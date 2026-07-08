<?php
require_once '../config/connexion.php';
require_once '../includes/fonctions.php';

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = secure($_POST['email']);
    $password = secure($_POST['mot_de_passe']);

    if (empty($email) || empty($password)) {
        $erreur = "Veuillez remplir tous les champs.";
    } 
    // On appelle notre fonction ici !
    else if (!isPasswordSecure($password)) {
        $erreur = "Le mot de passe doit contenir au moins 8 caractères, incluant une majuscule, une minuscule et un chiffre.";
    }
    else {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $utilisateur = $stmt->fetch();

        if ($utilisateur && password_verify($password, $utilisateur['mot_de_passe'])) {
            // Initialisation des variables de session
            $_SESSION['utilisateur_id'] = $utilisateur['id'];
            $_SESSION['nom'] = $utilisateur['nom'];
            $_SESSION['role'] = $utilisateur['role'];

            // Redirection vers le tableau de bord de l'utilisateur
            header("Location: ../dashboard.php");
            exit();
        } else {
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