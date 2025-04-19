document.addEventListener('DOMContentLoaded', function() {
    // Gestion des onglets
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Retirer active de tous les boutons et contenus
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            
            // Ajouter active au bouton cliqué
            btn.classList.add('active');
            
            // Afficher le contenu correspondant
            const tabId = btn.getAttribute('data-tab');
            document.getElementById(`${tabId}-tab`).classList.add('active');
        });
    });
    
    // Basculer la visibilité du mot de passe
    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            
            if (input.type === 'password') {
                input.type = 'text';
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
            }
        });
    });
    
    // Confirmation de suppression de compte
    const deleteBtn = document.getElementById('delete-account-btn');
    const deleteConfirm = document.getElementById('delete-confirm');
    const cancelDelete = document.getElementById('cancel-delete');
    
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            deleteConfirm.style.display = 'block';
        });
    }
    
    if (cancelDelete) {
        cancelDelete.addEventListener('click', function() {
            deleteConfirm.style.display = 'none';
        });
    }
    
    // Vérification des mots de passe
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    const passwordForm = document.getElementById('password-form');
    
    if (newPassword && confirmPassword) {
        // Indicateur de force du mot de passe
        newPassword.addEventListener('input', function() {
            updatePasswordStrengthIndicator(this.value);
        });
        
        // Vérification de la correspondance des mots de passe
        confirmPassword.addEventListener('input', function() {
            checkPasswordMatch();
        });
    }
    
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            if (!validatePasswordForm()) {
                e.preventDefault();
            }
        });
    }
    
    function updatePasswordStrengthIndicator(password) {
        const strengthBar = document.querySelector('.strength-bar');
        const strengthText = document.querySelector('.strength-text');
        const strength = checkPasswordStrength(password);
        
        strengthBar.style.width = `${strength.percentage}%`;
        strengthBar.style.backgroundColor = strength.color;
        strengthText.textContent = strength.text;
        strengthText.style.color = strength.color;
    }
    
    function checkPasswordStrength(password) {
        const strength = {
            0: { text: 'Très faible', color: '#e74c3c', percentage: 20 },
            1: { text: 'Faible', color: '#e67e22', percentage: 40 },
            2: { text: 'Moyen', color: '#f1c40f', percentage: 60 },
            3: { text: 'Fort', color: '#2ecc71', percentage: 80 },
            4: { text: 'Très fort', color: '#27ae60', percentage: 100 }
        };
        
        let score = 0;
        
        // Longueur
        if (password.length > 0) score++;
        if (password.length >= 8) score++;
        
        // Caractères spéciaux
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) score++;
        
        // Chiffres
        if (/\d/.test(password)) score++;
        
        // Majuscules et minuscules
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
        
        return strength[Math.min(score, 4)];
    }
    
    function checkPasswordMatch() {
        if (newPassword.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity("Les mots de passe ne correspondent pas");
            showPasswordMismatchError();
        } else {
            confirmPassword.setCustomValidity("");
            hidePasswordMismatchError();
        }
    }
    
    function showPasswordMismatchError() {
        let errorElement = confirmPassword.nextElementSibling;
        if (!errorElement || !errorElement.classList.contains('error-message')) {
            errorElement = document.createElement('div');
            errorElement.className = 'error-message';
            errorElement.style.color = '#e74c3c';
            errorElement.style.fontSize = '0.8rem';
            errorElement.style.marginTop = '5px';
            confirmPassword.parentNode.insertBefore(errorElement, confirmPassword.nextSibling);
        }
        errorElement.textContent = "Les mots de passe ne correspondent pas";
    }
    
    function hidePasswordMismatchError() {
        const errorElement = confirmPassword.nextElementSibling;
        if (errorElement && errorElement.classList.contains('error-message')) {
            errorElement.textContent = '';
        }
    }
    
    function validatePasswordForm() {
        if (newPassword.value !== confirmPassword.value) {
            alert("Les mots de passe ne correspondent pas !");
            return false;
        }
        
        // Vérification supplémentaire de la force du mot de passe
        const strength = checkPasswordStrength(newPassword.value);
        if (strength.percentage < 60) { // Moins que "Moyen"
            if (!confirm("Votre mot de passe est considéré comme faible. Voulez-vous vraiment continuer ?")) {
                return false;
            }
        }
        
        return true;
    }
});