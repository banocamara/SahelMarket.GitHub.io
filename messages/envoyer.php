<?php
require_once '../config/connexion.php';
require_once '../includes/fonctions.php';

// Sécurité : Il faut être connecté pour envoyer un message
verifierConnexion();

$erreur = "";
$succes = "";
$annonce_id = isset($_GET['annonce_id']) ? intval($_GET['annonce_id']) : 0;

// Récupérer l'annonce pour identifier le vendeur (destinataire)
$stmt = $pdo->prepare("SELECT a.*, u.nom AS vendeur_nom FROM annonces a JOIN utilisateurs u ON a.utilisateur_id = u.id WHERE a.id = ?");
$stmt->execute([$annonce_id]);
$annonce = $stmt->fetch();

// Sécurité : Si l'annonce n'existe pas ou si c'est notre propre annonce
if (!$annonce || $annonce['utilisateur_id'] == $_SESSION['utilisateur_id']) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenu = secure($_POST['contenu']);
    $expediteur_id = $_SESSION['utilisateur_id'];
    $destinataire_id = $annonce['utilisateur_id']; // Le propriétaire de l'annonce

    if (empty($contenu)) {
        $erreur = "Le corps du message ne peut pas être vide.";
    } else {
        $stmt_ins = $pdo->prepare("INSERT INTO messages (expediteur_id, destinataire_id, annonce_id, contenu) VALUES (?, ?, ?, ?)");
        if ($stmt_ins->execute([$expediteur_id, $destinataire_id, $annonce_id, $contenu])) {
            $succes = "Votre message a été transmis avec succès au vendeur !";
        } else {
            $erreur = "Impossible d'envoyer le message pour le moment.";
        }
    }
}

require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<main>
    <section class="publier">
        <a href="../annonces/details.php?id=<?= $annonce_id ?>" style="text-decoration: none; color: blue;"><i class="fa-solid fa-arrow-left"></i> Retour à l'annonce</a>
        
        <h2 style="margin-top: 15px;">Contacter <?= htmlspecialchars($annonce['vendeur_nom']) ?></h2>
        <p style="color: #64748b; margin-bottom: 20px;">Sujet : <strong><?= htmlspecialchars($annonce['titre']) ?></strong></p>

        <?php if (!empty($erreur)): ?><div class="alert alert-danger"><?= $erreur ?></div><?php endif; ?>
        <?php if (!empty($succes)): ?><div class="alert alert-success"><?= $succes ?></div><?php endif; ?>

        <form action="envoyer.php?annonce_id=<?= $annonce_id ?>" method="POST">
            <textarea name="contenu" placeholder="Écrivez votre question ou votre proposition de prix ici..." required></textarea>
            <button type="submit" class="btn" style="background-color: #10b981;">Envoyer le message</button>
        </form>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>