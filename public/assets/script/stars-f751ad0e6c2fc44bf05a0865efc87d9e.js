document.addEventListener('DOMContentLoaded', function () {
    const stars = document.querySelectorAll('.star-rating .star');
    const ratingInput = document.getElementById('rating-value');
    const form = document.querySelector('form');
    const modal = new bootstrap.Modal(document.getElementById('exampleModal')); // Modal Bootstrap

    stars.forEach(star => {
        star.addEventListener('click', function () {
            const rating = this.getAttribute('data-value');
            
            // Mettre à jour la valeur du champ caché
            ratingInput.value = rating;

            // Mettre à jour l'affichage des étoiles
            stars.forEach(s => s.classList.remove('selected'));
            this.classList.add('selected');
            let prev = this.previousElementSibling;
            while (prev) {
                prev.classList.add('selected');
                prev = prev.previousElementSibling;
            }
        });

        star.addEventListener('mouseover', function () {
            // Survol pour montrer les étoiles temporairement
            stars.forEach(s => s.classList.remove('selected'));
            let prev = this;
            while (prev) {
                prev.classList.add('selected');
                prev = prev.previousElementSibling;
            }
        });

        star.addEventListener('mouseout', function () {
            // Remettre l'état à celui enregistré dans l'input
            const currentRating = ratingInput.value;
            stars.forEach(s => s.classList.remove('selected'));
            if (currentRating) {
                stars.forEach(s => {
                    if (s.getAttribute('data-value') <= currentRating) {
                        s.classList.add('selected');
                    }
                });
            }
        });
    });

    // Utilisation de AJAX pour envoyer le formulaire sans recharger la page
    form.addEventListener('submit', function(event) {
        event.preventDefault();  // Empêcher la soumission classique

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())  // Assume response is in JSON format
        .then(data => {
            // Réinitialiser la sélection des étoiles et fermer le modal
            stars.forEach(s => s.classList.remove('selected'));
            ratingInput.value = '';  // Effacer la valeur cachée

            // Vous pouvez traiter ici la réponse du serveur si nécessaire
            if (data.success) {
                modal.hide();  // Fermer le modal après l'envoi
                alert('Merci pour votre avis !');
            } else {
                alert('Une erreur est survenue, essayez à nouveau.');
            }
        })
        .catch(error => {
            alert('Une erreur est survenue lors de l\'envoi.');
            console.error(error);
        });
    });
});
