<?php
require_once '../config/connexion.php';
require_once '../includes/fonctions.php';

$recherche = isset($_GET['recherche']) ? secure($_GET['recherche']) : '';

// Construction dynamique de la requête SQL selon la présence d'une recherche
if (!empty($recherche)) {
    $stmt = $pdo->prepare("SELECT * FROM annonces WHERE titre LIKE ? OR description LIKE ? ORDER BY date_publication DESC");
    $stmt->execute(["%$recherche%", "%$recherche%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM annonces ORDER BY date_publication DESC");
}

$annonces = $stmt->fetchAll();

require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<main>
    <section class="annonces">
        <?php if (!empty($recherche)): ?>
            <h2>Résultats de recherche pour : "<?= htmlspecialchars($recherche) ?>"</h2>
        <?php else: ?>
            <h2>Toutes les annonces disponibles</h2>
        <?php endif; ?>

        <div class="cards">
            <?php if (count($annonces) > 0): ?>
                <?php foreach ($annonces as $annonce): ?>
                    <div class="card">
                        <img src="../assets/uploads/annonces/<?= htmlspecialchars($annonce['image']) ?>" alt="<?= htmlspecialchars($annonce['titre']) ?>">
                        <div class="card-body">
                            <h3><?= htmlspecialchars($annonce['titre']) ?></h3>
                            <p class="prix"><?= number_format($annonce['prix'], 0, ',', ' ') ?> MRU</p>
                            <a href="details.php?id=<?= $annonce['id'] ?>" class="btn">Voir détail</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucune annonce ne correspond à votre recherche actuelle.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>