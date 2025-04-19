<?php
session_start();
require_once('../../../../connect_database.php');

$database = new Database();
$conn = $database->getConnection();

if (isset($_GET['id'])) {
    $id = $_GET['id'] ?? null;
    $id_type = $_GET['id'];

    try {
        $query = "DELETE FROM type_compte WHERE id_type = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id_type]);

        $_SESSION['message'] = "Le type de compte a été supprimé avec succès !";
        $_SESSION['message_type'] = "success";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Erreur lors de la suppression : " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
}

header('Location: ../../account.php?id='.$id);
exit();
?>