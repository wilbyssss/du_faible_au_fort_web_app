<?php
session_start();

// Supprimer le token de la base de données
if (isset($_SESSION['user_id'])) {
    require_once('connect_database.php');
    
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare('UPDATE administration SET remember_token = NULL WHERE id_admin = ?');
        $stmt->execute([$_SESSION['user_id']]);
    } catch (PDOException $e) {
        // Logger l'erreur si nécessaire
    }
}

// Supprimer le cookie
setcookie('remember_me', '', time() - 3600, '/');

// Détruire la session
session_destroy();

// Empêcher le cache des pages sécurisées
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// Rediriger vers la page de login
header('Location: pages/login.php');
exit;
?>