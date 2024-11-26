document.addEventListener('DOMContentLoaded', function () {
    // Cibler les étoiles du modal uniquement (ne pas affecter celles des avis existants)
    const modalStars = document.querySelectorAll('#exampleModal .star-rating .star');
    const ratingInput = document.getElementById('rating-value');
    
    // Vérifier si on est bien dans le modal de création d'avis
    if (modalStars.length > 0) {
        modalStars.forEach(star => {
            star.addEventListener('click', function () {
                const rating = this.getAttribute('data-value');
                
                // Mettre à jour la valeur du champ caché
                ratingInput.value = rating;

                // Mettre à jour l'affichage des étoiles
                modalStars.forEach(s => s.classList.remove('selected'));
                this.classList.add('selected');
                let prev = this.previousElementSibling;
                while (prev) {
                    prev.classList.add('selected');
                    prev = prev.previousElementSibling;
                }
            });

            star.addEventListener('mouseover', function () {
                // Survol pour montrer les étoiles temporairement
                modalStars.forEach(s => s.classList.remove('selected'));
                let prev = this;
                while (prev) {
                    prev.classList.add('selected');
                    prev = prev.previousElementSibling;
                }
            });

            star.addEventListener('mouseout', function () {
                // Remettre l'état à celui enregistré dans l'input
                const currentRating = ratingInput.value;
                modalStars.forEach(s => s.classList.remove('selected'));
                if (currentRating) {
                    modalStars.forEach(s => {
                        if (s.getAttribute('data-value') <= currentRating) {
                            s.classList.add('selected');
                        }
                    });
                }
            });
        });
    }
});
