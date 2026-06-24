<?php
require_once '../config/connexion.php';
require_once '../includes/fonctions.php';

// Extraction et validation de l'ID passé en paramètre URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Requête avec jointures pour rassembler les informations complémentaires
$stmt = $pdo->prepare("
    SELECT a.*, c.nom AS categorie_nom, u.nom AS vendeur_nom, u.telephone AS vendeur_tel 
    FROM annonces a
    JOIN categories c ON a.categorie_id = c.id
    JOIN utilisateurs u ON a.utilisateur_id = u.id
    WHERE a.id = ?
");
$stmt->execute([$id]);
$annonce = $stmt->fetch();

// Si l'annonce n'existe pas en BDD, redirection immédiate vers la liste
if (!$annonce) {
    header("Location: liste.php");
    exit();
}

require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<main>
    <div class="details-container">
        
        <div class="details-image">
            <img src="../assets/uploads/annonces/<?= htmlspecialchars($annonce['image']) ?>" alt="<?= htmlspecialchars($annonce['titre']) ?>">
        </div>

        <div class="details-info">
            <h1><?= htmlspecialchars($annonce['titre']) ?></h1>
            <p class="prix-grand"><?= number_format($annonce['prix'], 0, ',', ' ') ?> MRU</p>
            
            <div class="meta-box">
                <p><strong>Catégorie :</strong> <?= htmlspecialchars($annonce['categorie_nom']) ?></p>
                <p><strong>Publiée le :</strong> <?= date('d/m/Y à H:i', strtotime($annonce['date_publication'])) ?></p>
            </div>

            <div style="margin-bottom: 25px;">
                <h3>Description</h3>
                <p style="margin-top: 10px; color: #4a5568; white-space: pre-line;">
                    <?= htmlspecialchars($annonce['description']) ?>
                </p>
            </div>

            <div class="meta-box" style="background-color: #eff6ff; border: 1px solid #bfdbfe;">
                <h3 style="color: #1e40af; margin-bottom: 8px;"><i class="fa-solid fa-user"></i> Informations Vendeur</h3>
                <p><strong>Nom :</strong> <?= htmlspecialchars($vendeur_nom = $annonce['vendeur_nom']) ?></p>
                <p><strong>Téléphone :</strong> <a href="tel:<?= $annonce['vendeur_tel'] ?>" style="color: blue; text-decoration: none; font-weight: bold;"><?= htmlspecialchars($annonce['vendeur_tel']) ?></a></p>
            </div>

            <a href="liste.php" class="btn" style="background-color: #64748b;"><i class="fa-solid fa-arrow-left"></i> Retour aux annonces</a>
        </div>

    </div>
</main>

<!-- Bloc à ajouter dans annonces/details.php juste avant le bouton de retour -->
<?php if (isset($_SESSION['utilisateur_id'])): ?>
    <?php if ($_SESSION['utilisateur_id'] != $annonce['utilisateur_id']): ?>
        <div style="margin-top: 20px; margin-bottom: 25px;">
            <a href="../messages/envoyer.php?annonce_id=<?= $annonce['id'] ?>" class="btn" style="background-color: #10b981; display: block; width: 100%;">
                <i class="fa-solid fa-paper-plane"></i> Envoyer un message au vendeur
            </a>
        </div>
    <?php else: ?>
        <p style="color: #64748b; font-style: italic; margin-top: 15px;">C'est votre propre annonce.</p>
    <?php endif; ?>
<?php else: ?>
    <div style="margin-top: 20px; margin-bottom: 25px; background: #fff7ed; border: 1px solid #ffedd5; padding: 15px; border-radius: 6px;">
        <p style="font-size: 14px; color: #c2410c;">Vous devez <a href="../auth/login.php" style="font-weight: bold; color: blue;">vous connecter</a> pour envoyer un message en ligne au vendeur.</p>
    </div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>