<?php
session_start();
require_once('../../../../connect_database.php');

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_GET['id'] ?? null;
    $nom_type = $_POST['nom_type'];

    if (!empty($nom_type)) {
        $query = "INSERT INTO type_compte (nom_type) VALUES (?)";
        $stmt = $conn->prepare($query);
        
        try {
            $stmt->execute([$nom_type]);
            $_SESSION['message'] = "Le type de compte a été ajouté avec succès !";
            $_SESSION['message_type'] = "success";
        } catch (PDOException $e) {
            $_SESSION['message'] = "Erreur lors de la création : " . $e->getMessage();
            $_SESSION['message_type'] = "error";
        }
    } else {
        $_SESSION['message'] = "Le nom ne peut pas être vide";
        $_SESSION['message_type'] = "error";
    }

    header('Location: ../../account.php?id='.$id);
    exit();
}
?>