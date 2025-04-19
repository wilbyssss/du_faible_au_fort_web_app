<?php
session_start();
require_once('../../../../connect_database.php');

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_theme = $_POST['id_theme'];
    $nom_theme = $_POST['nom_theme'];
    $idUser = $_GET['id'];

    if (!empty($nom_theme) && !empty($id_theme)) {
        $query = "UPDATE themes SET nom_theme = ? WHERE id_theme = ?";
        $stmt = $conn->prepare($query);
        
        try {
            $stmt->execute([$nom_theme, $id_theme]);

            $_SESSION['message'] = "Le theme a été modifié avec succès !";
            $_SESSION['message_type'] = "success";
        } catch (PDOException $e) {
            $_SESSION['message'] = "Erreur lors de la modification : " . $e->getMessage();
            $_SESSION['message_type'] = "error";
        }
    } else {
        $_SESSION['message'] = "Le nom du theme ne peut pas être vide.";
        $_SESSION['message_type'] = "error";
    }

    header("Location: ../../theme.php?id=".$idUser);
    exit();
}
?>
