document.addEventListener('DOMContentLoaded', () => {
    const closedCheckboxes = document.querySelectorAll('input[type="checkbox"][name^="closed"]');

    closedCheckboxes.forEach((checkbox) => {
        // Appeler la fonction au chargement de la page pour vérifier l'état initial
        toggleTimeFields(checkbox);

        // Écouter le changement de chaque checkbox
        checkbox.addEventListener('change', () => {
            toggleTimeFields(checkbox);
        });
    });

    function toggleTimeFields(checkbox) {
        const dayIndex = checkbox.name.match(/\d+/)[0]; // Récupérer l'index du jour
        const openField = document.querySelector(`input[name="entries[${dayIndex}][open]"]`);
        const closeField = document.querySelector(`input[name="entries[${dayIndex}][close]"]`);

        // Vérifier si les champs open et close existent avant d'effectuer des actions
        if (openField && closeField) {
            if (checkbox.checked) {
                openField.disabled = true;
                closeField.disabled = true;
                openField.value = ''; // Efface la saisie de l'utilisateur
                closeField.value = '';
            } else {
                openField.disabled = false;
                closeField.disabled = false;
            }
        }
    }
});
