<?php
session_start();
require_once('../../../../connect_database.php');

$database = new Database();
$conn = $database->getConnection();

if (isset($_GET['id'])) {
    $id_theme = $_GET['id'];
    $idUser = $_GET['user'];

    try {
        $conn->exec("SET FOREIGN_KEY_CHECKS =0 ");
        $query = "DELETE FROM themes WHERE id_theme = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id_theme]);
        $conn->exec("SET FOREIGN_KEY_CHECKS = 1");

        $_SESSION['message'] = "Le theme a été supprimé avec succès !";
        $_SESSION['message_type'] = "success";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Erreur lors de la suppression : " . $e->getMessage();
        $_SESSION['message_type'] = "error";
        echo $e->getMessage();
    }
}

header("Location: ../../theme.php?id=".$idUser);
exit();
?>
