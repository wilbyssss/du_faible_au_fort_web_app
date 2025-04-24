<?php
// Définir le chemin de base du projet
$base_url ="/du_faible_au_fort/"; 
$id = $_GET['id'] ?? null;
?>

<div class="sidebar" id="sidebar">
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    <a href="<?php echo $base_url; ?>pages/home_page.php?id=<?php echo $id?>">Accueil</a>
    <hr>

    <div class="dropdown">
        <a href="#">Administration ▾</a>
        <div class="dropdown-content">
            <a href="<?php echo $base_url; ?>pages/admin/user_admin.php?id=<?php echo $id?>">Utilisateurs Administration</a>
            <a href="<?php echo $base_url; ?>pages/admin/role_admin.php?id=<?php echo $id?>">Roles</a>
        </div>
    </div>
    <hr>

    <div class="dropdown">
        <a href="#">Contenus ▾</a>
        <div class="dropdown-content">
            <a href="<?php echo $base_url; ?>pages/contenus/theme.php?id=<?php echo $id?>">Thèmes</a>
            <a href="<?php echo $base_url; ?>pages/contenus/phrase.php?id=<?php echo $id?>">Phrases à trou</a>
            <a href="<?php echo $base_url; ?>pages/contenus/exercice.php?id=<?php echo $id?>">Exercices</a>
            <a href="<?php echo $base_url; ?>pages/contenus/niveau_difficult.php?id=<?php echo $id?>">Niveau de difficultés</a>
            <a href="<?php echo $base_url; ?>pages/contenus/texte_training.php?id=<?php echo $id?>">Texte d'entraînement</a>
            <a href="<?php echo $base_url; ?>pages/contenus/classe.php?id=<?php echo $id?>">Classes</a>
            <a href="<?php echo $base_url; ?>pages/contenus/account.php?id=<?php echo $id?>">Types de comptes</a>
        </div>
    </div>
    <hr>

    <div class="dropdown">
        <a href="#">Analyse ▾</a>
        <div class="dropdown-content">
            <a href="<?php echo $base_url; ?>pages/analyse/sessions.php">Sessions</a>
            <a href="<?php echo $base_url; ?>pages/analyse/trait_exercice.php">Traitement</a>
            <a href="<?php echo $base_url; ?>pages/analyse/user_cli.php">Utilisateurs Clients</a>
            <a href="<?php echo $base_url; ?>pages/analyse/chart.php">Chart</a>
        </div>
    </div>
    <hr>

    <a href="<?php echo $base_url; ?>pages/about.php">À propos</a>
</div>

<script>
    function toggleSidebar() {
        document.getElementById("sidebar").classList.toggle("active");
    }
</script>
