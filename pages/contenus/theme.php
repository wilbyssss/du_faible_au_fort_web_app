<?php
session_start();
$id = $_GET['id'] ?? null;
$baseUrl = "/du_faible_au_fort/";
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


if (isset($message)) {
 if ($message_type == "success") {
     echo "<div class='alert success'>$message</div>";
 } else {
     echo "<div class='alert error'>$message</div>";
 }
}

?>
<link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/themes.css">
<link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/pagination.css">
<div class="content">
    <h2>Liste des Thèmes</h2>
    <button class="btn-new" onclick="toggleForm()">Nouveau</button>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
             $limit = 10;
             $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
             $offset =( $page - 1 )* $limit;
 
             $countQuery = "SELECT COUNT(*) as total FROM themes";
             $countStmt = $conn->prepare($countQuery);
             $countStmt->execute();
             $totalItems = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
             $totalPages = ceil($totalItems / $limit);

            $query = "SELECT * FROM themes LIMIT :limit OFFSET :offset";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $themes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($themes as $row) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['nom_theme']) . "</td>";
                echo "<td>"
                    . "<button class='btn-edit' onclick='editTheme(" . $row['id_theme'] . ", \"" . htmlspecialchars($row['nom_theme']) . "\")'>Modifier</button>"
                    . "<button class='btn-delete' onclick='deleteTheme(" . $row['id_theme'] . ",\"". $id ."\")'>Supprimer</button>"
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

    <!-- Formulaire d'ajout caché initialement -->
    <div id="form-container" class="hidden">
        <h2>Ajouter un Thème</h2>
        <form action="<?php echo $baseUrl; ?>pages/contenus/crud/theme/ajout_theme.php?id=<?php echo $id;?>" method="POST">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom_theme" required>
            <button type="submit" class="btn-save">Enregistrer</button>
            <button type="button" class="btn-cancel" onclick="toggleForm()">Annuler</button>
        </form>
    </div>

    <div id="form-container-modify" class="hidden">
        <h2>Modifier un Thème</h2>
        <form action="<?php echo $baseUrl; ?>pages/contenus/crud/theme/update_theme.php?id=<?php echo $id;?>" method="POST">
             <input type="hidden" id="id_theme" name="id_theme">
            <label for="nom">Nom :</label>
            <input type="text" id="nom_theme" name="nom_theme" required>
            <button type="submit" class="btn-save">Enregistrer</button>
            <button type="button" class="btn-cancel" onclick="toggleFormUpdate()">Annuler</button>
        </form>
    </div>

    
</div>

<script src="<?php echo $baseUrl; ?>assets/js/themes.js"></script>
