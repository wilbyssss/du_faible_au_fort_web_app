<?php
session_start();
require_once('../../../../connect_database.php');

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_GET['id'] ?? null;
    $id_type = $_POST['id_type'];
    $nom_type = $_POST['nom_type'];

    if (!empty($nom_type) && !empty($id_type)) {
        $query = "UPDATE type_compte SET nom_type = ? WHERE id_type = ?";
        $stmt = $conn->prepare($query);
        
        try {
            $stmt->execute([$nom_type, $id_type]);
            $_SESSION['message'] = "Le type de compte a été modifié avec succès !";
            $_SESSION['message_type'] = "success";
        } catch (PDOException $e) {
            $_SESSION['message'] = "Erreur lors de la modification : " . $e->getMessage();
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