<?php
session_start();
$baseUrl = "/du_faible_au_fort/";
$id = $_GET['id'] ?? null;

// Sécurité et headers
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Vérification authentification
if (!isset($_SESSION['connecte']) || $_SESSION['connecte'] !== true) {
    header('Location: login.php');
    exit;
}

// Connexion base de données
require_once('../../connect_database.php');
$database = new Database();
$conn = $database->getConnection();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['action'])){
        switch ($_POST['action']) {
            case 'add':
                //Insertion Dune classe
                $stmt = $conn->prepare('INSERT INTO classes(nom_classe) Values(?)');
                $stmt->execute([$_POST['nom_classe']]);
                $_SESSION['message'] = "Ajout de la classe réussie";
                $_SESSION['message_type'] = 'success';
                break;
            
            case 'edit':
                 //Mise à jor Dune classe
                 $stmt = $conn->prepare('UPDATE classes SET nom_classe = ? WHERE id_classe = ?');
                 $stmt->execute([$_POST['nom_classe'], $_POST['id_classe']]);
                 $_SESSION['message'] = "Modification de la classe réussie";
                 $_SESSION['message_type'] = 'success';
                break;
            case 'delete':
                // Supprimer une classe (vérifier d'abord s'il n'est pas utilisé)
                $check = $conn->prepare("SELECT COUNT(*) FROM utilisateurs WHERE id_classe = ?");
                $check->execute([$_POST['id']]);
                $count = $check->fetchColumn();
                
                if ($count == 0) {
                    $stmt = $conn->prepare("DELETE FROM classes WHERE id_classe = ?");
                    $stmt->execute([$_POST['id']]);
                    $_SESSION['message'] = "Suppression réussie";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Ce Compte est utilisé par des utilisateurs et ne peut pas être supprimé.";
                    $_SESSION['message_type'] = "error";
                }
                break;

        }
    }
}

// Inclusions des composants visuels
include('../../includes/header_view.php');
include('../../includes/slider_bar.php');
?>

<div class="container" style="margin-top:100px; margin-left:20%; width: 80%;height:auto; overflow:hidden;">

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

<div class="card shadow-sm">
            <!-- En-tête -->
            <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Classes</h3>
                <button class="btn btn-light" onclick="toggleForm()">
                    <i class="bi bi-plus-lg"></i> Nouveau
                </button>
            </div>
        </div>

         <!-- Formulaire unique pour ajout/modification -->
    <div id="form-container" class="card mt-3 d-none">
        <div class="card-body">
            <h4 class="card-title" id="form-title">Ajouter une Classe</h4>
            <form method="POST" id="classe-form">
                <input type="hidden" name="action" id="form-action" value="add">
                <input type="hidden" name="id_classe" id="id_classe">
                
                <div class="mb-3">
                    <label class="form-label">Nom de la classe</label>
                    <input type="text" class="form-control" id="nom_classe" name="nom_classe" required>
                </div>
                
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary" onclick="toggleForm()">Annuler</button>
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des classes -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-light">
                <tr>
                    <th>Nom de la classe</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Configuration de la pagination
                $limit = 10;
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $offset = ($page - 1) * $limit;

                // Comptage total
                $countStmt = $conn->prepare("SELECT COUNT(*) as total FROM classes");
                $countStmt->execute();
                $totalItems = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
                $totalPages = ceil($totalItems / $limit);

                // Récupération des classes
                $query = "SELECT * FROM classes LIMIT :limit OFFSET :offset";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                $stmt->execute();
                $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($classes as $classe):
                ?>
                <tr>
                    <td><?= htmlspecialchars($classe['nom_classe']) ?></td>
                    <td class="text-end">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary" 
                                    onclick="editClasse(<?= $classe['id_classe'] ?>, '<?= htmlspecialchars(addslashes($classe['nom_classe'])) ?>')">
                                    <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $classe['id_classe'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette classe ?')">
                                        <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <nav class="mt-3">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page-1 ?>&id=<?= $id ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&id=<?= $id ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page+1 ?>&id=<?= $id ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
                    </div>
</div>

<script>
// Gestion des formulaires
function toggleForm(editing = false) {
    const form = document.getElementById('form-container');
    form.classList.toggle('d-none');
    
    if (editing) {
        document.getElementById('form-title').textContent = "Modifier une Classe";
        document.getElementById('form-action').value = "edit";
    } else {
        document.getElementById('form-title').textContent = "Ajouter une Classe";
        document.getElementById('form-action').value = "add";
        document.getElementById('classe-form').reset();
    }
}

function editClasse(id, nom) {
    document.getElementById('id_classe').value = id;
    document.getElementById('nom_classe').value = nom;
    toggleForm(true);
}
</script>