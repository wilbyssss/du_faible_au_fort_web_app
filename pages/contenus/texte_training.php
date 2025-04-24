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
 <meta charset="UTF-8">
<link rel="stylesheet" href="<?php echo $baseUrl;?>assets/css/texte_training.css">
<link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/pagination.css">
<div class="content">
 <h2>Liste des Textes d'entrainement</h2>
 <a href="#" class="btn-new" onclick="toggleForm()">Nouveau</a>

 <table>
  <thead>
   <tr>
    <th>Titre</th>
    <th>Contenu</th>
    <th>Visibilité</th>
    <th>Actions</th>
   </tr>
  </thead>
  <tbody>
   <?php

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset =( $page - 1 )* $limit;

$countQuery = "SELECT COUNT(*) as total FROM text_training ";
$countStmt = $conn->prepare($countQuery);
$countStmt->execute();
$totalItems = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalItems / $limit);


            $query = "SELECT * FROM text_training LIMIT :limit OFFSET :offset";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($result as $row) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['titre_text']) . "</td>";
                echo "<td>" . htmlspecialchars($row['contenu_text_training']) . "</td>";
                echo "<td>";
                echo "<select class='visibility-select' data-id='" . $row['id_text_training'] . "' onchange='updateVisibility(this)'>";
                echo "<option value='1'" . ($row['visibility'] == 1 ? " selected" : "") . ">Oui</option>";
                echo "<option value='0'" . ($row['visibility'] == 0 ? " selected" : "") . ">Non</option>";
                echo "</select>";
                echo "</td>";
                echo "<td>"
                . "<a href='#' class='btn-link' onclick='editText("
                . htmlspecialchars($row['id_text_training']) . ", "
                . htmlspecialchars(json_encode($row['titre_text']), ENT_QUOTES, 'UTF-8') . ", "
                . htmlspecialchars(json_encode($row['contenu_text_training']), ENT_QUOTES, 'UTF-8')
                . ")'>Modifier</a> "
                . "<a href='#' class='btn-link delete' onclick='deleteText("
                . htmlspecialchars($row['id_text_training']) . ", "
                . json_encode($id) . ")'>Supprimer</a>"
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

 <!-- Formulaire d'ajout caché initialement -->
 <div id="form-container" class="hidden">
  <h2>Ajouter un Texte d'entrainiement</h2>
  <form action="<?php echo $baseUrl;?>pages/contenus/crud/texte/ajout_texte.php?id=<?php echo $id;?>" method="POST">
   <label for="nom">Titre :</label>
   <input type="text" id="nom" name="titre_texte" required>
   <label for="instruction">Contenu :</label>
   <textarea id="instruction" name="contenu" required style="height:200px"></textarea>
   <button type="submit" class="btn-save">Enregistrer</button>
   <button class="btn-cancel" onclick="toggleForm()">Annuler</button>
  </form>
 </div>

 <div id="form-container-modify" class="hidden">
  <h2>Modifier un Texte d'entrainiement</h2>
  <form action="<?php echo $baseUrl;?>pages/contenus/crud/texte/update_texte.php?id=<?php echo $id;?>" method="POST">
   <input type="hidden" name="id_text" id="id-text">
   <label for="nom">Titre :</label>
   <input type="text" id="nom-text-modify" name="titre_texte" required>
   <label for="contenu">Contenu :</label>
   <textarea id="contenu-text-modify" name="contenu" required style="height:200px"></textarea>
   <button type="submit" class="btn-save">Enregistrer</button>
   <button class="btn-cancel" onclick="toggleFormUpdate()">Annuler</button>
  </form>
 </div>
</div>

<script src="<?php echo $baseUrl;?>assets/js/text_training.js"></script>
<script>
function updateVisibility(select) {
    const textId = select.getAttribute('data-id');
    const visibility = select.value;
    
    // Désactivez le select pendant le traitement
    select.disabled = true;
    
    fetch('<?php echo $baseUrl;?>pages/contenus/crud/texte/update_visibility.php?id=<?php echo $id;?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${textId}&visibility=${visibility}`
    })
    .then(response => {
        // Peu importe la réponse, on recharge la page
        window.location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
        select.disabled = false;
        alert('Erreur lors de la mise à jour');
    });
}
</script>