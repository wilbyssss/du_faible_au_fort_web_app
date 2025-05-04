<?php
// Définir le chemin de base du projet
$base_url = "/du_faible_au_fort/"; 
$id = $_GET['id'] ?? null;
?>

<!-- Sidebar Bootstrap -->
<div class="d-flex flex-column flex-shrink-0 p-3 bg-light sidebar" style="width: 250px; height: 100vh; position: fixed;">
    <!-- Bouton toggle pour mobile -->
    <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    
    <!-- Logo/Titre -->
    <a href="<?= $base_url ?>" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
        <span class="fs-4">Tableau de bord</span>
    </a>
    
    <hr>
    
    <!-- Contenu du sidebar -->
    <div class="collapse d-md-block" id="sidebarCollapse">
        <ul class="nav nav-pills flex-column mb-auto">
            <!-- Accueil -->
            <li class="nav-item">
                <a href="<?= $base_url ?>pages/home_page.php?id=<?= $id ?>" class="nav-link active" aria-current="page">
                    <i class="bi bi-house-door me-2"></i> Accueil
                </a>
            </li>
            
            <!-- Administration -->
            <li>
                <a class="nav-link link-dark dropdown-toggle" data-bs-toggle="collapse" href="#adminCollapse">
                    <i class="bi bi-shield-lock me-2"></i> Administration
                </a>
                <div class="collapse" id="adminCollapse">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <li><a href="<?= $base_url ?>pages/admin/user_admin.php?id=<?= $id ?>" class="nav-link link-dark rounded">Utilisateurs</a></li>
                        <li><a href="<?= $base_url ?>pages/admin/role_admin.php?id=<?= $id ?>" class="nav-link link-dark rounded">Rôles</a></li>
                    </ul>
                </div>
            </li>
            
            <!-- Contenus -->
            <li>
                <a class="nav-link link-dark dropdown-toggle" data-bs-toggle="collapse" href="#contentCollapse">
                    <i class="bi bi-collection me-2"></i> Contenus
                </a>
                <div class="collapse" id="contentCollapse">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <li><a href="<?= $base_url ?>pages/contenus/theme.php?id=<?= $id ?>" class="nav-link link-dark rounded">Thèmes</a></li>
                        <li><a href="<?= $base_url ?>pages/contenus/phrase.php?id=<?= $id ?>" class="nav-link link-dark rounded">Phrases à trou</a></li>
                        <li><a href="<?= $base_url ?>pages/contenus/exercice.php?id=<?= $id ?>" class="nav-link link-dark rounded">Exercices</a></li>
                        <li><a href="<?= $base_url ?>pages/contenus/niveau_difficult.php?id=<?= $id ?>" class="nav-link link-dark rounded">Niveaux</a></li>
                        <li><a href="<?= $base_url ?>pages/contenus/texte_training.php?id=<?= $id ?>" class="nav-link link-dark rounded">Textes</a></li>
                        <li><a href="<?= $base_url ?>pages/contenus/classe.php?id=<?= $id ?>" class="nav-link link-dark rounded">Classes</a></li>
                        <li><a href="<?= $base_url ?>pages/contenus/account.php?id=<?= $id ?>" class="nav-link link-dark rounded">Types de comptes</a></li>
                    </ul>
                </div>
            </li>
            
            <!-- Analyse -->
            <li>
                <a class="nav-link link-dark dropdown-toggle" data-bs-toggle="collapse" href="#analyticsCollapse" id="analyseSection">
                    <i class="bi bi-graph-up me-2"></i> Analyse
                </a>
                <div class="collapse" id="analyticsCollapse">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <li><a href="<?= $base_url ?>pages/analyse/sessions.php" class="nav-link link-dark rounded">Sessions</a></li>
                        <li><a href="<?= $base_url ?>pages/analyse/trait_exercice.php" class="nav-link link-dark rounded">Traitement</a></li>
                        <li><a href="<?= $base_url ?>pages/analyse/user_cli.php" class="nav-link link-dark rounded">Clients</a></li>
                        <li><a href="<?= $base_url ?>pages/analyse/chart.php" class="nav-link link-dark rounded">Graphiques</a></li>
                    </ul>
                </div>
            </li>
            
            <!-- À propos -->
            <li class="nav-item">
                <a href="<?= $base_url ?>pages/about.php" class="nav-link link-dark" id="about">
                    <i class="bi bi-info-circle me-2"></i> À propos
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Styles additionnels -->
<style>
    .sidebar {
        transition: all 0.3s;
        z-index: 1000;
        
    }
    
    @media (max-width: 767.98px) {
        .sidebar {
            width: 100% !important;
            height: auto !important;
            position: relative !important;
            
        }
    }
    
    .nav-link {
        border-radius: 4px;
        margin-bottom: 2px;
    }
    
    .nav-link:hover {
        background-color: #e9ecef;
    }
    
    .nav-link.active {
        background-color: #0d6efd;
        color: white !important;
    }
</style>
   

<!-- Script pour le toggle -->
<script>
    // Activer les dropdowns
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl)
    });

    var analyseSection = document.getElementById('analyseSection');
    var itemAnalyseSection = document.getElementById('analyticsCollapse')
    analyseSection.addEventListener('click', function(e) {
        e.preventDefault(); // Empêche le comportement par défaut du lien
        //masque les diferent liens de la section analys 
        itemAnalyseSection.style.display = "none";
        // Affiche un message indiquant que la section n'est pas encore disponible
        var divShowAlert = document.createElement('div');
        divShowAlert.className = 'alert alert-info alert-dismissible fade show';
        divShowAlert.role = 'alert';
        divShowAlert.innerHTML = '<strong>Information :</strong> Cette section n\'est pas encore disponible. <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        document.querySelector('.sidebar').appendChild(divShowAlert);
        setTimeout(function() {
            divShowAlert.remove();
        }, 5000); // 5 secondes
    });

    var aboutUs = document.getElementById('about');
    aboutUs.addEventListener('click', function (e) {
        e.preventDefault();
        var divShowAlert = document.createElement('div');
        divShowAlert.className = 'alert alert-info alert-dismissible fade show';
        divShowAlert.role = 'alert';
        divShowAlert.innerHTML = '<strong>Information :</strong> Cette section n\'est pas encore disponible. <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        document.querySelector('.sidebar').appendChild(divShowAlert);
        setTimeout(function() {
            divShowAlert.remove();
        }, 5000); // 5 secondes
    })
</script>