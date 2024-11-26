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

    // Réinitialisation du formulaire et des étoiles après soumission
    form.addEventListener('submit', function (event) {
        event.preventDefault();  // Empêche la soumission classique du formulaire

        // Envoyer le formulaire via fetch ou AJAX ici si nécessaire

        // Réinitialisation des étoiles et du formulaire
        setTimeout(function () {
            stars.forEach(s => s.classList.remove('selected'));
            ratingInput.value = '';  // Réinitialiser la valeur cachée
            currentRating = 0;

            // Optionnel : Fermer le modal après l'envoi
            modal.hide();
            form.reset();  // Réinitialise tous les champs du formulaire

            // Rechargement automatique de la page pour réinitialiser le JavaScript
            location.reload();  // Rechargement de la page
        }, 500);  // Délai de 500ms avant réinitialisation (si vous avez une animation)
    });
});
