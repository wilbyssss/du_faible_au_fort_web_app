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
                    $stmt = $conn->prepare("INSERT INTO exercice_a_trou (libelle_ex, instruction_globale, id_theme, id_text_training, niveauId) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['libelle'],
                        $_POST['instruction'],
                        $_POST['theme_id'],
                        !empty($_POST['texte_id']) ? $_POST['texte_id'] : null,
                        $_POST['niveau_id']
                    ]);
                    $_SESSION['message'] = "Exercice ajouté avec succès";
                    $_SESSION['message_type'] = "success";
                    break;
                    
                case 'update':
                    $stmt = $conn->prepare("UPDATE exercice_a_trou SET libelle_ex = ?, instruction_globale = ?, id_theme = ?, id_text_training = ?, niveauId = ? WHERE id_ex_trou = ?");
                    $stmt->execute([
                        $_POST['libelle'],
                        $_POST['instruction'],
                        $_POST['theme_id'],
                        !empty($_POST['texte_id']) ? $_POST['texte_id'] : null,
                        $_POST['niveau_id'],
                        $_POST['id_ex_trou']
                    ]);
                    $_SESSION['message'] = "Exercice modifié avec succès";
                    $_SESSION['message_type'] = "success";
                    break;
                    
                case 'delete':
                    // Vérification avant suppression
                    $checkPhrases = $conn->prepare("SELECT COUNT(*) FROM phrase_a_trou WHERE id_ex_trou = ?");
                    $checkPhrases->execute([$_POST['id']]);
                    $countPhrases = $checkPhrases->fetchColumn();
                    
                    $checkResultats = $conn->prepare("SELECT COUNT(*) FROM traitementexercice WHERE id_ex_trou = ?");
                    $checkResultats->execute([$_POST['id']]);
                    $countResultats = $checkResultats->fetchColumn();
                    
                    if ($countPhrases == 0 && $countResultats == 0) {
                        $stmt = $conn->prepare("DELETE FROM exercice_a_trou WHERE id_ex_trou = ?");
                        $stmt->execute([$_POST['id']]);
                        $_SESSION['message'] = "Exercice supprimé avec succès";
                        $_SESSION['message_type'] = "success";
                    } else {
                        $message = "Cet exercice ne peut pas être supprimé car il est utilisé par :";
                        if ($countPhrases > 0) $message .= " des phrases,";
                        if ($countResultats > 0) $message .= " des résultats,";
                        $message = rtrim($message, ',');
                        
                        $_SESSION['message'] = $message;
                        $_SESSION['message_type'] = "error";
                    }
                    break;
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = "Erreur: " . $e->getMessage();
            $_SESSION['message_type'] = "error";
        }
    }
    header("Location: exercice.php?id=".$id);
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
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Bouche-trous</h3>
                        <button class="btn btn-light" onclick="toggleForm()">
                            <i class="bi bi-plus-lg"></i> Nouveau
                        </button>
                    </div>
                </div>

                <!-- Formulaire unique pour ajout/modification -->
                <div id="form-container" class="card mb-4 d-none">
                    <div class="card-body">
                        <h4 class="card-title" id="form-title">Ajouter un Exercice</h4>
                        <form method="POST" id="exercice-form">
                            <input type="hidden" name="action" id="form-action" value="add">
                            <input type="hidden" name="id_ex_trou" id="id_exercice">
                            
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Libellé</label>
                                    <input type="text" class="form-control" id="libelle" name="libelle" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Consigne générale</label>
                                    <textarea class="form-control" id="instruction" name="instruction" rows="3" required></textarea>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Histoire à croquer</label>
                                    <select class="form-select" id="theme_id" name="theme_id" required>
                                        <option value="">-- Sélectionnez une histoire à croquer--</option>
                                        <?php
                                        $themes = $conn->query("SELECT * FROM themes")->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($themes as $theme): ?>
                                            <option value="<?= $theme['id_theme'] ?>"><?= htmlspecialchars($theme['nom_theme']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">Niveau de difficulté</label>
                                    <select class="form-select" id="niveau_id" name="niveau_id" required>
                                        <option value="">-- Sélectionnez un niveau --</option>
                                        <?php
                                        $niveaux = $conn->query("SELECT * FROM niveau_difficulte")->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($niveaux as $niveau): ?>
                                            <option value="<?= $niveau['id_niveau'] ?>"><?= htmlspecialchars($niveau['nom_niveau']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label">Histoire</label>
                                    <select class="form-select" id="texte_id" name="texte_id">
                                        <option value="">-- Sélectionnez une histoire --</option>
                                        <?php
                                        $textes = $conn->query("SELECT * FROM text_training")->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($textes as $texte): ?>
                                            <option value="<?= $texte['id_text_training'] ?>"><?= htmlspecialchars($texte['titre_text']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary" onclick="toggleForm()">Annuler</button>
                                <button type="submit" class="btn btn-success">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tableau des exercices -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Libellé</th>
                                    <th>Consigne générale</th>
                                    <th>Histoire à croquer</th>
                                    <th>Niveau</th>
                                    <th>Histoire</th>
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
                                $countStmt = $conn->prepare("SELECT COUNT(*) as total FROM exercice_a_trou");
                                $countStmt->execute();
                                $totalItems = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
                                $totalPages = ceil($totalItems / $limit);

                                // Récupération des exercices
                                $query = "SELECT e.*, t.nom_theme, te.titre_text, n.nom_niveau
                                         FROM exercice_a_trou e
                                         LEFT JOIN themes t ON e.id_theme = t.id_theme
                                         LEFT JOIN text_training te ON e.id_text_training = te.id_text_training
                                         LEFT JOIN niveau_difficulte n ON e.niveauId = n.id_niveau
                                         LIMIT :limit OFFSET :offset";
                                $stmt = $conn->prepare($query);
                                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                                $stmt->execute();
                                $exercices = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($exercices as $exercice):
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($exercice['libelle_ex']) ?></td>
                                    <td><?= htmlspecialchars($exercice['instruction_globale']) ?></td>
                                    <td><?= htmlspecialchars($exercice['nom_theme'] ?? 'Non défini') ?></td>
                                    <td><?= htmlspecialchars($exercice['nom_niveau'] ?? 'Non défini') ?></td>
                                    <td><?= htmlspecialchars($exercice['titre_text'] ?? 'Aucun texte') ?></td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="editExercice(
                                                <?= $exercice['id_ex_trou'] ?>,
                                                '<?= htmlspecialchars(addslashes($exercice['libelle_ex'])) ?>',
                                                '<?= htmlspecialchars(addslashes($exercice['instruction_globale'])) ?>',
                                                <?= $exercice['id_theme'] ?>,
                                                <?= $exercice['niveauId'] ?? 'null' ?>,
                                                <?= $exercice['id_text_training'] ?? 'null' ?>
                                            )">
                                                <i class="bi bi-pencil"></i> Modifier
                                            </button>
                                            
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $exercice['id_ex_trou'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet exercice ?')">
                                                    <i class="bi bi-trash"></i>Supprimer</button>
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
    </div>
</div>

<script>
// Gestion des formulaires
function toggleForm(editing = false) {
    const form = document.getElementById('form-container');
    form.classList.toggle('d-none');
    
    if (editing) {
        document.getElementById('form-title').textContent = "Modifier un bouche-trous";
        document.getElementById('form-action').value = "update";
    } else {
        document.getElementById('form-title').textContent = "Ajouter un bouche-trous";
        document.getElementById('form-action').value = "add";
        document.getElementById('exercice-form').reset();
    }
}

function editExercice(id, libelle, instruction, themeId, niveauId, texteId) {
    document.getElementById('id_exercice').value = id;
    document.getElementById('libelle').value = libelle;
    document.getElementById('instruction').value = instruction;
    document.getElementById('theme_id').value = themeId;
    document.getElementById('niveau_id').value = niveauId !== 'null' ? niveauId : '';
    document.getElementById('texte_id').value = texteId !== 'null' ? texteId : '';
    
    toggleForm(true);
}
</script>