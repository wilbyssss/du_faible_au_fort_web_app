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
                    $stmt = $conn->prepare("INSERT INTO phrase_a_trou (libelle_phrase_a_trou, indication_phr, reponse_correspondante, id_ex_trou) VALUES (?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['libelle_phrase'],
                        $_POST['indication'],
                        $_POST['reponse'],
                        $_POST['exercice_id']
                    ]);
                    $_SESSION['message'] = "Phrase ajoutée avec succès";
                    $_SESSION['message_type'] = "success";
                    break;
                    
                case 'update':
                    $stmt = $conn->prepare("UPDATE phrase_a_trou SET libelle_phrase_a_trou = ?, indication_phr = ?, reponse_correspondante = ?, id_ex_trou = ? WHERE id_phrase_a_trou = ?");
                    $stmt->execute([
                        $_POST['libelle_phrase'],
                        $_POST['indication'],
                        $_POST['reponse'],
                        $_POST['exercice_id'],
                        $_POST['id_phrase_a_trou']
                    ]);
                    $_SESSION['message'] = "Phrase modifiée avec succès";
                    $_SESSION['message_type'] = "success";
                    break;
                    
                case 'delete':
                        $stmt = $conn->prepare("DELETE FROM phrase_a_trou WHERE id_phrase_a_trou = ?");
                        $stmt->execute([$_POST['id']]);
                        $_SESSION['message'] = "Phrase supprimée avec succès";
                        $_SESSION['message_type'] = "success";
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = "Erreur: " . $e->getMessage();
            $_SESSION['message_type'] = "error";
        }
    }
    header("Location: phrase.php?id=".$id);
    exit;
}

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$countStmt = $conn->prepare("SELECT COUNT(*) as total FROM phrase_a_trou");
$countStmt->execute();
$totalItems = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalItems / $limit);

$query = "SELECT p.*, e.libelle_ex 
          FROM phrase_a_trou AS p 
          INNER JOIN exercice_a_trou AS e ON p.id_ex_trou = e.id_ex_trou
          LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($query);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$phrases = $stmt->fetchAll(PDO::FETCH_ASSOC);

$exercices = $conn->query("SELECT * FROM exercice_a_trou")->fetchAll(PDO::FETCH_ASSOC);


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
                <h3 class="mb-0">Enoncés à trou</h3>
                <button class="btn btn-light" onclick="toggleForm()">
                    <i class="bi bi-plus-lg"></i> Nouveau
                </button>
            </div>
        </div>

        <!-- Formulaire (caché par défaut) -->
        <div id="form-container" class="card mb-4 d-none">
            <div class="card-body">
                <h4 class="card-title" id="form-title">Ajouter une phrase à trou</h4>
                <form method="POST" id="phrase-form">
                    <input type="hidden" name="action" id="form-action" value="add">
                    <input type="hidden" name="id_phrase_a_trou" id="id_phrase">
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Phrase (utilisez _ pour créer le trou)</label>
                            <textarea class="form-control" id="libelle_phrase" name="libelle_phrase" rows="3" required></textarea>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Indication</label>
                            <input type="text" class="form-control" id="indication" name="indication" placeholder="(Optionnel)">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Exercice associé</label>
                            <select class="form-select" id="exercice_id" name="exercice_id" required>
                                <option value="">-- Sélectionnez --</option>
                                <?php foreach ($exercices as $exercice): ?>
                                    <option value="<?= $exercice['id_ex_trou'] ?>"><?= htmlspecialchars($exercice['libelle_ex']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Réponse</label>
                            <textarea class="form-control" id="reponse" name="reponse" rows="3" required></textarea>
                            <div class="form-text">Séparez plusieurs réponses par des virgules</div>
                        </div>
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
                            <th>Phrase</th>
                            <th>Indication</th>
                            <th>Réponse</th>
                            <th>Exercice</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($phrases as $phrase): ?>
                        <tr>
                            <td><?= htmlspecialchars($phrase['libelle_phrase_a_trou']) ?></td>
                            <td><?= htmlspecialchars($phrase['indication_phr'] ?? 'Aucune') ?></td>
                            <td><?= htmlspecialchars($phrase['reponse_correspondante']) ?></td>
                            <td><?= htmlspecialchars($phrase['libelle_ex']) ?></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" onclick="editPhrase(
                                        <?= $phrase['id_phrase_a_trou'] ?>,
                                        '<?= htmlspecialchars(addslashes($phrase['libelle_phrase_a_trou'])) ?>',
                                        '<?= htmlspecialchars(addslashes($phrase['indication_phr'])) ?>',
                                        '<?= htmlspecialchars(addslashes($phrase['reponse_correspondante'])) ?>',
                                        <?= $phrase['id_ex_trou'] ?>
                                    )">
                                        <i class="bi bi-pencil"></i>Modifier
                                    </button>
                                    
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $phrase['id_phrase_a_trou'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette phrase ?')">
                                            <i class="bi bi-trash"></i>Supprimer
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

<script>
// Gestion des formulaires
function toggleForm(editing = false) {
    const form = document.getElementById('form-container');
    form.classList.toggle('d-none');
    
    if (editing) {
        document.getElementById('form-title').textContent = "Modifier un énoncé à trou";
        document.getElementById('form-action').value = "update";
    } else {
        document.getElementById('form-title').textContent = "Ajouter un énoncé à trou";
        document.getElementById('form-action').value = "add";
        document.getElementById('phrase-form').reset();
    }
}

function editPhrase(id, libelle, indication, reponse, exerciceId) {
    document.getElementById('id_phrase').value = id;
    document.getElementById('libelle_phrase').value = libelle;
    document.getElementById('indication').value = indication;
    document.getElementById('reponse').value = reponse;
    document.getElementById('exercice_id').value = exerciceId;
    
    toggleForm(true);
}
</script>