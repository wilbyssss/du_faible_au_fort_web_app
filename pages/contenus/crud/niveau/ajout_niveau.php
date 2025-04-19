<?php
session_start();
require_once('../../../../connect_database.php');

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_niveau = $_POST['id_niveau'];
    $nom_niveau = $_POST['nom_niveau'];

    if (!empty($nom_niveau)) {
        $query = "INSERT INTO niveau_difficulte (nom_niveau) VALUES (?)";
        $stmt = $conn->prepare($query);
        
        try {
            $stmt->execute([$nom_niveau]);

            $_SESSION['message'] = "Le niveau a été ajouté avec succès !";
            $_SESSION['message_type'] = "success";
        } catch (PDOException $e) {
            $_SESSION['message'] = "Erreur lors de la création du niveau : " . $e->getMessage();
            $_SESSION['message_type'] = "error";
        }
    } else {
        $_SESSION['message'] = "Le nom du niveau ne peut pas être vide.";
        $_SESSION['message_type'] = "error";
    }

    header('Location: ../../niveau_difficult.php');
    exit();
}
?>
