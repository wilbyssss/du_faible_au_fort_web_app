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

?>
<link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/phrase.css">
<link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/pagination.css">
<div class="content">
    <h2>Liste des Phrases à trou</h2>
    <button class="btn-new" onclick="toggleForm()">Nouveau</button>

    <table>
        <thead>
            <tr>
                <th>Phrase (avec _ pour le trou)</th>
                <th>Indication</th>
                <th>Réponse</th>
                <th>Exercice Associé</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $limit = 10;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset =( $page - 1 )* $limit;

            $countQuery = "SELECT COUNT(*) as total FROM phrase_a_trou";
            $countStmt = $conn->prepare($countQuery);
            $countStmt->execute();
            $totalItems = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
            $totalPages = ceil($totalItems / $limit);

            $query = "SELECT p.*, e.libelle_ex 
          FROM phrase_a_trou AS p 
          INNER JOIN exercice_a_trou AS e ON p.id_ex_trou = e.id_ex_trou
          LIMIT :limit OFFSET :offset";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $phrases = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($phrases as $phrase) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($phrase['libelle_phrase_a_trou']) . "</td>";
                echo "<td>" . htmlspecialchars($phrase['indication_phr']) . "</td>";
                echo "<td>" . htmlspecialchars($phrase['reponse_correspondante']) . "</td>";
                echo "<td>" . htmlspecialchars($phrase['libelle_ex']) . "</td>";
                echo "<td>"
                    . "<button class='btn-edit' onclick='editPhrase(".$phrase['id_phrase_a_trou'].",\"".htmlspecialchars($phrase['libelle_phrase_a_trou'])."\",\"".htmlspecialchars($phrase['indication_phr'])."\",\"".htmlspecialchars($phrase['reponse_correspondante'])."\",".$phrase['id_ex_trou'].")'>Modifier</button>"
                    . "<button class='btn-delete' onclick='deletePhrase(".$phrase['id_phrase_a_trou'].",\"". $id ."\")'>Supprimer</button>"
                    . "</td>";
                echo "</tr>";
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
        <h2>Ajouter une Phrase à trou</h2>
        <form action="<?php echo $baseUrl; ?>pages/contenus/crud/phrase/ajout_phrase.php?id=<?php echo $id;?>" method="POST">
            <label for="libelle_phrase">Phrase (utilisez _ pour indiquer le trou) :</label>
            <input type="text" id="libelle_phrase" name="libelle_phrase" placeholder="Ex: Le chat _ sur le tapis ou il fait _ ce _." style="height:90px">
            
            <label for="indication">Indication :</label>
            <input type="text" id="indication" name="indication" placeholder="Que fait le chat?(si il n'y'a aucune indication laisser vide)" required>
            
            <label for="reponse">Réponse :</label>
            <input type="text" id="reponse" name="reponse" placeholder="dort(au cas où vous aurez plusieurs reponses séparés ces dernières par une virgule)" required style="height:90px">
            
            <label for="exercice_id">Exercice associé :</label>
            <select id="exercice_id" name="exercice_id" required>
                <option value="">-- Sélectionnez un exercice --</option>
                <?php
                $query = "SELECT * FROM exercice_a_trou";
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $phrases = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($phrases as $phrase ) {
                    echo "<option value='" . $phrase['id_ex_trou'] . "'>" . htmlspecialchars($phrase['libelle_ex']) . "</option>";
                }
                ?>
            </select>

            <button type="submit" class="btn-save">Enregistrer</button>
            <button type="button" class="btn-cancel" onclick="toggleForm()">Annuler</button>
        </form>
    </div>

    <!-- Formulaire de modification -->
    <div id="form-container-modify" class="hidden">
        <h2>Modifier une Phrase à trou</h2>
        <form action="<?php echo $baseUrl; ?>pages/contenus/crud/phrase/update_phrase.php?id=<?php echo $id;?>" method="POST">
            <input type="hidden" id="id_phrase_modify" name="id_phrase_a_trou">
            
            <label for="libelle_phrase_modify">Phrase :</label>
            <input type="text" id="libelle_phrase_modify" name="libelle_phrase" placeholder="Utilisez _ pour le trou" required style="height:90px">
            
            <label for="indication_modify">Indication :</label>
            <input type="text" id="indication_modify" name="indication">
            
            <label for="reponse_modify">Réponse :</label>
            <input type="text" id="reponse_modify" name="reponse" required style="height:90px">
            
            <label for="exercice_id_modify">Exercice associé :</label>
            <select id="exercice_id_modify" name="exercice_id" required>
                <option value="">-- Sélectionnez un exercice --</option>
                <?php
                $query = "SELECT * FROM exercice_a_trou";
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $exercices = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($phrases as $phrase) {
                    echo "<option value='" . $phrase['id_ex_trou'] . "'>" . htmlspecialchars($phrase['libelle_ex']) . "</option>";
                }
                ?>
            </select>

            <button type="submit" class="btn-save">Enregistrer</button>
            <button type="button" class="btn-cancel" onclick="toggleFormUpdate()">Annuler</button>
        </form>
    </div>
</div>


<script src="<?php echo $baseUrl; ?>assets/js/phrase.js"></script>
