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

// Initialisation des variables de message
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
<link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/classe.css">
<link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/pagination.css">
<div class="content">
    <h2>Liste des Classes</h2>
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
 
             $countQuery = "SELECT COUNT(*) as total FROM classes ";
             $countStmt = $conn->prepare($countQuery);
             $countStmt->execute();
             $totalItems = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
             $totalPages = ceil($totalItems / $limit);
 

            $query = "SELECT * FROM classes LIMIT :limit OFFSET :offset";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($classes as $classe) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($classe['nom_classe']) . "</td>";
                echo "<td>"
                    . "<button class='btn-edit' onclick='editClasse(".$classe['id_classe'].",\"".htmlspecialchars($classe['nom_classe'])."\")'>Modifier</button>"
                    . "<button class='btn-delete' onclick='deleteClasse(".$classe['id_classe'].",\"". $id ."\")'>Supprimer</button>"
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
        <h2>Ajouter une Classe</h2>
        <form action="<?php echo $baseUrl; ?>pages/contenus/crud/classe/ajout_classe.php?id=<?php echo $id;?>" method="POST">
            <label for="nom_classe">Nom :</label>
            <input type="text" id="nom_classe" name="nom_classe" required>
            <button type="submit" class="btn-save">Enregistrer</button>
            <button type="button" class="btn-cancel" onclick="toggleForm()">Annuler</button>
        </form>
    </div>

    <!-- Formulaire de modification -->
    <div id="form-container-modify" class="hidden">
        <h2>Modifier une Classe</h2>
        <form action="<?php echo $baseUrl; ?>pages/contenus/crud/classe/update_classe.php?id=<?php echo $id;?>" method="POST">
            <input type="hidden" id="id_classe_modify" name="id_classe">
            <label for="nom_classe_modify">Nom :</label>
            <input type="text" id="nom_classe_modify" name="nom_classe" required>
            <button type="submit" class="btn-save">Enregistrer</button>
            <button type="button" class="btn-cancel" onclick="toggleFormUpdate()">Annuler</button>
        </form>
    </div>
</div>

<script src="<?php echo $baseUrl; ?>assets/js/classe.js"></script>