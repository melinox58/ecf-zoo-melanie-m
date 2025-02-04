// Écouter tous les boutons qui ouvrent des modals
document.querySelectorAll('.btn-primary').forEach(button => {
    button.addEventListener('click', function(event) {
        const animalId = this.getAttribute('data-id');
        const habitat = this.getAttribute('data-habitat');  // Récupère l'habitat (jungle, marais ou savane)

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

        // Envoi AJAX pour incrémenter les clics dans la base de données
        fetch(`/${habitat}/${animalId}/increment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: animalId })
        })
        .then(response => response.text())  // Utiliser text() pour vérifier la réponse brute
        .then(data => {
            console.log('Réponse brute du serveur:', data);  // Afficher la réponse brute

            try {
                const jsonData = JSON.parse(data);  // Essayer de parser manuellement
                if (jsonData.clicks) {
                    console.log(`L'animal ${animalId} a maintenant ${jsonData.clicks} clics.`);
                } else {
                    console.error('Erreur de récupération des clics.');
                }
            } catch (e) {
                console.error('Erreur de parsing JSON:', e);
            }
        })
        .catch(error => {
            console.error('Erreur AJAX:', error);
        });
    });
});
