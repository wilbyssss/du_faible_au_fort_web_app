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
                    $stmt = $conn->prepare("INSERT INTO themes (nom_theme) VALUES (?)");
                    $stmt->execute([$_POST['nom_theme']]);
                    $_SESSION['message'] = "Thème ajouté avec succès";
                    $_SESSION['message_type'] = "success";
                    break;
                    
                case 'update':
                    $stmt = $conn->prepare("UPDATE themes SET nom_theme = ? WHERE id_theme = ?");
                    $stmt->execute([$_POST['nom_theme'], $_POST['id_theme']]);
                    $_SESSION['message'] = "Thème modifié avec succès";
                    $_SESSION['message_type'] = "success";
                    break;
                    
                case 'delete':
                    // Vérification avant suppression
                    $checkExercices = $conn->prepare("SELECT COUNT(*) FROM exercice_a_trou WHERE id_theme = ?");
                    $checkExercices->execute([$_POST['id']]);
                    $countExercices = $checkExercices->fetchColumn();
                    
                    if ($countExercices == 0) {
                        $stmt = $conn->prepare("DELETE FROM themes WHERE id_theme = ?");
                        $stmt->execute([$_POST['id']]);
                        $_SESSION['message'] = "Thème supprimé avec succès";
                        $_SESSION['message_type'] = "success";
                    } else {
                        $_SESSION['message'] = "Ce thème ne peut pas être supprimé car il est utilisé par des exercices";
                        $_SESSION['message_type'] = "error";
                    }
                    break;
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = "Erreur: " . $e->getMessage();
            $_SESSION['message_type'] = "error";
        }
    }
    header("Location: theme.php?id=".$id);
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

    <div class="card shadow-sm">
        <!-- En-tête -->
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Histoires</h3>
                <button class="btn btn-light" onclick="toggleForm()">
                    <i class="bi bi-plus-lg"></i> Nouveau
                </button>
            </div>
        </div>

                <!-- Formulaire unique pour ajout/modification -->
                <div id="form-container" class="card mt-3 d-none">
            <div class="card-body">
                <h4 class="card-title" id="form-title">Ajouter une histoire</h4>
                <form method="POST" id="theme-form">
                    <input type="hidden" name="action" id="form-action" value="add">
                    <input type="hidden" name="id_theme" id="id_theme">
                    
                    <div class="mb-3">
                        <label class="form-label">Titre de l'histoire</label>
                        <input type="text" class="form-control" id="nom_theme" name="nom_theme" required>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" onclick="toggleForm()">Annuler</button>
                        <button type="submit" class="btn btn-success">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tableau -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Titre</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // 6. PAGINATION ET DONNÉES
                        $limit = 10;
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        $offset = ($page - 1) * $limit;

                        // Comptage total
                        $countStmt = $conn->prepare("SELECT COUNT(*) as total FROM themes");
                        $countStmt->execute();
                        $totalItems = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
                        $totalPages = ceil($totalItems / $limit);

                        // Récupération des thèmes
                        $query = "SELECT * FROM themes LIMIT :limit OFFSET :offset";
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                        $stmt->execute();
                        $themes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($themes as $theme):
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($theme['nom_theme']) ?></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick="editTheme(<?= $theme['id_theme'] ?>, '<?= htmlspecialchars(addslashes($theme['nom_theme'])) ?>')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $theme['id_theme'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce thème ?')">
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
</div>

<script>
// Gestion des formulaires
function toggleForm(editing = false) {
    const form = document.getElementById('form-container');
    form.classList.toggle('d-none');
    
    if (editing) {
        document.getElementById('form-title').textContent = "Modifier une catégorie d'histoire";
        document.getElementById('form-action').value = "update";
    } else {
        document.getElementById('form-title').textContent = "Ajouter une catégorie d'histoire";
        document.getElementById('form-action').value = "add";
        document.getElementById('theme-form').reset();
    }
}

function editTheme(id, nom) {
    document.getElementById('id_theme').value = id;
    document.getElementById('nom_theme').value = nom;
    toggleForm(true);
}
</script>