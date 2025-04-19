function toggleForm() {
 var form = document.getElementById("form-container");
 form.classList.toggle("show");
}

function toggleFormUpdate() {
 var form = document.getElementById("form-container-modify");
 form.classList.toggle("show");
}

function editExercice(id, libelle, instruction, theme_id, texte_id) {
 toggleFormUpdate();
 document.getElementById("id_exercice").value = id;
 document.getElementById("nom_exercice").value = libelle;
 document.getElementById("instruction_exercice").value = instruction;
 document.getElementById("theme_id_modify").value = theme_id;
 document.getElementById("texte_id_modify").value = texte_id;
}

function deleteExercice(id,idUser) {
 if (confirm("Voulez-vous vraiment supprimer cet exercice ?")) {
     window.location.href = `../../pages/contenus/crud/exercice/delete_exercice.php?id=${id}&user=${idUser}`;
 }
}