<?php
session_start();
require_once('../../../../connect_database.php');

$database = new Database();
$conn = $database->getConnection();
$idUser = $_GET['user'] ?? null;
if (isset($_GET['id'])) {
    $id_niveau = $_GET['id'];

    try {
        $conn->exec("SET FOREIGN_KEY_CHECKS = 0");
        $query = "DELETE FROM niveau_difficulte WHERE id_niveau = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id_niveau]);
        $conn->exec("SET FOREIGN_KEY_CHECKS = 1");

        $_SESSION['message'] = "Le niveau a été supprimé avec succès !";
        $_SESSION['message_type'] = "success";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Erreur lors de la suppression : " . $e->getMessage();
        $_SESSION['message_type'] = "error";
        echo "Erreur : ". $e->getMessage();
    }
}

header('Location: ../../niveau_difficult.php?id='.$idUser);
exit();
?>
