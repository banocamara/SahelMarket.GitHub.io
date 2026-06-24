<?php
require_once '../config/connexion.php';
require_once '../includes/fonctions.php';

verifierConnexion();

$erreur = "";
$succes = "";
$id_annonce = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 1. Récupérer l'annonce et vérifier si elle existe
$stmt = $pdo->prepare("SELECT * FROM annonces WHERE id = ?");
$stmt->execute([$id_annonce]);
$annonce = $stmt->fetch();

if (!$annonce) {
    header("Location: ../dashboard.php");
    exit();
}

// 2. SÉCURITÉ STRICTE : Empêcher un utilisateur de modifier l'annonce d'un autre
if ($annonce['utilisateur_id'] != $_SESSION['utilisateur_id']) {
    header("Location: ../dashboard.php");
    exit();
}

// Récupération des catégories pour le formulaire
$categories = $pdo->query("SELECT * FROM categories ORDER BY nom ASC")->fetchAll();

// 3. Traitement de la modification du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = secure($_POST['titre']);
    $description = secure($_POST['description']);
    $prix = secure($_POST['prix']);
    $categorie_id = secure($_POST['categorie_id']);
    $nom_image = $annonce['image']; // Par défaut, on garde l'ancienne image

    if (empty($titre) || empty($description) || empty($prix) || empty($categorie_id)) {
        $erreur = "Veuillez remplir tous les champs obligatoires.";
    } else {
        // Gestion de l'upload si une nouvelle image est soumise
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                // Supprimer l'ancienne image physique du serveur s'il ne s'agit pas de l'image par défaut
                if ($annonce['image'] !== 'default.jpg' && file_exists('../assets/uploads/annonces/' . $annonce['image'])) {
                    unlink('../assets/uploads/annonces/' . $annonce['image']);
                }

                // Générer le nouveau nom unique
                $nom_image = uniqid('img_', true) . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], '../assets/uploads/annonces/' . $nom_image);
            } else {
                $erreur = "Format d'image non valide (JPG, PNG, WEBP uniquement).";
            }
        }

        // Si pas d'erreur, exécution de la mise à jour
        if (empty($erreur)) {
            $stmt_update = $pdo->prepare("UPDATE annonces SET titre = ?, description = ?, prix = ?, image = ?, categorie_id = ? WHERE id = ?");
            if ($stmt_update->execute([$titre, $description, $prix, $nom_image, $categorie_id, $id_annonce])) {
                $succes = "Votre annonce a été modifiée avec succès !";
                // Recharger les nouvelles données mis à jour pour réaffichage instantané
                $annonce['titre'] = $titre;
                $annonce['description'] = $description;
                $annonce['prix'] = $prix;
                $annonce['categorie_id'] = $categorie_id;
                $annonce['image'] = $nom_image;
            } else {
                $erreur = "Une erreur système s'est produite lors de la mise à jour.";
            }
        }
    }
}

require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<main>
    <section class="publier">
        <h2>Modifier votre annonce</h2>

        <?php if (!empty($erreur)): ?>
            <div class="alert alert-danger"><?= $erreur ?></div>
        <?php endif; ?>

        <?php if (!empty($succes)): ?>
            <div class="alert alert-success"><?= $succes ?></div>
        <?php endif; ?>

        <form action="modifier.php?id=<?= $id_annonce ?>" method="POST" enctype="multipart/form-data">
            <input type="text" name="titre" value="<?= htmlspecialchars($annonce['titre']) ?>" required>

            <textarea name="description" required><?= htmlspecialchars($annonce['description']) ?></textarea>

            <input type="number" name="prix" value="<?= htmlspecialchars($annonce['prix']) ?>" required>

            <select name="categorie_id" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $annonce['categorie_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div style="margin: 5px 0;">
                <p style="font-size: 14px; color:#64748b; margin-bottom: 5px;">Image actuelle :</p>
                <img src="../assets/uploads/annonces/<?= htmlspecialchars($annonce['image']) ?>" style="width: 100px; height: 70px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
            </div>

            <label style="font-weight: 500; margin-bottom: -5px;">Remplacer l'image (optionnel) :</label>
            <input type="file" name="image" accept="image/*">

            <div style="display: flex; gap: 15px; margin-top: 10px;">
                <button type="submit" class="btn" style="flex: 1;">Enregistrer les modifications</button>
                <a href="../dashboard.php" class="btn" style="background-color: #64748b; text-align: center; line-height: 24px;">Annuler</a>
            </div>
        </form>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>