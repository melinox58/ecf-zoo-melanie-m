document.addEventListener('DOMContentLoaded', function () {
    const stars = document.querySelectorAll('.star-rating .star');
    const ratingInput = document.getElementById('rating-value');
    const form = document.querySelector('form');
    const modal = new bootstrap.Modal(document.getElementById('exampleModal')); // Modal Bootstrap
    let currentRating = 0;  // Pour garder la note actuelle

    // Mettre à jour l'affichage des étoiles et la valeur cachée lors du clic
    stars.forEach(star => {
        star.addEventListener('click', function () {
            currentRating = this.getAttribute('data-value');
            ratingInput.value = currentRating;

            stars.forEach(s => s.classList.remove('selected'));
            this.classList.add('selected');
            let prev = this.previousElementSibling;
            while (prev) {
                prev.classList.add('selected');
                prev = prev.previousElementSibling;
            }
        });

        // Affichage temporaire lors du survol
        star.addEventListener('mouseover', function () {
            stars.forEach(s => s.classList.remove('selected'));
            let prev = this;
            while (prev) {
                prev.classList.add('selected');
                prev = prev.previousElementSibling;
            }
        });

        // Restauration de l'état actuel après la sortie du survol
        star.addEventListener('mouseout', function () {
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

    // Soumission du formulaire avec AJAX
    form.addEventListener('submit', function (event) {
        event.preventDefault();  // Empêche la soumission classique du formulaire

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Si la soumission réussit, réinitialisez les étoiles et fermez le modal
            stars.forEach(s => s.classList.remove('selected'));
            ratingInput.value = '';  // Réinitialiser la valeur cachée
            currentRating = 0;
            modal.hide();  // Fermer le modal

            // Afficher un message flash (par exemple : succès)
            const flashMessage = document.createElement('div');
            flashMessage.className = 'alert alert-success';
            flashMessage.innerText = 'Avis envoyé avec succès !';
            document.body.appendChild(flashMessage);  // Ajouter le message flash à la page

            // Faire disparaître le message flash après 3 secondes
            setTimeout(function () {
                flashMessage.style.display = 'none';  // Masquer le message flash
                // Recharger la page après le délai pour réinitialiser le JS
                location.reload();  // Rechargement de la page
            }, 3000);  // Délai de 3 secondes
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue.');
        });
    });
});
