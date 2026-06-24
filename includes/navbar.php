<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <nav>
        <div class="about">
            <div class="titre">
                <a href="/SahelMarket/index.php" style="text-decoration: none; color: blue;">
                    <i class="fa-solid fa-store"></i> SahelMarket
                </a>
            </div>

            <div class="cherche">
                <form action="/SahelMarket/annonces/liste.php" method="GET" style="display: flex; width: 100%;">
                    <input type="search" name="recherche" placeholder="Rechercher une annonce...">
                    <button type="submit" style="background: none; border: none; cursor: pointer;">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>
            </div>

            <div class="dispo">
                <i class="fa-solid fa-phone"></i>
                <a href="#">Disponible 24/24</a>
            </div>
        </div>

        <ul class="accueil">
            <li><a href="/SahelMarket/index.php">Accueil</a></li>
            <li><a href="/SahelMarket/annonces/liste.php">Annonces</a></li>
            
            <?php if (isset($_SESSION['utilisateur_id'])): ?>
                <li><a href="/SahelMarket/annonces/ajouter.php">Publier</a></li>
                <li><a href="/SahelMarket/dashboard.php">Mon Tableau de bord</a></li>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li><a href="/SahelMarket/dashboard.php" style="color: red; font-weight: bold;">Admin</a></li>
                <?php endif; ?>
                <li><a href="/SahelMarket/auth/logout.php" style="color: brown;"><i class="fa-solid fa-sign-out-alt"></i> Déconnexion</a></li>
            <?php else: ?>
                <li><a href="/SahelMarket/auth/login.php"><i class="fa-solid fa-sign-in-alt"></i> Connexion</a></li>
                <li><a href="/SahelMarket/auth/register.php"><i class="fa-solid fa-user-plus"></i> Inscription</a></li>
            <?php endif; ?>

            <li class="shop"><i class="fa-solid fa-cart-shopping"></i> MRU</li>
        </ul>
    </nav>
</header>