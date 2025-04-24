<?php
require_once __DIR__ . '/header.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Du Faible au Fort</title>
    
    <!-- Liens CSS -->
    <link rel="stylesheet" href="<?php echo $header->getCssUrl('header.css'); ?>">
    <link rel="stylesheet" href="<?php echo $header->getCssUrl('slider_bar.css'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Favicon -->
    <link rel="icon" href="<?php echo $header->getBaseUrl(); ?>images/favicon.ico">
</head>
<body>
    <header class="header">
        <h1>Du Faible au Fort</h1>
        <div class="profile-container">
            <div style="margin-right:30px;"><h3><?php echo $header->getUsername(); ?></h3></div>
            <div class="profile">
                <div class="profile-pic" onclick="toggleProfileMenu()"></div>
                <div class="profile-menu" id="profileMenu">
                    <a href="<?php echo $header->getBaseUrl(); ?>pages/admin/my_account.php?id=<?php echo $header->getId(); ?>">Mon Compte</a>
                    <a href="#">Paramètres</a>
                    <a href="<?php echo $header->getBaseUrl(); ?>session_close.php">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Scripts JS -->
    <script src="<?php echo $header->getJsUrl('header.js'); ?>"></script>
    <script src="<?php echo $header->getJsUrl('slider_bar.js'); ?>"></script>