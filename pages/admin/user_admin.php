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
 echo '<script>alert("session expiré")</script>';
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
                // Ajouter un utilisateur
                $stmt = $conn->prepare("INSERT INTO administration (username_admin, email_admin, password_admin, role_admin) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_POST['nom'], $_POST['email'], password_hash($_POST['password'], PASSWORD_DEFAULT), $_POST['role']]);
                break;
                
            case 'edit':
                // Modifier un utilisateur
                $stmt = $conn->prepare("UPDATE administration SET username_admin = ?, email_admin = ?, role_admin = ? WHERE id_admin = ?");
                $stmt->execute([$_POST['nom'], $_POST['email'], $_POST['role'], $_POST['id']]);
                break;
                
            case 'delete':
                // Supprimer un utilisateur
                $stmt = $conn->prepare("DELETE FROM administration WHERE id_admin = ?");
                $stmt->execute([$_POST['id']]);
                break;
        }
    }
    // Rediriger pour éviter la soumission multiple
    header("Location: user_admin.php?id=".$id);
    exit;
}

//
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset =( $page - 1 )* $limit;

$countQuery = "SELECT COUNT(*) as total FROM administration";
$countStmt = $conn->prepare($countQuery);
$countStmt->execute();
$totalItems = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalItems / $limit);

// Récupérer les utilisateurs et les rôles
$users = $conn->query("SELECT a.*, r.nom_role as role_nom FROM administration a JOIN roles_admin r ON a.role_admin = r.id_role LIMIT $limit OFFSET $offset")->fetchAll(PDO::FETCH_ASSOC);
$roles = $conn->query("SELECT * FROM roles_admin")->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/pagination.css">
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
</style>
<div class="container" style=" margin-top:100px; margin-left: 20%; ">
 <h2>Gestion des utilisateurs</h2>

 <!-- Boutons d'action -->
 <div class="mb-3">
  <button class="btn btn-primary" onclick="showForm('add')">
   <i class="bi bi-plus-circle"></i> Ajouter un utilisateur
  </button>
 </div>

 <!-- Formulaire d'ajout -->
 <div id="add-form" class="form-container">
  <h4>Ajouter un utilisateur</h4>
  <form method="POST">
   <input type="hidden" name="action" value="add">
   <div class="row mb-3">
    <div class="col-md-4">
     <label class="form-label">Nom</label>
     <input type="text" name="nom" class="form-control" required>
    </div>
    <div class="col-md-4">
     <label class="form-label">Email</label>
     <input type="email" name="email" class="form-control" required>
    </div>
    <div class="col-md-4">
     <label class="form-label">Mot de passe</label>
     <input type="password" name="password" class="form-control" required>
    </div>
   </div>
   <div class="row mb-3">
    <div class="col-md-4">
     <label class="form-label">Rôle</label>
     <select name="role" class="form-select" required>
      <?php foreach ($roles as $role): ?>
      <option value="<?= $role['id_role'] ?>"><?= $role['nom_role'] ?></option>
      <?php endforeach; ?>
     </select>
    </div>
   </div>
   <button type="submit" class="btn btn-success">Enregistrer</button>
   <button type="button" class="btn btn-secondary" onclick="hideForms()">Annuler</button>
  </form>
 </div>

 <!-- Formulaire de modification -->
 <div id="edit-form" class="form-container">
  <h4>Modifier l'utilisateur</h4>
  <form method="POST">
   <input type="hidden" name="action" value="edit">
   <input type="hidden" name="id" id="edit-id">
   <div class="row mb-3">
    <div class="col-md-4">
     <label class="form-label">Nom</label>
     <input type="text" name="nom" id="edit-nom" class="form-control" required>
    </div>
    <div class="col-md-4">
     <label class="form-label">Email</label>
     <input type="email" name="email" id="edit-email" class="form-control" required>
    </div>
   </div>
   <div class="row mb-3">
    <div class="col-md-4">
     <label class="form-label">Rôle</label>
     <select name="role" id="edit-role" class="form-select" required>
      <?php foreach ($roles as $role): ?>
      <option value="<?= $role['id_role'] ?>"><?= $role['nom_role'] ?></option>
      <?php endforeach; ?>
     </select>
    </div>
   </div>
   <button type="submit" class="btn btn-success">Enregistrer</button>
   <button type="button" class="btn btn-secondary" onclick="hideForms()">Annuler</button>
  </form>
 </div>

 <!-- Tableau des utilisateurs -->
 <div class="table-responsive container">
  <table class="table table-striped table-hover">
   <thead>
    <tr>
     <th>ID</th>
     <th>Nom</th>
     <th>Email</th>
     <th>Rôle</th>
     <th>Statut</th>
     <th>Actions</th>
    </tr>
   </thead>
   <tbody>
    <?php foreach ($users as $user): ?>
    <tr>
     <td><?= $user['id_admin'] ?></td>
     <td><?= htmlspecialchars($user['username_admin']) ?></td>
     <td><?= htmlspecialchars($user['email_admin']) ?></td>
     <td><?= htmlspecialchars($user['role_nom']) ?></td>
     <td><?= (htmlspecialchars($user['statut_admin']) != 0) ? "en ligne" : "hors ligne" ?></td>
     <td>
      <button class="btn btn-sm btn-warning action-btn"
       onclick="showEditForm(<?= $user['id_admin'] ?>, '<?= htmlspecialchars($user['username_admin']) ?>', '<?= htmlspecialchars($user['email_admin']) ?>', <?= $user['role_admin'] ?>)">
       <i class="bi bi-pencil"></i> Modifier
      </button>
      <form method="POST" style="display:inline;">
       <input type="hidden" name="action" value="delete">
       <input type="hidden" name="id" value="<?= $user['id_admin'] ?>">
       <button type="submit" class="btn btn-sm btn-danger action-btn"
        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur?')">
        <i class="bi bi-trash"></i> Supprimer
       </button>
      </form>
     </td>
    </tr>
    <?php endforeach; ?>
   </tbody>
  </table>

  <div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>&id=<?= $id?>">Précédent</a>
    <?php endif; ?>
    
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>&id=<?= $id?>" <?= $i === $page ? 'class="active"' : '' ?>><?= $i ?></a>
    <?php endfor; ?>
    
    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?>&id=<?= $id?>">Suivant</a>
    <?php endif; ?>
    </div>
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

function showEditForm(id, nom, email, role) {
 document.getElementById('edit-id').value = id;
 document.getElementById('edit-nom').value = nom;
 document.getElementById('edit-email').value = email;
 document.getElementById('edit-role').value = role;
 showForm('edit');
}
</script>