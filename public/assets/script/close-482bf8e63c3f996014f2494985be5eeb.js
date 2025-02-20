document.addEventListener("DOMContentLoaded", function () {
    let navbarCollapse = document.getElementById("navbarAdmin");
    let burgerButton = document.querySelector("#navAdmin .navbar-toggler");
    let navLinks = document.querySelectorAll("#navAdmin .btnRegister");

    // Fermer le menu après un clic sur un lien en mode mobile
    navLinks.forEach(function (link) {
        link.addEventListener("click", function () {
            if (window.innerWidth < 992 && navbarCollapse.classList.contains("show")) {
                let bsCollapse = new bootstrap.Collapse(navbarCollapse, { toggle: false });
                bsCollapse.hide();
            }
        });
    });

    // Fermer le menu si on reclique sur le bouton burger alors que le menu est ouvert
    burgerButton.addEventListener("click", function () {
        if (navbarCollapse.classList.contains("show")) {
            let bsCollapse = new bootstrap.Collapse(navbarCollapse, { toggle: false });
            bsCollapse.hide();
        }
    });
});
