window.onload = () => {

    let btn_ajout = document.getElementById("add_image");

    btn_ajout.addEventListener("click", function(e){
        // On submit le formulaire ayant pour nom "form"

        // On vérifie que l'input de type file n'est pas vide

        // On vérifie que l'input de type File n°1 ou n°2 n'est pas vide
        // N°1 : input ayant pour nom "image"
        // N°2 : input ayant pour nom "image2"

        if (document.getElementById("produit_images").value == "" && document.getElementById("produit_documentsProduit").value == "") {
                alert("Veuillez sélectionner une image");
            }
        else
        {
            document.querySelector("form[name=produit]").submit();
        }
      
    });













    // Gestion des boutons "Supprimer"
    let links = document.querySelectorAll("[data-delete]")
    
    // On boucle sur links
    for(link of links){
        // On écoute le clic
        link.addEventListener("click", function(e){
            // On empêche la navigation
            e.preventDefault()

            // On demande confirmation
            if(confirm("Voulez-vous supprimer le média ? Il va être supprimé définitivement.")){
                // On envoie une requête Ajax vers le href du lien avec la méthode DELETE
                fetch(this.getAttribute("href"), {
                    method: "DELETE",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({"_token": this.dataset.token})
                }).then(
                    // On récupère la réponse en JSON
                    response => response.json()
                ).then(data => {
                    if(data.success)
                        this.parentElement.remove()
                    else
                        alert(data.error)
                }).catch(e => alert(e))
            }
        })
    }


    

}