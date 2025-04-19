<?php
session_start();
require_once('../../../../connect_database.php');

if (isset($_GET['id'])) {
    $database = new Database();
    $conn = $database->getConnection();
    $id = $_GET['id'];
    $idUser = $_GET['user'];
    
    try {
        $query = "DELETE FROM text_training WHERE id_text_training = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $_SESSION['message'] = "texte supprimée avec succès";
        $_SESSION['message_type'] = "success";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Erreur: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: ../../texte_training.php?id=".$idUser);
    exit();
}
?>