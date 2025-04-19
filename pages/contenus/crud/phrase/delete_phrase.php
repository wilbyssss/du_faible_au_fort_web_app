<?php
session_start();
require_once('../../../../connect_database.php');

if (isset($_GET['id'])) {
    $database = new Database();
    $conn = $database->getConnection();
    $id = $_GET['id'];
    $idUser = $_GET['user'];
    
    try {

        $query = "DELETE FROM phrase_a_trou WHERE id_phrase_a_trou = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $_SESSION['message'] = "Phrase supprimée avec succès";
        $_SESSION['message_type'] = "success";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Erreur: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: ../../phrase.php?id=".$idUser);
    exit();
}
?>