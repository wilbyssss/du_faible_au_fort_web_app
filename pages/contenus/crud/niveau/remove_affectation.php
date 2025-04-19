<?php
session_start();
require_once('../../../../connect_database.php'); // Chemin relatif à ajuster

// Récupérer les paramètres GET
$idUser = $_GET['user'];
$id_niveau = filter_input(INPUT_GET, 'id_niveau', FILTER_VALIDATE_INT);
$id_classe = filter_input(INPUT_GET, 'id_classe', FILTER_VALIDATE_INT);

// Validation des entrées
if (!$id_niveau || !$id_classe) {
    $_SESSION['message'] = "IDs invalides";
    $_SESSION['message_type'] = "error";
    header("Location: ../../texte_training.php?id=".$idUser);
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Requête de suppression
    $query = "DELETE FROM avoir WHERE id_niveau = :id_niveau AND id_classe = :id_classe";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_niveau', $id_niveau, PDO::PARAM_INT);
    $stmt->bindParam(':id_classe', $id_classe, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $_SESSION['message'] = "Affectation supprimée avec succès";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Aucune affectation trouvée";
        $_SESSION['message_type'] = "warning";
    }
} catch (PDOException $e) {
    $_SESSION['message'] = "Erreur lors de la suppression: " . $e->getMessage();
    $_SESSION['message_type'] = "error";
    error_log("Erreur suppression affectation: " . $e->getMessage());
}

header("Location: ../../niveau_difficult.php?id=".$idUser);
exit();
?>