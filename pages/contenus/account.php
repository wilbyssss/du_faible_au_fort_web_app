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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                // Ajouter un rôle
                $stmt = $conn->prepare("INSERT INTO type_compte (nom_type) VALUES (?)");
                $stmt->execute([$_POST['nom_type']]);
                $_SESSION['message'] = "Ajout du compte réussie";
                $_SESSION['message_type'] = "success";
                break;
                
            case 'edit':
                // Modifier un rôle
                $stmt = $conn->prepare("UPDATE type_compte SET nom_type = ? WHERE id_type = ?");
                $stmt->execute([$_POST['nom_type'], $_POST['id_type']]);
                $_SESSION['message'] = "Modification réussie";
                $_SESSION['message_type'] = "success";
                break;
                
            case 'delete':
                // Supprimer un compte (vérifier d'abord s'il n'est pas utilisé)
                $check = $conn->prepare("SELECT COUNT(*) FROM utilisateurs WHERE id_type = ?");
                $check->execute([$_POST['id']]);
                $count = $check->fetchColumn();
                
                if ($count == 0) {
                    $stmt = $conn->prepare("DELETE FROM type_compte WHERE id_type = ?");
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
    header("Location: account.php?id=".$id);
    exit;
}

// Inclusions des composants visuels
include('../../includes/header_view.php');
include('../../includes/slider_bar.php');
?>

<div class="container" style="margin-top:100px; margin-left:17%;">
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
                <h3 class="mb-0">Types de compte</h3>
                <button class="btn btn-light" onclick="toggleForm()">
                    <i class="bi bi-plus-lg"></i> Nouveau
                </button>
            </div>
        </div>

          <!-- Formulaire unique pour ajout/modification -->
        <div id="form-container" class="card mt-3 d-none">
            <div class="card-body">
                <h4 class="card-title" id="form-title">Ajouter un Type de Compte</h4>
                <form method="POST" id="account-form">
                    <input type="hidden" name="action" id="form-action" value="add">
                    <input type="hidden" name="id_type" id="id_type">
                    
                    <div class="mb-3">
                        <label class="form-label">Nom du Type</label>
                        <input type="text" class="form-control" id="nom_type" name="nom_type" required>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" onclick="toggleForm()">Annuler</button>
                        <button type="submit" class="btn btn-success">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tableau des types de comptes -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nom du Type</th>
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
                        $countStmt = $conn->prepare("SELECT COUNT(*) as total FROM type_compte");
                        $countStmt->execute();
                        $totalItems = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
                        $totalPages = ceil($totalItems / $limit);

                        // Récupération des types de compte
                        $query = "SELECT * FROM type_compte LIMIT :limit OFFSET :offset";
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                        $stmt->execute();
                        $types = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($types as $type):
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($type['nom_type']) ?></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick="editAccount(<?= $type['id_type'] ?>, '<?= htmlspecialchars(addslashes($type['nom_type'])) ?>')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $type['id_type'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce type de compte ?')">
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

            <!-- Pagination -->
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
        document.getElementById('form-title').textContent = "Modifier un Type de Compte";
        document.getElementById('form-action').value = "edit";
    } else {
        document.getElementById('form-title').textContent = "Ajouter un Type de Compte";
        document.getElementById('form-action').value = "add";
        document.getElementById('account-form').reset();
    }
}

function editAccount(id, nom) {
    document.getElementById('id_type').value = id;
    document.getElementById('nom_type').value = nom;
    toggleForm(true);
}
</script>