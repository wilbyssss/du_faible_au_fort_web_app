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
    header('Location: ../login.php');
    exit;
}

$database = new Database();
$conn = $database->getConnection();

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                // Ajouter un rôle
                $stmt = $conn->prepare("INSERT INTO roles_admin (nom_role) VALUES (?, ?)");
                $stmt->execute([$_POST['nom']]);
                break;
                
            case 'edit':
                // Modifier un rôle
                $stmt = $conn->prepare("UPDATE roles_admin SET nom_role = ? WHERE id_role = ?");
                $stmt->execute([$_POST['nom'], $_POST['id']]);
                break;
                
            case 'delete':
                // Supprimer un rôle (vérifier d'abord s'il n'est pas utilisé)
                $check = $conn->prepare("SELECT COUNT(*) FROM administration WHERE id_role = ?");
                $check->execute([$_POST['id']]);
                $count = $check->fetchColumn();
                
                if ($count == 0) {
                    $stmt = $conn->prepare("DELETE FROM role_admin WHERE id_role = ?");
                    $stmt->execute([$_POST['id']]);
                } else {
                    $_SESSION['error'] = "Ce rôle est utilisé par des utilisateurs et ne peut pas être supprimé.";
                }
                break;
        }
    }
    // Rediriger pour éviter la soumission multiple
    header("Location: role_admin.php?id=".$id);
    exit;
}

// Récupérer les rôles
$roles = $conn->query("SELECT * FROM roles_admin")->fetchAll(PDO::FETCH_ASSOC);
?>
    <style>
        .action-btn {
            margin: 0 3px;
            padding: 5px 10px;
        }
        .form-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none;
        }
        .form-container.active {
            display: block;
        }
        .alert {
            margin-top: 20px;
        }
    </style>

    <div class="container" style="margin-top:100px";>
        <h2>Gestion des rôles</h2>
        
        <!-- Affichage des erreurs -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <!-- Boutons d'action -->
        <div class="mb-3">
            <button class="btn btn-primary" onclick="showForm('add')">
                <i class="bi bi-plus-circle"></i> Ajouter un rôle
            </button>
        </div>
        
        <!-- Formulaire d'ajout -->
        <div id="add-form" class="form-container">
            <h4>Ajouter un rôle</h4>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nom du rôle</label>
                        <input type="text" name="nom" class="form-control" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-success">Enregistrer</button>
                <button type="button" class="btn btn-secondary" onclick="hideForms()">Annuler</button>
            </form>
        </div>
        
        <!-- Formulaire de modification -->
        <div id="edit-form" class="form-container">
            <h4>Modifier le rôle</h4>
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit-id">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nom du rôle</label>
                        <input type="text" name="nom" id="edit-nom" class="form-control" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-success">Enregistrer</button>
                <button type="button" class="btn btn-secondary" onclick="hideForms()">Annuler</button>
            </form>
        </div>
        
        <!-- Tableau des rôles -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roles as $role): ?>
                    <tr>
                        <td><?= $role['id_role'] ?></td>
                        <td><?= htmlspecialchars($role['nom_role']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning action-btn" 
                                    onclick="showEditForm(<?= $role['id_role'] ?>, '<?= htmlspecialchars($role['nom_role']) ?>')">
                                <i class="bi bi-pencil"></i> Modifier
                            </button>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $role['id_role'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger action-btn" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce rôle?')">
                                    <i class="bi bi-trash"></i> Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showForm(formType) {
            hideForms();
            document.getElementById(formType + '-form').classList.add('active');
        }
        
        function hideForms() {
            document.querySelectorAll('.form-container').forEach(form => {
                form.classList.remove('active');
            });
        }
        
        function showEditForm(id, nom) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-nom').value = nom;
            showForm('edit');
        }
    </script>
