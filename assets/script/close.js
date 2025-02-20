document.addEventListener("DOMContentLoaded", function () {
    let navLinks = document.querySelectorAll("#navAdmin .btnRegister");
    let navbarCollapse = document.getElementById("navbarAdmin");

    navLinks.forEach(function (link) {
        link.addEventListener("click", function () {
            if (window.innerWidth < 992) { // Vérifie si on est en mode mobile
                let bsCollapse = new bootstrap.Collapse(navbarCollapse);
                bsCollapse.hide();
            }
        });
    });
});
