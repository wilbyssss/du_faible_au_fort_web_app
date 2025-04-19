<?php
session_start();
require_once('../../../../connect_database.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $conn = $database->getConnection();
    $idUser = $_GET['id'];
    $titre= $_POST['titre_texte'];
    $contenu= $_POST['contenu'];
    $visible = 1;
    
    try {
        $query = "INSERT INTO text_training (titre_text, contenu_text_training, visibility) 
                  VALUES (:titre, :contenu, :visible)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':titre', $titre);
        $stmt->bindParam(':contenu', $contenu);
        $stmt->bindParam(':visible', $visible);
        $stmt->execute();
        
        $_SESSION['message'] = "Texte ajoutée avec succès";
        $_SESSION['message_type'] = "success";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Erreur: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: ../../texte_training.php?id=".$idUser);
    exit();
}
?>