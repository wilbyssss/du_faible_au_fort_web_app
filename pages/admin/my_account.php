<?php 
error_reporting(0);
session_start();
$baseUrl = "/du_faible_au_fort/";
require_once('../../connect_database.php');
session_start();

$id = $_GET['id'] ?? null;
$database = new Database();
$conn = $database->getConnection();

$query = "SELECT * FROM administration WHERE id_admin = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
include('../../includes/header_view.php');
include('../../includes/slider_bar.php');
?>

    <link rel="stylesheet" href="<?php echo $baseUrl?>assets/css/my_account.css">
    <div class="profile-containers" style="margin-top: 150px;">
        <div class="profile-header">
            <div class="avatar">
                <i class="fas fa-user-shield"></i>
            </div>
            <h1>Mon Profil Administrateur</h1>
            <p><?php echo htmlspecialchars($user['email_admin']); ?></p>
        </div>

        <div class="profile-content">
            <div class="tabs">
                <button class="tab-btn active" data-tab="info">Informations</button>
                <button class="tab-btn" data-tab="security">Sécurité</button>
            </div>

            <div class="tab-content active" id="info-tab">
                <form id="profile-form" action="update_profile.php" method="POST">
                    <input type="hidden" name="id_admin" value="<?php echo $user['id_admin']; ?>">
                    
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email_admin']); ?>" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="nom"><i class="fas fa-user"></i> Nom</label>
                        <input type="text" name="username" id="nom" value="<?php echo htmlspecialchars($user['username_admin'] ?? ''); ?>">
                    </div>
                    
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                </form>
            </div>

            <div class="tab-content" id="security-tab">
                <form id="password-form" action="update_password.php" method="POST">
                    <input type="hidden" name="id_admin" value="<?php echo $user['id_admin']; ?>">
                    
                    <div class="form-group">
                        <label for="current_password"><i class="fas fa-lock"></i> Mot de passe actuel</label>
                        <input type="password" name="current_password" id="current_password" required>
                        <i class="fas fa-eye toggle-password" data-target="current_password"></i>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password"><i class="fas fa-key"></i> Nouveau mot de passe</label>
                        <input type="password" name="new_password" id="new_password" required>
                        <div class="password-strength">
                            <span class="strength-bar"></span>
                            <span class="strength-text">Faible</span>
                        </div>
                        <i class="fas fa-eye toggle-password" data-target="new_password"></i>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password"><i class="fas fa-key"></i> Confirmer le nouveau mot de passe</label>
                        <input type="password" name="confirm_password" id="confirm_password" required>
                        <i class="fas fa-eye toggle-password" data-target="confirm_password"></i>
                    </div>
                    
                    <button type="submit" class="btn-save">
                        <i class="fas fa-lock"></i> Changer le mot de passe
                    </button>
                </form>
            </div>
        </div>

        <div class="danger-zone">
            <h3><i class="fas fa-exclamation-triangle"></i> Zone dangereuse</h3>
            <button id="delete-account-btn" class="btn-delete">
                <i class="fas fa-trash-alt"></i> Supprimer mon compte
            </button>
            
            <div id="delete-confirm" style="display: none;">
                <p>Êtes-vous sûr de vouloir supprimer définitivement votre compte ? Cette action est irréversible.</p>
                <form action="delete_account.php" method="POST">
                    <input type="hidden" name="id_admin" value="<?php echo $user['id_admin']; ?>">
                    <div class="form-group">
                        <label for="delete_password"><i class="fas fa-lock"></i> Entrez votre mot de passe pour confirmer</label>
                        <input type="password" name="delete_password" id="delete_password" required>
                    </div>
                    <button type="submit" class="btn-confirm-delete">
                        <i class="fas fa-check"></i> Confirmer la suppression
                    </button>
                    <button type="button" id="cancel-delete" class="btn-cancel">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="../../assets/js/profile.js"></script>
