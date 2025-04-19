<?php
session_start();

// 1. Vérifier la session
if (isset($_SESSION['connecte']) && $_SESSION['connecte'] === true) {
    header('Location: pages/home_page.php?id=' . $_SESSION['user_id']);
    exit;
}

// 2. Vérifier le cookie remember_me
elseif (isset($_COOKIE['remember_me'])) {
    require_once('connect_database.php');
    
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare('SELECT id_admin FROM administration WHERE remember_token = ?');
        $stmt->execute([$_COOKIE['remember_me']]);
        $user = $stmt->fetch();
        
        if ($user) {
            $_SESSION['connecte'] = true;
            $_SESSION['user_id'] = $user['id_admin'];
            header('Location: pages/home_page.php?id=' . $user['id_admin']);
            exit;
        }
    } catch (PDOException $e) {
        // En cas d'erreur, continuer vers la page de login
    }
}

// 3. Rediriger vers le login
header('Location: pages/login.php');
exit;