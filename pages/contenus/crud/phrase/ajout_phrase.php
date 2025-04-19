<?php
session_start();
require_once('../../../../connect_database.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $conn = $database->getConnection();
    $idUser = $_GET['id'];
    $libelle = $_POST['libelle_phrase'];
    $indication = $_POST['indication'];
    $reponse = $_POST['reponse'];
    $exercice_id = $_POST['exercice_id'];
    
    try {
        $query = "INSERT INTO phrase_a_trou (libelle_phrase_a_trou, indication_phr, reponse_correspondante, id_ex_trou) 
                  VALUES (:libelle, :indication, :reponse, :exercice_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':libelle', $libelle);
        $stmt->bindParam(':indication', $indication);
        $stmt->bindParam(':reponse', $reponse);
        $stmt->bindParam(':exercice_id', $exercice_id);
        $stmt->execute();
        
        $_SESSION['message'] = "Phrase ajoutée avec succès";
        $_SESSION['message_type'] = "success";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Erreur: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: ../../phrase.php?id=".$idUser);
    exit();
}
?>