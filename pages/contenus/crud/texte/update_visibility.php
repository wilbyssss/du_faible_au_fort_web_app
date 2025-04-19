<?php
session_start();
require_once('../../../../connect_database.php');

// Toujours définir le Content-Type en premier
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $conn = $database->getConnection();
    
    $id = $_POST['id'];
    $visibility = $_POST['visibility'];
    
    try {
        $query = "UPDATE text_training SET visibility = :visible WHERE id_text_training = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':visible', $visibility);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            // Stockez le message dans la session si vous voulez l'afficher après
            $_SESSION['message'] = "Visibilité mise à jour avec succès";
            $_SESSION['message_type'] = "success";
            
            // Renvoyez une réponse JSON
            echo json_encode(['success' => true]);
            exit();
        }
    } catch (PDOException $e) {
        // Erreur JSON plutôt que redirection
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
        exit();
    }
}

// Réponse par défaut si la méthode n'est pas POST
echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
exit();
?>