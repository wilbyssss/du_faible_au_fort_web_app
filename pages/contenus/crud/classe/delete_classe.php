<?php
session_start();
require_once('../../../../connect_database.php');

$database = new Database();
$conn = $database->getConnection();

if (isset($_GET['id'])) {
    $id = $_GET['user'] ?? null;
    $id_classe = $_GET['id'];

    try {
        $conn->exec("SET FOREIGN_KEY_CHECKS = 0");
        $query =
         "DELETE FROM classes WHERE id_classe = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id_classe]);
        $conn->exec("SET FOREIGN_KEY_CHECKS = 1");

        $_SESSION['message'] = "Le classe a été supprimé avec succès !";
        $_SESSION['message_type'] = "success";
    } catch (PDOException $e) {
        echo  ($e->getMessage());
    }
}

header('Location: ../../classe.php?id='. $id);
exit();
?>
