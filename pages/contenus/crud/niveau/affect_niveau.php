<?php
session_start();
require_once('../../../../connect_database.php');
$idUser = $_GET['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $conn = $database->getConnection();
    
    $id_niveau = $_POST['id_niveau'];
    $id_classe = $_POST['id_classe'];
    
    try {
        // Vérifier si cette association existe déjà
        $queryCheck = "SELECT * FROM avoir WHERE id_niveau = ? AND id_classe = ?";
        $stmtCheck = $conn->prepare($queryCheck);
        $stmtCheck->execute([$id_niveau, $id_classe]);
        
        if ($stmtCheck->rowCount() > 0) {
            $_SESSION['message'] = "Cette classe a déjà ce niveau";
            $_SESSION['message_type'] = "warning";
        } else {
            // Créer une nouvelle association
            $query = "INSERT INTO avoir (id_niveau, id_classe) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$id_niveau, $id_classe]);
            
            $_SESSION['message'] = "Niveau affecté à la classe avec succès";
            $_SESSION['message_type'] = "success";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Erreur: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: ../../niveau_difficult.php?id=".$idUser);
    exit();
}
?>