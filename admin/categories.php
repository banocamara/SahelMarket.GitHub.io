<?php
require_once '../config/connexion.php';
require_once '../includes/fonctions.php';

verifierAdmin();

$erreur = "";
$succes = "";

// 1. Ajouter une catégorie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_categorie'])) {
    $nom_cat = secure($_POST['nom_categorie']);
    if (empty($nom_cat)) {
        $erreur = "Le nom de la catégorie ne peut pas être vide.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO categories (nom) VALUES (?)");
        try {
            $stmt->execute([$nom_cat]);
            $succes = "Catégorie ajoutée avec succès !";
        } catch (PDOException $e) {
            $erreur = "Cette catégorie existe déjà.";
        }
    }
}

// 2. Supprimer une catégorie
if (isset($_GET['supprimer'])) {
    $id_sup = intval($_GET['supprimer']);
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id_sup]);
    header("Location: categories.php");
    exit();
}

// 3. Récupérer toutes les catégories
$categories = $pdo->query("SELECT * FROM categories ORDER BY nom ASC")->fetchAll();

require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<main style="padding: 40px 30px; max-width: 1200px; margin: 0 auto;">
    <a href="dashboard.php" style="text-decoration: none; color: blue;"><i class="fa-solid fa-arrow-left"></i> Retour au tableau de bord Admin</a>
    
    <h2 style="margin-top: 20px; margin-bottom: 20px;">Gestion des Catégories</h2>

    <?php if(!empty($erreur)): ?><div class="alert alert-danger"><?= $erreur ?></div><?php endif; ?>
    <?php if(!empty($succes)): ?><div class="alert alert-success"><?= $succes ?></div><?php endif; ?>

    <div style="display: flex; gap: 40px; flex-wrap: wrap; margin-top: 20px;">
        <!-- Formulaire d'ajout -->
        <div style="flex: 1; min-width: 300px;">
            <form action="categories.php" method="POST" style="box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <h3>Ajouter une catégorie</h3>
                <input type="text" name="nom_categorie" placeholder="Ex: Électronique, Mode..." required>
                <button type="submit" name="ajouter_categorie" class="btn">Créer la catégorie</button>
            </form>
        </div>

        <!-- Liste des catégories existantes sous forme de tableau standard -->
        <div style="flex: 2; min-width: 300px; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <h3 style="margin-bottom: 15px;">Catégories en place</h3>
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid #cbd5e1; background: #f8f9fa;">
                        <th style="padding: 10px;">ID</th>
                        <th style="padding: 10px;">Nom de la catégorie</th>
                        <th style="padding: 10px; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 10px;"><?= $cat['id'] ?></td>
                            <td style="padding: 10px; font-weight: 500;"><?= htmlspecialchars($cat['nom']) ?></td>
                            <td style="padding: 10px; text-align: center;">
                                <a href="categories.php?supprimer=<?= $cat['id'] ?>" style="color: red; text-decoration: none;" onclick="return confirm('Attention, supprimer cette catégorie supprimera également TOUTES les annonces associées ! Continuer ?');">
                                    <i class="fa-solid fa-trash"></i> Supprimer
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>