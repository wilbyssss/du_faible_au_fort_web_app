// Fonction pour afficher ou masquer le formulaire
function toggleForm() {
    var form = document.getElementById("form-container");
    form.classList.toggle("show");
}

function toggleFormUpdate() {
    var form = document.getElementById("form-container-modify");
    form.classList.toggle("show");
}

// Fonction pour afficher le formulaire de modification et pré-remplir les champs
function editTheme(id, nom) {
    toggleFormUpdate();
    document.getElementById("id_theme").value = id;
    document.getElementById("nom_theme").value = nom;
}

// Fonction pour confirmer et supprimer un niveau
function deleteTheme(id,idUser) {
    if (confirm("Voulez-vous vraiment supprimer ce niveau ?")) {
        window.location.href = `../../pages/contenus/crud/theme/delete_theme.php?id=${id}&user=${idUser}`;
    }
}
