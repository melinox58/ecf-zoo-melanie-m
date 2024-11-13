document.addEventListener('DOMContentLoaded', function () {
    // Récupère toutes les cases à cocher pour "Jour de fermeture"
    const closedCheckboxes = document.querySelectorAll('input[type="checkbox"][name^="closed"]');

    // Fonction pour cacher ou afficher les champs en fonction de l'état de la case à cocher
    function toggleScheduleVisibility(checkbox) {
        const entryDiv = checkbox.closest('div'); // Trouve le parent div qui contient le jour et les horaires
        const openInput = entryDiv.querySelector('input[name^="entries"][name$="[open]"]');
        const closeInput = entryDiv.querySelector('input[name^="entries"][name$="[close]"]');

        // Si la case est cochée, on masque les champs horaires
        if (checkbox.checked) {
            openInput.disabled = true; // Désactive les champs d'horaires
            closeInput.disabled = true;
            openInput.style.display = 'none'; // Masque visuellement les champs
            closeInput.style.display = 'none';
        } else {
            openInput.disabled = false; // Active les champs d'horaires
            closeInput.disabled = false;
            openInput.style.display = ''; // Affiche les champs
            closeInput.style.display = '';
        }
    }

    // Initialise l'état de chaque case à cocher et des champs associés
    closedCheckboxes.forEach(function(checkbox) {
        // Appel de la fonction pour mettre à jour l'affichage en fonction de l'état initial
        toggleScheduleVisibility(checkbox);

        // Ajoute un écouteur d'événement pour chaque case à cocher
        checkbox.addEventListener('change', function() {
            toggleScheduleVisibility(checkbox);
        });
    });
});
