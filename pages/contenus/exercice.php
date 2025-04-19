<?php
session_start();
$baseUrl = "/du_faible_au_fort/";
$id = $_GET['id'] ?? null;
require_once('../../connect_database.php');
include('../../includes/header_view.php');
include('../../includes/slider_bar.php');
// Empêcher la mise en cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Vérifier la connexion
if (!isset($_SESSION['connecte']) || $_SESSION['connecte'] !== true) {
    header('Location: login.php');
    exit;
}

$database = new Database();
$conn = $database->getConnection();

$message = $_SESSION['message'] ?? null;
$message_type = $_SESSION['message_type'] ?? null;

if (isset($message)) {
    echo $message_type == "success" 
        ? "<div class='alert success'>$message</div>" 
        : "<div class='alert error'>$message</div>";
    
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>
<link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/exercice.css">
<link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/pagination.css">
<div class="content">
    <h2>Liste des Exercices</h2>
    <button class="btn-new" onclick="toggleForm()">Nouveau</button>

    <table>
        <thead>
            <tr>
                <th>Libellé</th>
                <th>Instruction Globale</th>
                <th>Theme Associé</th>
                <th>Texte d'entrainement</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
             $limit = 10;
             $page = isset($_GET['page']) ? int($_GET['page']) : 1;
             $offset =( $page - 1 )* $limit;
 
             $countQuery = "SELECT COUNT(*) as total FROM exercice_a_trou";
             $countStmt = $conn->prepare($countQuery);
             $countStmt->execute();
             $totalItems = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
             $totalPages = ceil($totalItems / $limit);

            $query = "SELECT e.*, t.nom_theme, te.titre_text 
                     FROM exercice_a_trou AS e 
                     INNER JOIN themes AS t ON e.id_theme = t.id_theme 
                     LEFT JOIN text_training AS te ON e.id_text_training = te.id_text_training LIMIT :limit OFFSET :offset";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $exercices = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($exercices as $exercice) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($exercice['libelle_ex']) . "</td>";
                echo "<td>" . htmlspecialchars($exercice['instruction_globale']) . "</td>";
                echo "<td>" . htmlspecialchars($exercice['nom_theme']) . "</td>";
                echo "<td>" . htmlspecialchars($exercice['titre_text']?? 'Pas de texte d\'entainement') . "</td>";
                echo "<td>"
                    . "<button class='btn-edit' onclick='editExercice(".$exercice['id_ex_trou'].",\"".htmlspecialchars($exercice['libelle_ex'])."\",\"".htmlspecialchars($exercice['instruction_globale'])."\",".$exercice['id_theme'].",".$exercice['id_text_training'].")'>Modifier</button>"
                    . "<button class='btn-delete' onclick='deleteExercice(".$exercice['id_ex_trou'].",\"". $id ."\")'>Supprimer</button>"
                    . "</td>";            
            }
            ?>
        </tbody>
    </table>

    <div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>">Précédent</a>
    <?php endif; ?>
    
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>" <?= $i === $page ? 'class="active"' : '' ?>><?= $i ?></a>
    <?php endfor; ?>
    
    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?>">Suivant</a>
    <?php endif; ?>
    </div>

    <!-- Formulaire d'ajout -->
    <div id="form-container" class="hidden">
        <h2>Ajouter un Exercice</h2>
        <form action="<?php echo $baseUrl; ?>pages/contenus/crud/exercice/ajout_exercice.php?id=<?php echo $id;?>" method="POST">
            <label for="libelle">Libellé :</label>
            <input type="text" id="libelle" name="libelle" required>

            <label for="instruction">Instruction Globale :</label>
            <textarea id="instruction" name="instruction" required></textarea>

            <label for="theme_id">Thème :</label>
            <select id="theme_id" name="theme_id" required>
                <option value="">-- Sélectionnez un thème --</option>
                <?php
                $query = "SELECT * FROM themes";
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $themes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($themes as $theme) {
                    echo "<option value='" . $theme['id_theme'] . "'>" . htmlspecialchars($theme['nom_theme']) . "</option>";
                }
                ?>
            </select>

            <label for="texte_id">Texte d'entraînement :</label>
            <select id="texte_id" name="texte_id">
                <option value="">-- Sélectionnez un texte --</option>
                <?php
                $query = "SELECT * FROM text_training";
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $textes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($textes as $texte) {
                    echo "<option value='" . $texte['id_text_training'] . "'>" . htmlspecialchars($texte['titre_text']) . "</option>";
                }
                ?>
            </select>

            <button type="submit" class="btn-save">Enregistrer</button>
            <button type="button" class="btn-cancel" onclick="toggleForm()">Annuler</button>
        </form>
    </div>

    <!-- Formulaire de modification -->
    <div id="form-container-modify" class="hidden">
        <h2>Modifier un Exercice</h2>
        <form action="<?php echo $baseUrl; ?>pages/contenus/crud/exercice/update_exercice.php?id=<?php echo $id;?>" method="POST">
            <input type="hidden" id="id_exercice" name="id_ex_trou">

            <label for="nom_exercice">Libellé :</label>
            <input type="text" id="nom_exercice" name="libelle" required>

            <label for="instruction_exercice">Instruction Globale :</label>
            <textarea id="instruction_exercice" name="instruction" required></textarea>

            <label for="theme_id_modify">Thème :</label>
            <select id="theme_id_modify" name="theme_id" required>
                <option value="">-- Sélectionnez un thème --</option>
                <?php
                $query = "SELECT * FROM themes";
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $themes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($themes as $theme) {
                    echo "<option value='" . $theme['id_theme'] . "'>" . htmlspecialchars($theme['nom_theme']) . "</option>";
                }
                ?>
            </select>

            <label for="texte_id_modify">Texte d'entraînement :</label>
            <select id="texte_id_modify" name="texte_id" >
                <option value="">-- Sélectionnez un texte --</option>
                <?php
                $query = "SELECT * FROM text_training";
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $textes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($textes as $texte) {
                    echo "<option value='" . $texte['id_text_training'] . "'>" . htmlspecialchars($texte['titre_text']) . "</option>";
                }
                ?>
            </select>

            <button type="submit" class="btn-save">Enregistrer</button>
            <button type="button" class="btn-cancel" onclick="toggleFormUpdate()">Annuler</button>
        </form>
    </div>
</div>


<script src="<?php echo $baseUrl; ?>assets/js/exercice.js"></script>