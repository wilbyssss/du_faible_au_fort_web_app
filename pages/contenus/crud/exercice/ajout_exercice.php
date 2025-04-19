<?php
require_once('../../../../connect_database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_GET['id'] ?? null;
    $libelle = $_POST['libelle'];
    $instruction = $_POST['instruction'];
    $theme_id = $_POST['theme_id'];
    $texte_id = $_POST['texte_id'];

    if (!empty($libelle) && !empty($instruction) && !empty($theme_id)) {
        $database = new Database();
        $conn = $database->getConnection();
        $conn->exec("SET FOREIGN_KEY_CHECKS = 0");

        $query = "INSERT INTO exercice_a_trou (libelle_ex, instruction_globale, id_theme, id_text_training) 
                  VALUES (:libelle, :instruction, :theme_id, :texte_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':libelle', $libelle);
        $stmt->bindParam(':instruction', $instruction);
        $stmt->bindParam(':theme_id', $theme_id);
        $stmt->bindParam(':texte_id', $texte_id);
        $stmt->execute();
        $conn->exec("SET FOREIGN_KEY_CHECKS = 1");

    } else {
    
    }
    header('Location: ../../exercice.php?id='.$id);
    exit();
}
?>
