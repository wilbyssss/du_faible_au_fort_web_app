<?php
session_start();
require_once('../../../../connect_database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $conn = $database->getConnection();
    $idUser = $_GET['id'];
    $id = $_POST['id_phrase_a_trou'];
    $libelle = $_POST['libelle_phrase'];
    $indication = $_POST['indication'];
    $reponse = $_POST['reponse'];
    $exercice_id = $_POST['exercice_id'];
    
    try {
        $query = "UPDATE phrase_a_trou 
                 SET libelle_phrase_a_trou = :libelle, 
                     indication_phr = :indication, 
                     reponse_correspondante = :reponse, 
                     id_ex_trou = :exercice_id 
                 WHERE id_phrase_a_trou = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':libelle', $libelle);
        $stmt->bindParam(':indication', $indication);
        $stmt->bindParam(':reponse', $reponse);
        $stmt->bindParam(':exercice_id', $exercice_id);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $_SESSION['message'] = "Phrase modifiée avec succès";
        $_SESSION['message_type'] = "success";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Erreur: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: ../../phrase.php?id=".$idUser);
    exit();
}
?>