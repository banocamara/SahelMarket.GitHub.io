<?php
require_once 'config/connexion.php';
require_once 'includes/fonctions.php';

// Sécurité : l'utilisateur doit être connecté pour accéder à son tableau de bord
verifierConnexion();

$utilisateur_id = $_SESSION['utilisateur_id'];

// Récupérer uniquement les annonces de l'utilisateur connecté avec leur catégorie
$stmt = $pdo->prepare("
    SELECT a.*, c.nom AS categorie_nom 
    FROM annonces a
    JOIN categories c ON a.categorie_id = c.id
    WHERE a.utilisateur_id = ? 
    ORDER BY a.date_publication DESC
");
$stmt->execute([$utilisateur_id]);
$mes_annonces = $stmt->fetchAll();

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<main style="padding: 40px 30px; max-width: 1200px; margin: 0 auto;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; margin-bottom: 30px; border-bottom: 2px solid #e2e8f0; padding-bottom: 20px;">
        <div>
            <h2 style="color: blue; margin: 0;"><i class="fa-solid fa-gauge"></i> Mon Espace Personnel</h2>
            <p style="color: #64748b; margin: 5px 0 0 0;">Bienvenue, <strong><?= htmlspecialchars($_SESSION['nom']) ?></strong> ! Gérez vos annonces et vos messages ici.</p>
        </div>
        
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="messages/boite.php" class="btn" style="background-color: #1e293b;"><i class="fa-solid fa-envelope"></i> Voir mes messages</a>
            <a href="annonces/ajouter.php" class="btn"><i class="fa-solid fa-plus"></i> Publier une nouvelle annonce</a>
        </div>
    </div>

    <h3 style="margin-bottom: 25px; color: #1a202c;"><i class="fa-solid fa-list"></i> Vos annonces en ligne (<?= count($mes_annonces) ?>)</h3>

    <div class="cards">
        <?php if (count($mes_annonces) > 0): ?>
            <?php foreach ($mes_annonces as $annonce): ?>
                <div class="card">
                    <img src="assets/uploads/annonces/<?= htmlspecialchars($annonce['image']) ?>" alt="<?= htmlspecialchars($annonce['titre']) ?>">
                    
                    <div class="card-body">
                        <span style="font-size: 12px; background: #e2e8f0; padding: 3px 8px; border-radius: 4px; align-self: flex-start; margin-bottom: 8px; font-weight: 500;">
                            <?= htmlspecialchars($annonce['categorie_nom']) ?>
                        </span>
                        
                        <h3><?= htmlspecialchars($annonce['titre']) ?></h3>
                        <p class="prix"><?= number_format($annonce['prix'], 0, ',', ' ') ?> MRU</p>
                        <p style="font-size: 12px; color: #a0aec0; margin-bottom: 15px;">Publié le : <?= date('d/m/Y', strtotime($annonce['date_publication'])) ?></p>
                        
                        <div style="display: flex; gap: 10px; margin-top: auto;">
                            <a href="annonces/modifier.php?id=<?= $annonce['id'] ?>" class="btn" style="background-color: #f59e0b; flex: 1; padding: 10px 0;">
                                <i class="fa-solid fa-pen"></i> Modifier
                            </a>
                            <a href="annonces/supprimer.php?id=<?= $annonce['id'] ?>" class="btn" style="background-color: #ef4444; flex: 1; padding: 10px 0;" onclick="return confirm('Êtes-vous sûr de vouloir supprimer définitivement cette annonce ?');">
                                <i class="fa-solid fa-trash"></i> Supprimer
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="background: white; padding: 40px; text-align: center; border-radius: 10px; width: 100%; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px dashed #cbd5e1;">
                <i class="fa-solid fa-store-slash" style="font-size: 48px; color: #cbd5e1; margin-bottom: 15px;"></i>
                <p style="margin-bottom: 20px; color: #64748b; font-size: 16px;">Vous n'avez pas encore publié d'annonce sur SahelMarket.</p>
                <a href="annonces/ajouter.php" class="btn">Commencer à vendre dès maintenant</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>