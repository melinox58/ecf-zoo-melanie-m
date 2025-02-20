document.addEventListener("DOMContentLoaded", function () {
    let navLinks = document.querySelectorAll("#navAdmin .btnRegister");
    let navbarCollapse = document.getElementById("navbarAdmin");
    let burgerButton = document.querySelector("#navAdmin .navbar-toggler");

    function closeNavbar() {
        if (window.innerWidth < 992) { // Vérifie si on est en mode mobile
            let bsCollapse = new bootstrap.Collapse(navbarCollapse);
            bsCollapse.hide();
        }
    }

    // Fermer le menu après un clic sur un lien
    navLinks.forEach(function (link) {
        link.addEventListener("click", closeNavbar);
    });

    // Fermer le menu après un clic sur le bouton burger (si déjà ouvert)
    burgerButton.addEventListener("click", function () {
        if (navbarCollapse.classList.contains("show")) {
            closeNavbar();
        }
    });
});
