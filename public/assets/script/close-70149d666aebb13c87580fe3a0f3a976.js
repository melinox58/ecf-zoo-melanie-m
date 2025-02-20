document.addEventListener("DOMContentLoaded", function () {
    let navbarMain = document.getElementById("navbarNavAltMarkup");
    let navbarAdmin = document.getElementById("navbarAdmin");
    
    let burgerMain = document.querySelector(".navbar-toggler[data-bs-target='#navbarNavAltMarkup']");
    let burgerAdmin = document.querySelector(".navbar-toggler[data-bs-target='#navbarAdmin']");
    
    let navLinksMain = document.querySelectorAll("#navbarNavAltMarkup .nav-link");
    let navLinksAdmin = document.querySelectorAll("#navbarAdmin .btnRegister");

    function closeNavbar(navbar) {
        if (navbar.classList.contains("show")) {
            let bsCollapse = new bootstrap.Collapse(navbar, { toggle: false });
            bsCollapse.hide();
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

    // Fermer le menu principal si on reclique sur le bouton burger (mobile)
    if (burgerMain) {
        burgerMain.addEventListener("click", function () {
            closeNavbar(navbarMain);
        });
    }

    // Fermer le menu admin si on reclique sur le bouton burger (mobile)
    if (burgerAdmin) {
        burgerAdmin.addEventListener("click", function () {
            closeNavbar(navbarAdmin);
        });
    }
});
