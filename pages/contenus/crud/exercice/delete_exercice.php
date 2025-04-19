<?php
require_once('../../../../connect_database.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $idUser = $_GET['user'] ?? null;
    $database = new Database();
    $conn = $database->getConnection();
    $conn->exec("SET FOREIGN_KEY_CHECKS = 0");
    $query = "DELETE FROM exercice_a_trou WHERE id_ex_trou = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    echo $idUser;

    if ($stmt->execute()) {
        $conn->exec("SET FOREIGN_KEY_CHECKS = 1");
        header('Location: ../../exercice.php?id='.$idUser);
    } else {
        header('Location: ../../exercice.php?id='.$idUser);
    }
}
?>
