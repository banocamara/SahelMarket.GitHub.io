<?php
require_once '../config/connexion.php';
require_once '../includes/fonctions.php';

// Sécurité : Vérifier que l'utilisateur est connecté
verifierConnexion();

$id_annonce = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 1. Récupérer l'annonce pour connaître le propriétaire et le fichier image associé
$stmt = $pdo->prepare("SELECT * FROM annonces WHERE id = ?");
$stmt->execute([$id_annonce]);
$annonce = $stmt->fetch();

if ($annonce) {
    // 2. SÉCURITÉ STRICTE : L'utilisateur connecté doit posséder cette annonce
    if ($annonce['utilisateur_id'] == $_SESSION['utilisateur_id']) {
        
        // 3. Supprimer le fichier image physiquement du dossier de stockage
        if ($annonce['image'] !== 'default.jpg' && file_exists('../assets/uploads/annonces/' . $annonce['image'])) {
            unlink('../assets/uploads/annonces/' . $annonce['image']);
        }
        
        // 4. Supprimer l'enregistrement dans la base de données
        $delete = $pdo->prepare("DELETE FROM annonces WHERE id = ?");
        $delete->execute([$id_annonce]);
    }
}

// Redirection vers le tableau de bord avec actualisation
header("Location: ../dashboard.php");
exit();
?>