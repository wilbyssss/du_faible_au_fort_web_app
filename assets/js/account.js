function toggleForm() {
    var form = document.getElementById("form-container");
    form.classList.toggle("show");
}

function toggleFormUpdate() {
    var form = document.getElementById("form-container-modify");
    form.classList.toggle("show");
}

function editAccount(id, nom) {
    toggleFormUpdate();
    document.getElementById("id_compte").value = id;
    document.getElementById("nom_compte").value = nom;
}

function deleteAccount(id, idUser) {
    if (confirm("Voulez-vous vraiment supprimer ce type de compte ?")) {
        window.location.href = `../../pages/contenus/crud/compte/delete_account.php?id=${id}&user=${idUser}`;
    }
}