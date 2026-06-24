<?php
require_once '../config/connexion.php';
require_once '../includes/fonctions.php';

// Sécurité : l'utilisateur doit être connecté pour ajouter
verifierConnexion();

$erreur = "";
$succes = "";

// Récupération des catégories pour alimenter la liste déroulante
$stmt_cat = $pdo->query("SELECT * FROM categories ORDER BY nom ASC");
$categories = $stmt_cat->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = secure($_POST['titre']);
    $description = secure($_POST['description']);
    $prix = secure($_POST['prix']);
    $categorie_id = secure($_POST['categorie_id']);
    $utilisateur_id = $_SESSION['utilisateur_id'];

    if (empty($titre) || empty($description) || empty($prix) || empty($categorie_id)) {
        $erreur = "Veuillez remplir tous les champs obligatoires.";
    } else {
        // Traitement de l'upload d'image
        $nom_image = "default.jpg"; // Image par défaut si aucun fichier choisi

        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                // Générer un nom unique pour éviter les collisions de fichiers
                $nom_image = uniqid('img_', true) . '.' . $ext;
                $destination = '../assets/uploads/annonces/' . $nom_image;

                // Créer le dossier s'il n'existe pas encore
                if (!is_dir('../assets/uploads/annonces/')) {
                    mkdir('../assets/uploads/annonces/', 0777, true);
                }

                move_uploaded_file($_FILES['image']['tmp_name'], $destination);
            } else {
                $erreur = "Format d'image non valide (JPG, PNG, WEBP uniquement).";
            }
        }

        // Si aucune erreur d'image, on insère dans la BDD
        if (empty($erreur)) {
            $stmt = $pdo->prepare("INSERT INTO annonces (titre, description, prix, image, categorie_id, utilisateur_id) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$titre, $description, $prix, $nom_image, $categorie_id, $utilisateur_id])) {
                $succes = "Votre annonce a été publiée avec succès !";
            } else {
                $erreur = "Une erreur système s'est produite lors de la publication.";
            }
        }
    }
}

require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<main>
    <section class="publier">
        <h2>Créer une nouvelle annonce</h2>

        <?php if (!empty($erreur)): ?>
            <div class="alert alert-danger"><?= $erreur ?></div>
        <?php endif; ?>

        <?php if (!empty($succes)): ?>
            <div class="alert alert-success"><?= $succes ?></div>
        <?php endif; ?>

        <form action="ajouter.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="titre" placeholder="Titre de l'annonce" required>

            <textarea name="description" placeholder="Description détaillée du bien ou du service..." required></textarea>

            <input type="number" name="prix" placeholder="Prix (en MRU)" required>

            <select name="categorie_id" required>
                <option value="">-- Choisir une catégorie --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
                <?php endforeach; ?>
            </select>

            <label style="font-weight: 500; margin-bottom: -5px;">Photo de l'annonce :</label>
            <input type="file" name="image" accept="image/*">

            <button type="submit" class="btn">Publier l'annonce</button>
        </form>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>