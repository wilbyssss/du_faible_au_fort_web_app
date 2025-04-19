<?php
session_start();
require_once('../../connect_database.php');

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $id = $_POST['id_admin'] ?? null;
    $currentpassword = $_POST['current_password'] ?? null;
    $newpassword = $_POST['new_password'] ?? null;

    if (!$id || !$currentpassword || !$newpassword) {
        echo "Erreur: Tous les champs sont requis";
        header("Location: my_account.php?id=$id");
        exit();
    }

    $database = new Database(); 
    $conn = $database->getConnection();

    try {
        // Récupérer le mot de passe haché de la base de données
        $query = "SELECT password_admin FROM administration WHERE id_admin = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            echo "Erreur: Utilisateur non trouvé";
            header("Location: my_account.php?id=$id");
            exit();
        }

        // Version pour mot de passe en clair (DÉCONSEILLÉ)
        if ($currentpassword !== $result['password_admin']) {
            echo "Erreur: Mot de passe actuel incorrect";
            header("Location: my_account.php?id=$id?error=1");
            exit();
        }
        
        // Version sécurisée avec password_verify (si mot de passe haché)
        // if (!password_verify($currentpassword, $result['password_admin'])) {
        //     echo "Erreur: Mot de passe actuel incorrect";
        //     //header("Location: my_account.php?id=$id?error=1");
        //     exit();
        // }

        // Hacher le nouveau mot de passe (si vous utilisez le hachage)
        // $hashedPassword = password_hash($newpassword, PASSWORD_DEFAULT);
        
        // Mise à jour du mot de passe
        $sql = "UPDATE administration SET password_admin = ? WHERE id_admin = ?";
        $stmt = $conn->prepare($sql);
        
        
        $stmt->execute([$newpassword, $id]);
        
        echo "Succès: Mot de passe mis à jour avec succès";
        
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
    }
    
    header("Location: my_account.php?id=$id");
    exit();
} else {
    echo "Erreur: Méthode non autorisée";
}