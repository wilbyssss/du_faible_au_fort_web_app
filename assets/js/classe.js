function toggleForm() {
 var form = document.getElementById("form-container");
 form.classList.toggle("show");
}

function toggleFormUpdate() {
 var form = document.getElementById("form-container-modify");
 form.classList.toggle("show");
}

function editClasse(id, nom) {
 toggleFormUpdate();
 document.getElementById("id_classe_modify").value = id;
 document.getElementById("nom_classe_modify").value = nom;
}


function deleteClasse(id, idUser) {
    if (confirm("Voulez-vous vraiment supprimer cette classe ?")) {
        window.location.href = `../../pages/contenus/crud/classe/delete_classe.php?id=${id}&user=${idUser}`;
    }
}