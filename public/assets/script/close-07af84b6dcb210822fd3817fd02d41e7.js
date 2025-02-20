document.addEventListener("DOMContentLoaded", function () {
    let navbarMain = document.getElementById("navbarNavAltMarkup");
    let navbarAdmin = document.getElementById("navbarAdmin");

    let navLinksMain = document.querySelectorAll("#navbarNavAltMarkup .nav-link");
    let navLinksAdmin = document.querySelectorAll("#navbarAdmin .btnRegister");

    function closeNavbar(navbar) {
        if (navbar.classList.contains("show")) {
            let bsCollapse = bootstrap.Collapse.getInstance(navbar);
            if (bsCollapse) {
                bsCollapse.hide();
            }
        }
    }

    // Fermer le menu principal après un clic sur un lien (mobile)
    navLinksMain.forEach(function (link) {
        link.addEventListener("click", function () {
            if (window.innerWidth < 992) {
                closeNavbar(navbarMain);
            }
        });
    });

    // Fermer le menu admin après un clic sur un lien (mobile)
    navLinksAdmin.forEach(function (link) {
        link.addEventListener("click", function () {
            if (window.innerWidth < 992) {
                closeNavbar(navbarAdmin);
            }
        });
    });
});
