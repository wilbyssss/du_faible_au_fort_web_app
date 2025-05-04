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
    header('Location: ../login.php');
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
                    $stmt = $conn->prepare("INSERT INTO roles_admin (nom_role) VALUES (?)");
                    $stmt->execute([$_POST['nom']]);
                    $_SESSION['message'] = "Rôle ajouté avec succès";
                    $_SESSION['message_type'] = "success";
                    break;
                    
                case 'update':
                    $stmt = $conn->prepare("UPDATE roles_admin SET nom_role = ? WHERE id_role = ?");
                    $stmt->execute([$_POST['nom'], $_POST['id']]);
                    $_SESSION['message'] = "Rôle modifié avec succès";
                    $_SESSION['message_type'] = "success";
                    break;
                    
                case 'delete':
                    // Vérification avant suppression
                    $checkUtilisateurs = $conn->prepare("SELECT COUNT(*) FROM administration WHERE role_admin = ?");
                    $checkUtilisateurs->execute([$_POST['id']]);
                    $countUtilisateurs = $checkUtilisateurs->fetchColumn();
                    
                    if ($countUtilisateurs == 0) {
                        $stmt = $conn->prepare("DELETE FROM roles_admin WHERE id_role = ?");
                        $stmt->execute([$_POST['id']]);
                        $_SESSION['message'] = "Rôle supprimé avec succès";
                        $_SESSION['message_type'] = "success";
                    } else {
                        $_SESSION['message'] = "Ce rôle ne peut pas être supprimé car il est attribué à des utilisateurs";
                        $_SESSION['message_type'] = "error";
                    }
                    break;
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = "Erreur: " . $e->getMessage();
            $_SESSION['message_type'] = "error";
        }
    }
    header("Location: role_admin.php?id=".$id);
    exit;
}

// 5. INCLUSIONS DES COMPOSANTS VISUELS
include('../../includes/header_view.php');
include('../../includes/slider_bar.php');

// 6. RÉCUPÉRATION DES DONNÉES
$roles = $conn->query("SELECT * FROM roles_admin")->fetchAll(PDO::FETCH_ASSOC);
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
                <h3 class="mb-0">Gestion des Rôles</h3>
                <button class="btn btn-light" onclick="toggleForm()">
                    <i class="bi bi-plus-lg"></i> Nouveau rôle
                </button>
            </div>
        </div>

        <!-- Formulaire unique pour ajout/modification -->
        <div id="form-container" class="card mb-4 d-none">
            <div class="card-body">
                <h4 class="card-title" id="form-title">Ajouter un Rôle</h4>
                <form method="POST" id="role-form">
                    <input type="hidden" name="action" id="form-action" value="add">
                    <input type="hidden" name="id" id="id_role">
                    
                    <div class="mb-3">
                        <label class="form-label">Nom du rôle</label>
                        <input type="text" class="form-control" id="nom_role" name="nom" required>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" onclick="toggleForm()">Annuler</button>
                        <button type="submit" class="btn btn-success">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tableau des rôles -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roles as $role): ?>
                        <tr>
                            <td><?= $role['id_role'] ?></td>
                            <td><?= htmlspecialchars($role['nom_role']) ?></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick="editRole(<?= $role['id_role'] ?>, '<?= htmlspecialchars(addslashes($role['nom_role'])) ?>')">
                                        <i class="bi bi-pencil"></i> Modifier
                                    </button>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $role['id_role'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce rôle ?')">
                                            <i class="bi bi-trash"></i> Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
        document.getElementById('form-title').textContent = "Modifier un Rôle";
        document.getElementById('form-action').value = "update";
    } else {
        document.getElementById('form-title').textContent = "Ajouter un Rôle";
        document.getElementById('form-action').value = "add";
        document.getElementById('role-form').reset();
    }
}

function editRole(id, nom) {
    document.getElementById('id_role').value = id;
    document.getElementById('nom_role').value = nom;
    toggleForm(true);
}
</script>