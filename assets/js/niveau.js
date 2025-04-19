// Fonctions de base
function toggleForm() {
    const form = document.getElementById("form-container");
    form.classList.toggle("show");
}

function toggleFormUpdate() {
    const form = document.getElementById("form-container-modify");
    form.classList.toggle("show");
}

function toggleAffectForm() {
    const form = document.getElementById("form-affect-container");
    form.classList.toggle("show");
}

// Fonction d'édition
function editNiveau(id, nom) {
    document.getElementById("id_niveau").value = id;
    document.getElementById("nom_modif").value = nom;
    toggleFormUpdate();
}

// Fonction de suppression
function deleteNiveau(id,idUser) {
    if (confirm("Voulez-vous vraiment supprimer ce niveau ?")) {
        window.location.href = `../../pages/contenus/crud/niveau/delete_niveau.php?id=${id}&user=${idUser}`;
    }
}

// Fonction d'affectation
function showAffectForm(idNiveau) {
    document.getElementById('affect_id_niveau').value = idNiveau;
    toggleAffectForm();
}

// Fonction de suppression d'affectation
function removeAffectation(idNiveau, idClasse, idUser) {
    if (confirm("Voulez-vous vraiment supprimer cette affectation ?")) {
        window.location.href = `../../pages/contenus/crud/niveau/remove_affectation.php?id_niveau=${idNiveau}&id_classe=${idClasse}&user=${idUser}`;
    }
}