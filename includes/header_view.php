<?php
require_once __DIR__ . '/header.php';
$base_url = "/du_faible_au_fort/"
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Du Faible au Fort</title>
    
    <!-- Liens CSS Bootstrap + Custom -->
    <link href="<?= $base_url ?>assets/bootstrap-5.3.6-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        /* Style personnalisé */
        body {
            padding-top: 60px; /* Compensation pour le header fixe */
        }
        
        .fixed-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030; /* Doit être supérieur à tout autre élément */
        }
        
        .profile-pic {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #ffffff;
            display: inline-block;
        }
        
        /* Ajustement pour le contenu principal */
        main {
            margin-top: 20px; /* Espace supplémentaire après le header */
        }
    </style>
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo $header->getBaseUrl(); ?>images/favicon.ico">
</head>
<body>
    <!-- Header Bootstrap Fixe -->
    <header class="fixed-header navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <!-- Logo / Titre -->
            <a class="navbar-brand" href="#">
                <h1 class="h4 mb-0">Du Faible au Fort</h1>
            </a>
            
            <!-- Menu profil -->
            <div class="d-flex align-items-center">
                <div class="me-3 d-none d-sm-block">
                    <span class="text-white"><?php echo $header->getUsername(); ?></span>
                </div>
                
                <div class="dropdown">
                    <button class="btn btn-transparent dropdown-toggle" type="button" id="profileDropdown" 
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="profile-pic"></div>
                    </button>
                    
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li>
                            <a class="dropdown-item" href="<?php echo $header->getBaseUrl(); ?>pages/admin/my_account.php?id=<?php echo $header->getId(); ?>">
                                <i class="bi bi-person-circle me-2"></i>Mon Compte
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" >
                                <i class="bi bi-gear me-2"></i>Paramètres
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="<?php echo $header->getBaseUrl(); ?>session_close.php">
                                <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>


    <!-- Scripts JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="<?= $base_url ?>assets/bootstrap-5.3.6-dist/js/bootstrap.bundle.min.js"></script>