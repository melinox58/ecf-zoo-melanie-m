document.querySelectorAll('.btn-detail').forEach(button => {
    button.addEventListener('click', function() {
        const animalId = this.getAttribute('data-id');

        // Utiliser fetch pour envoyer le nombre de clics
        fetch(`/animal/${animalId}/increment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            console.log('Nouveau nombre de clics pour l\'animal :', data.clicks);
            // Tu peux mettre à jour l'interface avec le nouveau nombre de clics si nécessaire
        })
        .catch(error => {
            console.error('Erreur lors de l\'incrémentation des clics :', error);
        });
    });
});
