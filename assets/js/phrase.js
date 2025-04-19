// Fonctions JavaScript
function toggleForm() {
 document.getElementById("form-container").classList.toggle("show");
}

function toggleFormUpdate() {
 document.getElementById("form-container-modify").classList.toggle("show");
}

function editPhrase(id, libelle, indication, reponse, exercice_id) {
 document.getElementById("id_phrase_modify").value = id;
 document.getElementById("libelle_phrase_modify").value = libelle;
 document.getElementById("indication_modify").value = indication;
 document.getElementById("reponse_modify").value = reponse;
 document.getElementById("exercice_id_modify").value = exercice_id;
 toggleFormUpdate();
}

function deletePhrase(id, idUser) {
    if (confirm("Voulez-vous vraiment supprimer cette phrase à trou ?")) {
        window.location.href = `../../pages/contenus/crud/phrase/delete_phrase.php?id=${id}&user=${idUser}`;
    }
}

// Validation pour s'assurer qu'il y a bien un _ dans la phrase
document.addEventListener('DOMContentLoaded', function() {
 const formAdd = document.querySelector('#form-container form');
 const formModify = document.querySelector('#form-container-modify form');
 
 if(formAdd) {
     formAdd.addEventListener('submit', function(e) {
         const phrase = document.getElementById('libelle_phrase').value;
         if(!phrase.includes('_')) {
             alert("La phrase doit contenir un _ pour indiquer où se trouve le trou!");
             e.preventDefault();
         }
     });
 }
 
 if(formModify) {
     formModify.addEventListener('submit', function(e) {
         const phrase = document.getElementById('libelle_phrase_modify').value;
         if(!phrase.includes('_')) {
             alert("La phrase doit contenir un _ pour indiquer où se trouve le trou!");
             e.preventDefault();
         }
     });
 }
});