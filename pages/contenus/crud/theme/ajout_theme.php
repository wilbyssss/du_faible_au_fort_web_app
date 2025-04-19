<?php

require_once('../../../../connect_database.php');
$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération du nom du thème
    $nom_theme = $_POST['nom_theme'];
    $idUser = $_GET['id'];

    // Vérifier si le champ 'nom_theme' n'est pas vide
    if (!empty($nom_theme)) {
        // Préparer la requête d'insertion
        $query = "INSERT INTO themes (nom_theme) VALUES (?)";
        $stmt = $conn->prepare($query);
        
        try {
            // Exécuter la requête
            $stmt->execute([$nom_theme]);
            $message = "Le thème a été ajouté avec succès !"; // Message de succès
            $message_type = "success"; // Type du message
            header('location: ../../theme.php?id='.$idUser);
        } catch (PDOException $e) {
            $message = "Erreur lors de l'ajout du thème : " . $e->getMessage(); // Message d'erreur
            $message_type = "error"; // Type du message d'erreur
            header('location: ../../theme.php?id='.$idUser);
        }
    } else {
        $message = "Le nom du thème ne peut pas être vide."; // Message si le champ est vide
        $message_type = "error"; // Type du message d'erreur
        header('location: ../../theme.php?id='.$idUser);
    }
}
?>
