<?php
require_once '../config/connexion.php';
require_once '../includes/fonctions.php';

verifierAdmin();

// Récupérer la liste de tous les utilisateurs (sauf les mots de passe)
$utilisateurs = $pdo->query("SELECT id, nom, email, telephone, role, date_inscription FROM utilisateurs ORDER BY date_inscription DESC")->fetchAll();

require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<main style="padding: 40px 30px; max-width: 1200px; margin: 0 auto;">
    <a href="dashboard.php" style="text-decoration: none; color: blue;"><i class="fa-solid fa-arrow-left"></i> Retour au tableau de bord Admin</a>
    
    <h2 style="margin-top: 20px; margin-bottom: 20px;">Gestion des Comptes Utilisateurs</h2>

    <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 600px;">
            <thead>
                <tr style="border-bottom: 2px solid #cbd5e1; background: #f8f9fa;">
                    <th style="padding: 12px;">Nom</th>
                    <th style="padding: 12px;">Email</th>
                    <th style="padding: 12px;">Téléphone</th>
                    <th style="padding: 12px;">Rôle</th>
                    <th style="padding: 12px;">Date d'inscription</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($utilisateurs as $user): ?>
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 12px; font-weight: 500;"><?= htmlspecialchars($user['nom']) ?></td>
                        <td style="padding: 12px;"><?= htmlspecialchars($user['email']) ?></td>
                        <td style="padding: 12px;"><?= htmlspecialchars($user['telephone']) ?></td>
                        <td style="padding: 12px;">
                            <span style="padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; background: <?= $user['role'] === 'admin' ? '#fee2e2; color: #991b1b;' : '#f1f5f9; color: #334155;' ?>;">
                                <?= strtoupper($user['role']) ?>
                            </span>
                        </td>
                        <td style="padding: 12px; color: #64748b;"><?= date('d/m/Y', strtotime($user['date_inscription'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>