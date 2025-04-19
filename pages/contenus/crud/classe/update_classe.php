<?php
session_start();
require_once('../../../../connect_database.php');

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_classe= $_POST['id_classe'];
    $nom_classe= $_POST['nom_classe'];

    if (!empty($nom_classe) && !empty($id_classe)) {
        $query = "UPDATE classes SET nom_classe= ? WHERE id_classe= ?";
        $stmt = $conn->prepare($query);
        
        try {
            $stmt->execute([$nom_classe, $id_classe]);

            $_SESSION['message'] = "Le classe a été modifié avec succès !";
            $_SESSION['message_type'] = "success";
        } catch (PDOException $e) {
            $_SESSION['message'] = "Erreur lors de la modification : " . $e->getMessage();
            $_SESSION['message_type'] = "error";
        }
    } else {
        $_SESSION['message'] = "Le nom du classene peut pas être vide.";
        $_SESSION['message_type'] = "error";
    }

    header('Location: ../../classe.php?id='.$id);
    exit();
}
?>
