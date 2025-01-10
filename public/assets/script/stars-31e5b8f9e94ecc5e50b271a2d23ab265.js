document.addEventListener('DOMContentLoaded', function () {
    const stars = document.querySelectorAll('.star-rating .star');
    const ratingInput = document.getElementById('rating-value');

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

    form.addEventListener('submit', function() {
        // Réinitialiser les étoiles après l'envoi du formulaire (même sans AJAX)
        setTimeout(function() {
            stars.forEach(s => s.classList.remove('selected'));
            ratingInput.value = '';  // Effacer la valeur cachée
        }, 1000);  // Délai avant réinitialisation, si nécessaire
    });
});