function toggleForm() {
 var form = document.getElementById("form-container");
 form.classList.toggle("show"); // Ajoute ou enlève la classe "show" pour afficher ou masquer le formulaire
}

function toggleFormUpdate() {
 var form = document.getElementById("form-container-modify");
 form.classList.toggle("show");
}

// Fonction pour afficher le formulaire de modification et pré-remplir les champs
function editText(id, nom, contenu) {
 document.getElementById("id-text").value = id;
 document.getElementById("nom-text-modify").value = nom;
 document.getElementById("contenu-text-modify").value = contenu;

 toggleFormUpdate();
}

// Fonction pour confirmer et supprimer 
function deleteText(id, idUser) {
    if (confirm("Voulez-vous vraiment supprimer ce texte ?")) {
        window.location.href = `../../pages/contenus/crud/texte/delete_texte.php?id=${id}&user=${idUser}`;
    }
}