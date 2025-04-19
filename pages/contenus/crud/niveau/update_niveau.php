<?php
session_start();
require_once('../../../../connect_database.php');

$database = new Database();
$conn = $database->getConnection();
$idUser = $_GET['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_niveau = $_POST['id_niveau'];
    $nom_niveau = $_POST['nom_niveau'];

    if (!empty($nom_niveau) && !empty($id_niveau)) {
        $query = "UPDATE niveau_difficulte SET nom_niveau = ? WHERE id_niveau = ?";
        $stmt = $conn->prepare($query);
        
        try {
            $stmt->execute([$nom_niveau, $id_niveau]);

            $_SESSION['message'] = "Le niveau a été modifié avec succès !";
            $_SESSION['message_type'] = "success";
        } catch (PDOException $e) {
            $_SESSION['message'] = "Erreur lors de la modification : " . $e->getMessage();
            $_SESSION['message_type'] = "error";
        }
    } else {
        $_SESSION['message'] = "Le nom du niveau ne peut pas être vide.";
        $_SESSION['message_type'] = "error";
    }

    header('Location: ../../niveau_difficult.php?id='.$idUser);
    exit();
}
?>
