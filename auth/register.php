<?php
require_once '../config/connexion.php';
require_once '../includes/fonctions.php';

$erreur = "";
$succes = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = secure($_POST['nom']);
    $email = secure($_POST['email']);
    $telephone = secure($_POST['telephone']);
    $password = secure($_POST['mot_de_passe']);
    $password_conf = secure($_POST['mot_de_passe_conf']);

    if (empty($nom) || empty($email) || empty($telephone) || empty($password)) {
        $erreur = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Format de l'adresse email invalide.";
    } elseif ($password !== $password_conf) {
        $erreur = "Les deux mots de passe ne correspondent pas.";
    }
     // ON INSÈRE LA VÉRIFICATION DE ROBUSTESSE ICI
    else if (!isPasswordSecure($password)) {
        $erreur = "Sécurité insuffisante : le mot de passe doit faire au moins 8 caractères, contenir une majuscule, une minuscule et au moins un chiffre.";
    } 
    else {
        // Vérification si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erreur = "Cette adresse email est déjà enregistrée.";
        } else {
            // Hachage du mot de passe
            $password_hache = password_hash($password, PASSWORD_BCRYPT);
            
            // Insertion du nouvel utilisateur
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, telephone, mot_de_passe) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$nom, $email, $telephone, $password_hache])) {
                $succes = "Votre compte a été créé avec succès ! Vous pouvez vous connecter.";
            } else {
                $erreur = "Une erreur est survenue lors de l'enregistrement.";
            }
        }
    }
}

require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<main>
    <section class="publier">
        <h2>Créer un compte SahelMarket</h2>
        
        <?php if(!empty($erreur)): ?>
            <p style="color: red; font-weight: bold;"><?= $erreur ?></p>
        <?php endif; ?>
        
        <?php if(!empty($succes)): ?>
            <p style="color: green; font-weight: bold;"><?= $succes ?></p>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <input type="text" name="nom" placeholder="Nom complet" required>
            <input type="email" name="email" placeholder="Adresse Email" required>
            <input type="text" name="telephone" placeholder="Numéro de Téléphone" required>
            <input type="password" name="mot_de_passe" placeholder="Votre mot de passe" required>
            <input type="password" name="mot_de_passe_conf" placeholder="Confirmez votre mot de passe" required>
            <button type="submit">S'inscrire</button>
        </form>
        <p style="margin-top: 15px;">Déjà un compte ? <a href="login.php">Connectez-vous ici</a></p>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>