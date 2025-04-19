<?php
session_start();
$id =  $_GET['id'] ?? null;
require_once('../../../../connect_database.php');

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_classe = $_POST['nom_classe'];

    if (!empty($nom_classe)) {
        $query = "INSERT INTO classes (nom_classe) VALUES (?)";
        $stmt = $conn->prepare($query);
        
        try {
            $stmt->execute([$nom_classe]);

            $_SESSION['message'] = "Le classe a été ajouté avec succès !";
            $_SESSION['message_type'] = "success";
        } catch (PDOException $e) {
            $_SESSION['message'] = "Erreur lors de la création du classe : " . $e->getMessage();
            $_SESSION['message_type'] = "error";
        }
    } else {
        $_SESSION['message'] = "Le nom du classe ne peut pas être vide.";
        $_SESSION['message_type'] = "error";
    }

    header('Location: ../../classe.php?id='.$id);
    exit();
}
?>
