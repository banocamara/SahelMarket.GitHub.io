<?php
require_once '../config/connexion.php';
require_once '../includes/fonctions.php';

// Sécurité : Vérifie si l'utilisateur est connecté ET possède le rôle 'admin'
verifierAdmin();

// Récupération des statistiques globales de SahelMarket pour l'administrateur
$total_utilisateurs = $pdo->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
$total_annonces = $pdo->query("SELECT COUNT(*) FROM annonces")->fetchColumn();
$total_categories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();

require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<main style="padding: 40px 30px; max-width: 1200px; margin: 0 auto;">
    
    <div style="margin-bottom: 30px; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px;">
        <h2 style="color: #1e3a8a;"><i class="fa-solid fa-gauge"></i> Panel d'Administration - SahelMarket</h2>
        <p style="color: #64748b;">Gestion globale de la plateforme, des membres et des catégories du site.</p>
    </div>

    <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 40px;">
        <div style="flex: 1; min-width: 250px; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 5px solid blue;">
            <h3 style="color: #64748b; font-size: 14px; text-transform: uppercase;">Annonces globales en ligne</h3>
            <p style="font-size: 32px; font-weight: bold; color: #1e293b; margin-top: 10px;"><?= $total_annonces ?></p>
        </div>
        
        <div style="flex: 1; min-width: 250px; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 5px solid #10b981;">
            <h3 style="color: #64748b; font-size: 14px; text-transform: uppercase;">Utilisateurs inscrits</h3>
            <p style="font-size: 32px; font-weight: bold; color: #1e293b; margin-top: 10px;"><?= $total_utilisateurs ?></p>
        </div>

        <div style="flex: 1; min-width: 250px; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 5px solid #f59e0b;">
            <h3 style="color: #64748b; font-size: 14px; text-transform: uppercase;">Catégories créées</h3>
            <p style="font-size: 32px; font-weight: bold; color: #1e293b; margin-top: 10px;"><?= $total_categories ?></p>
        </div>
    </div>

    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <h3 style="margin-bottom: 20px; font-size: 18px;"><i class="fa-solid fa-toolbox"></i> Outils de modération</h3>
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            <a href="utilisateurs.php" class="btn" style="background-color: #1e293b;"><i class="fa-solid fa-users"></i> Gérer les Utilisateurs</a>
            <a href="categories.php" class="btn" style="background-color: #10b981;"><i class="fa-solid fa-tags"></i> Gérer les Catégories</a>
            <a href="../index.php" class="btn" style="background-color: #64748b;"><i class="fa-solid fa-eye"></i> Retourner sur le site public</a>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>