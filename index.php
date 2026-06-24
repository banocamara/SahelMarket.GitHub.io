<?php
require_once 'config/connexion.php';
require_once 'includes/fonctions.php';

// Récupérer les 3 dernières annonces publiées
$stmt = $pdo->query("SELECT * FROM annonces ORDER BY date_publication DESC LIMIT 3");
$annonces_populaires = $stmt->fetchAll();

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<main>
    <section class="hero">
        <img src="/SahelMarket/assets/images/banner-market1.png" alt="Annonce principale SahelMarket">
    </section>

    <section class="annonces">
        <h2>Annonces Récentes</h2>

        <div class="cards">
            <?php if (count($annonces_populaires) > 0): ?>
                <?php foreach ($annonces_populaires as $annonce): ?>
                    <div class="card">
                        <img src="/SahelMarket/assets/uploads/annonces/<?= htmlspecialchars($annonce['image']) ?>" alt="<?= htmlspecialchars($annonce['titre']) ?>">
                        <h3><?= htmlspecialchars($annonce['titre']) ?></h3>
                        <p><?= number_format($annonce['prix'], 0, ',', ' ') ?> MRU</p>
                        <a href="/SahelMarket/annonces/details.php?id=<?= $annonce['id'] ?>" class="btn-detail" style="display:inline-block; margin-bottom:15px; padding:10px 20px; background:blue; color:white; text-decoration:none; border-radius:5px;">Voir détail</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucune annonce n'est disponible pour le moment.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>