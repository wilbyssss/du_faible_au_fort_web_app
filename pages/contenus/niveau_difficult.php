<?php
// 1. INITIALISATION ET SÉCURITÉ
session_start();
$baseUrl = "/du_faible_au_fort/";
$id = $_GET['id'] ?? null;

// Headers anti-cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// 2. VÉRIFICATION AUTHENTIFICATION
if (!isset($_SESSION['connecte']) || $_SESSION['connecte'] !== true) {
    header('Location: login.php');
    exit;
}

// 3. CONNEXION BASE DE DONNÉES
require_once('../../connect_database.php');
$database = new Database();
$conn = $database->getConnection();

// 4. TRAITEMENT DES ACTIONS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'add':
                    $stmt = $conn->prepare("INSERT INTO niveau_difficulte (nom_niveau) VALUES (?)");
                    $stmt->execute([$_POST['nom_niveau']]);
                    $_SESSION['message'] = "Niveau ajouté avec succès";
                    $_SESSION['message_type'] = "success";
                    break;
                    
                case 'update':
                    $stmt = $conn->prepare("UPDATE niveau_difficulte SET nom_niveau = ? WHERE id_niveau = ?");
                    $stmt->execute([$_POST['nom_niveau'], $_POST['id_niveau']]);
                    $_SESSION['message'] = "Niveau modifié avec succès";
                    $_SESSION['message_type'] = "success";
                    break;
                    
                case 'delete':
                    // Vérification avant suppression
                  /*  $checkExercices = $conn->prepare("SELECT COUNT(*) FROM exercice_a_trou WHERE id_niveau = ?");
                    $checkExercices->execute([$_POST['id']]);
                    $countExercices = $checkExercices->fetchColumn();*/
                    
                    $checkAffectations = $conn->prepare("SELECT COUNT(*) FROM avoir WHERE id_niveau = ?");
                    $checkAffectations->execute([$_POST['id']]);
                    $countAffectations = $checkAffectations->fetchColumn();
                    
                    if ($countAffectations == 0) {
                        $stmt = $conn->prepare("DELETE FROM niveau_difficulte WHERE id_niveau = ?");
                        $stmt->execute([$_POST['id']]);
                        $_SESSION['message'] = "Niveau supprimé avec succès";
                        $_SESSION['message_type'] = "success";
                    } else {
                        $message = "Ce niveau ne peut pas être supprimé car il est utilisé par :";
                        if ($countExercices > 0) $message .= " des exercices,";
                        if ($countAffectations > 0) $message .= " des classes,";
                        $message = rtrim($message, ',');
                        
                        $_SESSION['message'] = $message;
                        $_SESSION['message_type'] = "error";
                    }
                    break;
                    
                case 'affect':
                    // Vérification si l'affectation existe déjà
                    $check = $conn->prepare("SELECT COUNT(*) FROM avoir WHERE id_niveau = ? AND id_classe = ?");
                    $check->execute([$_POST['id_niveau'], $_POST['id_classe']]);
                    
                    if ($check->fetchColumn() == 0) {
                        $stmt = $conn->prepare("INSERT INTO avoir (id_niveau, id_classe) VALUES (?, ?)");
                        $stmt->execute([$_POST['id_niveau'], $_POST['id_classe']]);
                        $_SESSION['message'] = "Affectation réussie";
                        $_SESSION['message_type'] = "success";
                    } else {
                        $_SESSION['message'] = "Cette classe est déjà associée à ce niveau";
                        $_SESSION['message_type'] = "error";
                    }
                    break;
                    
                case 'remove_affect':
                    $stmt = $conn->prepare("DELETE FROM avoir WHERE id_niveau = ? AND id_classe = ?");
                    $stmt->execute([$_POST['id_niveau'], $_POST['id_classe']]);
                    $_SESSION['message'] = "Affectation supprimée";
                    $_SESSION['message_type'] = "success";
                    break;
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = "Erreur: " . $e->getMessage();
            $_SESSION['message_type'] = "error";
        }
    }
    header("Location: niveau_difficult.php?id=".$id);
    exit;
}

// 5. INCLUSIONS DES COMPOSANTS VISUELS
include('../../includes/header_view.php');
include('../../includes/slider_bar.php');
?>

<div class="container" style="margin-top:100px; margin-left:20%; width: 80%;">
    <!-- Affichage des messages -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message_type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Liste des Niveaux</h2>
                <button class="btn btn-primary" onclick="toggleForm()">
                    <i class="bi bi-plus-circle"></i> Nouveau
                </button>
            </div>

            <!-- Formulaire d'ajout/modification -->
            <div id="form-container" class="card mb-4 d-none">
                <div class="card-body">
                    <h4 class="card-title" id="form-title">Ajouter un Niveau</h4>
                    <form method="POST" id="niveau-form">
                        <input type="hidden" name="action" id="form-action" value="add">
                        <input type="hidden" name="id_niveau" id="id_niveau">
                        
                        <div class="mb-3">
                            <label class="form-label">Nom du niveau</label>
                            <input type="text" class="form-control" id="nom_niveau" name="nom_niveau" required>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" onclick="toggleForm()">Annuler</button>
                            <button type="submit" class="btn btn-success">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Formulaire d'affectation -->
            <div id="form-affect-container" class="card mb-4 d-none">
                <div class="card-body">
                    <h4 class="card-title">Affecter à une classe</h4>
                    <form method="POST" id="affect-form">
                        <input type="hidden" name="action" value="affect">
                        <input type="hidden" name="id_niveau" id="affect_id_niveau">
                        
                        <div class="mb-3">
                            <label class="form-label">Classe</label>
                            <select class="form-select" id="id_classe" name="id_classe" required>
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
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" onclick="toggleAffectForm()">Annuler</button>
                            <button type="submit" class="btn btn-success">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tableau -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom</th>
                                    <th>Classes associées</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $limit = 10;
                                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                                $offset = ($page - 1) * $limit;

                                // Comptage total
                                $countStmt = $conn->prepare("SELECT COUNT(*) as total FROM niveau_difficulte");
                                $countStmt->execute();
                                $totalItems = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
                                $totalPages = ceil($totalItems / $limit);

                                // Récupération des niveaux
                                $query = "SELECT * FROM niveau_difficulte LIMIT :limit OFFSET :offset";
                                $stmt = $conn->prepare($query);
                                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                                $stmt->execute();
                                $niveaux = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($niveaux as $row):
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['nom_niveau']) ?></td>
                                    <td>
                                        <?php
                                        // Paramètres de pagination
                                        $classesParPage = 2;
                                        $page = isset($_GET['page_' . $row['id_niveau']]) ? (int)$_GET['page_' . $row['id_niveau']] : 1;
                                        $offset = ($page - 1) * $classesParPage;

                                        // Total des classes pour ce niveau
                                        $queryTotal = "SELECT COUNT(*) FROM avoir WHERE id_niveau = :id_niveau";
                                        $stmtTotal = $conn->prepare($queryTotal);
                                        $stmtTotal->bindParam(':id_niveau', $row['id_niveau']);
                                        $stmtTotal->execute();
                                        $totalClasses = $stmtTotal->fetchColumn();
                                        $nbPages = ceil($totalClasses / $classesParPage);

                                        $queryClasses = "SELECT c.id_classe, c.nom_classe 
                                                            FROM avoir a
                                                            JOIN classes c ON a.id_classe = c.id_classe
                                                            WHERE a.id_niveau = :id_niveau
                                                            ORDER BY c.nom_classe
                                                            LIMIT :limit OFFSET :offset";

                                        $stmtClasses = $conn->prepare($queryClasses);
                                        $stmtClasses->bindParam(':id_niveau', $row['id_niveau']);
                                        $stmtClasses->bindParam(':limit', $classesParPage, PDO::PARAM_INT);
                                        $stmtClasses->bindParam(':offset', $offset, PDO::PARAM_INT);
                                        $stmtClasses->execute();
                                        $classes = $stmtClasses->fetchAll(PDO::FETCH_ASSOC);


                                        
                                        if (count($classes) > 0):
                                        ?>
                                        <ul class="list-group list-group-flush">
                                            <?php foreach ($classes as $classe): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <?= htmlspecialchars($classe['nom_classe']) ?>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="action" value="remove_affect">
                                                    <input type="hidden" name="id_niveau" value="<?= $row['id_niveau'] ?>">
                                                    <input type="hidden" name="id_classe" value="<?= $classe['id_classe'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            onclick="return confirm('Supprimer cette affectation ?')">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                </form>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                         <nav aria-label="Page navigation" class="mt-4">
                                            <ul class="pagination justify-content-center">
                                                <?php for ($i = 1; $i <= $nbPages; $i++): ?>
                                                    <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                                                        <a class="page-link" href="?page_<?= $row['id_niveau'] ?>=<?= $i ?>&id=<?= $id ?>">
                                                            <?= $i ?>
                                                        </a>
                                                    </li>
                                                <?php endfor; ?>
                                            </ul>
                                        </nav>
                                        <?php else: ?>
                                        <span class="text-muted">Aucune classe associée</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="editNiveau(<?= $row['id_niveau'] ?>, '<?= htmlspecialchars(addslashes($row['nom_niveau'])) ?>')">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $row['id_niveau'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce niveau ?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                            
                                            <button class="btn btn-sm btn-outline-secondary" onclick="showAffectForm(<?= $row['id_niveau'] ?>)">
                                                <i class="bi bi-link-45deg"></i> 
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>&id=<?= $id ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&id=<?= $id ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>&id=<?= $id ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Gestion des formulaires
function toggleForm(editing = false) {
    const form = document.getElementById('form-container');
    form.classList.toggle('d-none');
    
    if (editing) {
        document.getElementById('form-title').textContent = "Modifier un Niveau";
        document.getElementById('form-action').value = "update";
    } else {
        document.getElementById('form-title').textContent = "Ajouter un Niveau";
        document.getElementById('form-action').value = "add";
        document.getElementById('niveau-form').reset();
    }
}

function toggleAffectForm() {
    const form = document.getElementById('form-affect-container');
    form.classList.toggle('d-none');
}

function editNiveau(id, nom) {
    document.getElementById('id_niveau').value = id;
    document.getElementById('nom_niveau').value = nom;
    toggleForm(true);
}

function showAffectForm(idNiveau) {
    document.getElementById('affect_id_niveau').value = idNiveau;
    toggleAffectForm();
}
</script>