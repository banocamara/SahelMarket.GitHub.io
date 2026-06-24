<?php
require_once '../config/connexion.php';
require_once '../includes/fonctions.php';

verifierConnexion();

$utilisateur_id = $_SESSION['utilisateur_id'];

// Récupérer les messages reçus avec les infos de l'acheteur et de l'annonce liée
$stmt = $pdo->prepare("
    SELECT m.*, u.nom AS acheteur_nom, u.telephone AS acheteur_tel, a.titre AS annonce_titre 
    FROM messages m
    JOIN utilisateurs u ON m.expediteur_id = u.id
    JOIN annonces a ON m.annonce_id = a.id
    WHERE m.destinataire_id = ? 
    ORDER BY m.date_envoi DESC
");
$stmt->execute([$utilisateur_id]);
$messages_recus = $stmt->fetchAll();

require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<main style="padding: 40px 30px; max-width: 1000px; margin: 0 auto;">
    <div style="margin-bottom: 30px;">
        <h2><i class="fa-solid fa-envelope"></i> Votre boîte de réception</h2>
        <p style="color: #64748b;">Retrouvez ici les demandes des acheteurs concernant vos annonces.</p>
    </div>

    <div style="display: flex; flex-direction: column; gap: 20px;">
        <?php if (count($messages_recus) > 0): ?>
            <?php foreach ($messages_recus as $msg): ?>
                <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 4px solid #10b981;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; margin-bottom: 10px;">
                        <div>
                            <span style="font-size: 13px; color: #64748b;">Intéressé par :</span>
                            <strong style="color: blue;"><?= htmlspecialchars($msg['annonce_titre']) ?></strong>
                        </div>
                        <span style="font-size: 12px; color: #a0aec0;"><?= date('d/m/Y à H:i', strtotime($msg['date_envoi'])) ?></span>
                    </div>

                    <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 12px; font-style: italic; color: #4a5568;">
                        " <?= nl2br(htmlspecialchars($msg['contenu'])) ?> "
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 14px; flex-wrap: wrap; gap: 10px;">
                        <div>
                            <span style="color: #64748b;">De la part de :</span> <strong><?= htmlspecialchars($msg['acheteur_nom']) ?></strong>
                        </div>
                        <div>
                            <span style="color: #64748b;">Téléphone acheteur : </span>
                            <a href="tel:<?= $msg['acheteur_tel'] ?>" style="color: green; font-weight: bold; text-decoration: none;">
                                <i class="fa-solid fa-phone"></i> <?= htmlspecialchars($msg['acheteur_tel']) ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="background: white; padding: 40px; text-align: center; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <i class="fa-solid fa-envelope-open" style="font-size: 40px; color: #cbd5e1; margin-bottom: 15px;"></i>
                <p style="color: #64748b;">Aucun message reçu pour le moment.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>