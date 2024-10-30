document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.closed-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const dayIndex = this.dataset.day; // Récupère le jour correspondant
            const openInput = document.getElementById(`open-${dayIndex}`);
            const closeInput = document.getElementById(`close-${dayIndex}`);

            if (this.checked) {
                // Si la case est cochée, désactivez les champs d'heure
                openInput.disabled = true;
                closeInput.disabled = true;

                // Optionnel : Réinitialiser les valeurs
                openInput.value = '';
                closeInput.value = '';
            } else {
                // Si la case n'est pas cochée, activez les champs d'heure
                openInput.disabled = false;
                closeInput.disabled = false;
            }
        });
    });
});