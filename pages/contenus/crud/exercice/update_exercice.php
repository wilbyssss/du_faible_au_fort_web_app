<?php
require_once('../../../../connect_database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idUser = $_GET['id'] ?? null;
    $id = $_POST['id_ex_trou'];
    $libelle = $_POST['libelle'];
    $instruction = $_POST['instruction'];
    $theme_id = $_POST['theme_id'];
    $texte_id = $_POST['texte_id'] ?? null;

    if (!empty($id) && !empty($libelle) && !empty($instruction) && !empty($theme_id) ){
        $database = new Database();
        $conn = $database->getConnection();

        $query = "UPDATE exercice_a_trou 
                  SET libelle_ex = :libelle, instruction_globale = :instruction, id_theme = :theme_id, id_text_training = :texte_id 
                  WHERE id_ex_trou = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':libelle', $libelle);
        $stmt->bindParam(':instruction', $instruction);
        $stmt->bindParam(':theme_id', $theme_id);
        $stmt->bindParam(':texte_id', $texte_id);

        if ($stmt->execute()) {
            header("Location: ../../exercice.php?id=$idUser");
        } else {
            header("Location: ../../exercice.php?id=$idUser");
        }
    } else {
        header("Location: ../../exercice.php?id=$idUser");
    }
}
?>
