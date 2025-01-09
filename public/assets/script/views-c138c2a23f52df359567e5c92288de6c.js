// Écouter tous les boutons qui ouvrent des modals
document.querySelectorAll('.btn-primary').forEach(button => {
    button.addEventListener('click', function(event) {
        const animalId = this.getAttribute('data-id');
        
        // Sélecteur dynamique du modal en fonction de l'ID de l'animal
        const modal = document.querySelector(`#modal-${animalId}`);
        
        // Si le modal existe, on l'affiche
        if (modal) {
            // Exemple : incrémentation d'un compteur
            let clickCounter = modal.getAttribute('data-clicks') || 0;
            clickCounter++;
            modal.setAttribute('data-clicks', clickCounter);

            // Afficher le compteur dans la console
            console.log(`Modal ${animalId} a été cliqué ${clickCounter} fois`);

            // Ouvrir le modal (si tu utilises Bootstrap)
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
        }
    });
});
