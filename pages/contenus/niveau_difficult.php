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
<link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/niveau.css">
<link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/pagination.css">
<div class="content">
    <h2>Liste des Niveaux</h2>

    <button class="btn-new" onclick="toggleForm()">Nouveau</button>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Classes associées</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php

            $limit = 10;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset =( $page - 1 )* $limit;

            $countQuery = "SELECT COUNT(*) as total FROM niveau_difficulte ";
            $countStmt = $conn->prepare($countQuery);
            $countStmt->execute();
            $totalItems = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
            $totalPages = ceil($totalItems / $limit);

            // Récupérer tous les niveaux
            $query = "SELECT * FROM niveau_difficulte LIMIT :limit OFFSET :offset";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $niveaux = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($niveaux as $row) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['nom_niveau']) . "</td>";
                
                // Récupérer les classes associées à ce niveau via la table 'avoir'
                $queryClasses = "SELECT c.id_classe, c.nom_classe 
                                FROM avoir a
                                JOIN classes c ON a.id_classe = c.id_classe
                                WHERE a.id_niveau = :id_niveau
                                ORDER BY c.nom_classe";
                $stmtClasses = $conn->prepare($queryClasses);
                $stmtClasses->bindParam(':id_niveau', $row['id_niveau']);
                $stmtClasses->execute();
                $classes = $stmtClasses->fetchAll(PDO::FETCH_ASSOC);
                
                echo "<td>";
                if (count($classes) > 0) {
                    echo "<ul class='class-list'>";
                    foreach ($classes as $classe) {
                        echo "<li>{$classe['nom_classe']} "
                            . "<button class='btn-remove' onclick='removeAffectation({$row['id_niveau']}, {$classe['id_classe']})'>×</button>"
                            . "</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "Aucune classe associée";
                }
                echo "</td>";
                
                echo "<td>"
                    . "<button class='btn-edit' onclick='editNiveau(" . $row['id_niveau'] . ", \"" . htmlspecialchars($row['nom_niveau']) . "\")'>Modifier</button>"
                    . "<button class='btn-delete' onclick='deleteNiveau(" . $row['id_niveau'] . ",\"". $id ."\")'>Supprimer</button>"
                    . "<button class='btn-affect' onclick='showAffectForm(" . $row['id_niveau'] . ")'>Affecter à une classe</button>"
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



    <!-- Formulaire d'affectation à une classe -->
    <div id="form-affect-container" class="hidden">
        <h2>Affecter le niveau à une classe</h2>
        <form action="<?php echo $baseUrl; ?>pages/contenus/crud/niveau/affect_niveau.php?id=<?php echo $id;?>" method="POST">
            <input type="hidden" id="affect_id_niveau" name="id_niveau">
            <label for="id_classe">Classe :</label>
            <select id="id_classe" name="id_classe" required>
                <option value="">-- Sélectionner une classe --</option>
                <?php
                $queryClasses = "SELECT * FROM classes ORDER BY nom_classe";
                $stmtClasses = $conn->prepare($queryClasses);
                $stmtClasses->execute();
                $classes = $stmtClasses->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($classes as $classe) {
                    echo "<option value='{$classe['id_classe']}'>{$classe['nom_classe']}</option>";
                }
                ?>
            </select>
            <button type="submit" class="btn-save">Enregistrer</button>
            <button type="button" class="btn-cancel" onclick="showAffectForm()">Annuler</button>
        </form>
    </div>
</div>


    <!-- Formulaire d'ajout -->
    <div id="form-container" class="hidden">
        <h2>Ajouter un Niveau</h2>
        <form action="<?php echo $baseUrl; ?>pages/contenus/crud/niveau/ajout_niveau.php?id=<?php echo $id;?>" method="POST">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom_niveau" required>
            <button type="submit" class="btn-save">Enregistrer</button>
            <button type="button" class="btn-cancel" onclick="toggleForm()">Annuler</button>
        </form>
    </div>

    <!-- Formulaire de modification -->
    <div id="form-container-modify" class="hidden">
        <h2>Modifier un Niveau</h2>
        <form action="<?php echo $baseUrl; ?>pages/contenus/crud/niveau/update_niveau.php?id=<?php echo $id;?>" method="POST">
            <input type="hidden" id="id_niveau" name="id_niveau">
            <label for="nom_modif">Nom :</label>
            <input type="text" id="nom_modif" name="nom_niveau" required>
            <button type="submit" class="btn-save">Enregistrer les modifications</button>
            <button type="button" class="btn-cancel" onclick="toggleFormUpdate()">Annuler</button>
        </form>
    </div>
</div>

<script src="<?php echo $baseUrl; ?>assets/js/niveau.js"></script>
