<?php
session_start();
require_once('../../../../connect_database.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $conn = $database->getConnection();
    $idUser = $_GET['id'];
    $id = $_POST['id_text'];
    $titre= $_POST['titre_texte'];
    $contenu= $_POST['contenu'];
    echo $_SESSION['message'];
    try {
        $query = "UPDATE text_training  SET titre_text = :titre, contenu_text_training = :contenu WHERE id_text_training = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':titre', $titre);
        $stmt->bindParam(':contenu', $contenu);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $_SESSION['message'] = "Texte modifié avec succès";
        $_SESSION['message_type'] = "success";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Erreur: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: ../../texte_training.php?id=".$idUser);
    exit();
}
?>