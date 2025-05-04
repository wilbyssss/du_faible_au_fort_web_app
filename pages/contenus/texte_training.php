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

// Connexion DB
require_once('../../connect_database.php');
$database = new Database();
$conn = $database->getConnection();

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $stmt = $conn->prepare("INSERT INTO text_training (titre_text, contenu_text_training, visibility) VALUES (?, ?, 1)");
                $stmt->execute([$_POST['titre_texte'], $_POST['contenu']]);
                $_SESSION['message'] = 'Texte ajouté avec succès';
                $_SESSION['message_type'] = 'success';
                break;

            case 'edit':
                $stmt = $conn->prepare("UPDATE text_training SET titre_text = ?, contenu_text_training = ? WHERE id_text_training = ?");
                $stmt->execute([$_POST['titre_texte'], $_POST['contenu'], $_POST['id_text']]);
                $_SESSION['message'] = 'Texte mis à jour avec succès';
                $_SESSION['message_type'] = 'success';
                break;

            case 'update_visibility':
                $stmt = $conn->prepare("UPDATE text_training SET visibility = ? WHERE id_text_training = ?");
                $stmt->execute([$_POST['visibility'], $_POST['id']]);
                $_SESSION['message'] = 'Visibilité mise à jour';
                $_SESSION['message_type'] = 'success';
                break;

            case 'delete':
                $check = $conn->prepare('SELECT COUNT(*) FROM exercice_a_trou WHERE id_text_training = ?');
                $check->execute([$_POST['id']]);
                $count = $check->fetchColumn();

                if($count == 0){
                    $stmt = $conn->prepare("DELETE FROM text_training WHERE id_text_training = ?");
                    $stmt->execute([$_POST['id']]);
                    $_SESSION['message'] = 'Texte supprimé avec succès';
                    $_SESSION['message_type'] = 'success';
                }else{
                    $_SESSION['message'] = 'Ce texte contient des exercices vous ne pouvez pas le supprimez';
                    $_SESSION['message_type'] = 'error';
                }
                break;
        }
    }
    header("Location: texte_training.php?id=".$id);
    exit;
}

// Inclusions
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
    endif; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Textes d'entraînement</h3>
                <button class="btn btn-light" onclick="toggleForm()">
                    <i class="bi bi-plus-lg"></i> Nouveau
                </button>
            </div>
        </div>

        <!-- Formulaire unique (ajout/modif) -->
        <div id="form-container" class="card mt-3 d-none">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0" id="form-title">Ajouter un texte</h4>
            </div>
            <div class="card-body">
                <form method="POST" id="text-form">
                    <input type="hidden" name="id_text" id="id-text">
                    <input type="hidden" name="action" id="form-action" value="add">
                    
                    <div class="mb-3">
                        <label class="form-label">Titre</label>
                        <input type="text" class="form-control" name="titre_texte" id="titre-text" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Contenu</label>
                        <textarea class="form-control" name="contenu" id="contenu-text" rows="5" required></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" onclick="toggleForm()">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body">
            <!-- Tableau responsive -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Titre</th>
                            <th>Contenu</th>
                            <th>Visibilité</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $limit = 10;
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        $offset = ($page - 1) * $limit;

                        // Pagination
                        $countStmt = $conn->prepare("SELECT COUNT(*) FROM text_training");
                        $countStmt->execute();
                        $totalItems = $countStmt->fetchColumn();
                        $totalPages = ceil($totalItems / $limit);

                        // Récupération données
                        $stmt = $conn->prepare("SELECT * FROM text_training LIMIT :limit OFFSET :offset");
                        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                        $stmt->execute();
                        $textes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($textes as $texte):
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($texte['titre_text']) ?></td>
                            <td><?= substr(htmlspecialchars($texte['contenu_text_training']), 0, 50) ?>...</td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="update_visibility">
                                    <input type="hidden" name="id" value="<?= $texte['id_text_training'] ?>">
                                    <select class="form-select form-select-sm" name="visibility" onchange="this.form.submit()">
                                        <option value="1" <?= $texte['visibility'] ? 'selected' : '' ?>>Visible</option>
                                        <option value="0" <?= !$texte['visibility'] ? 'selected' : '' ?>>Masqué</option>
                                    </select>
                                </form>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" 
                                            onclick="editText(<?= $texte['id_text_training'] ?>, 
                                                      '<?= htmlspecialchars(addslashes($texte['titre_text'])) ?>', 
                                                      `<?= htmlspecialchars(addslashes($texte['contenu_text_training'])) ?>`)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $texte['id_text_training'] ?>">
                                        <button type="submit" class="btn btn-outline-danger" 
                                                onclick="return confirm('Supprimer définitivement ce texte ?')">
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

            <!-- Pagination Bootstrap -->
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

<!-- Scripts -->
<script>
// Gestion des formulaires
function toggleForm(editing = false) {
    const form = document.getElementById('form-container');
    form.classList.toggle('d-none');
    
    if (editing) {
        document.getElementById('form-title').textContent = "Modifier un texte";
        document.getElementById('form-action').value = "edit";
    } else {
        document.getElementById('form-title').textContent = "Ajouter un texte";
        document.getElementById('form-action').value = "add";
        document.getElementById('text-form').reset();
    }
}

function editText(id, titre, contenu) {
    document.getElementById('id-text').value = id;
    document.getElementById('titre-text').value = titre;
    document.getElementById('contenu-text').value = contenu;
    toggleForm(true);
}
</script>