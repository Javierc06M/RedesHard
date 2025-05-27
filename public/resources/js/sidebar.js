document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById("sidebar");
    const sidebarToggle = document.getElementById("sidebarToggle");
    const profileDropdown = document.getElementById("profileDropdown");
    const profileMenu = profileDropdown?.querySelector(".sa-profile__menu");

    // Crear overlay dinámicamente para cerrar sidebar en móviles
    let overlay = document.createElement("div");
    overlay.classList.add("sa-overlay");
    document.body.appendChild(overlay);

    // Abrir/cerrar sidebar para móviles
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener("click", function (e) {
            e.stopPropagation(); // Prevenir cierre inmediato
            sidebar.classList.toggle("sa-sidebar--mobile-open");
            overlay.classList.toggle("active");

            document.body.style.overflow = overlay.classList.contains("active") ? "hidden" : "";
        });

        // Cerrar sidebar si se hace clic en overlay
        overlay.addEventListener("click", function () {
            sidebar.classList.remove("sa-sidebar--mobile-open");
            overlay.classList.remove("active");
            document.body.style.overflow = "";
        });

        // Cerrar sidebar si se hace clic fuera del mismo
        document.addEventListener("click", function (e) {
            if (
                sidebar.classList.contains("sa-sidebar--mobile-open") &&
                !sidebar.contains(e.target) &&
                !sidebarToggle.contains(e.target)
            ) {
                sidebar.classList.remove("sa-sidebar--mobile-open");
                overlay.classList.remove("active");
                document.body.style.overflow = "";
            }
        });
    }

    // PERFIL: mostrar/ocultar menú con animaciones
    if (profileDropdown && profileMenu) {
        let isMenuOpen = false;

        profileDropdown.addEventListener("click", function (e) {
            e.stopPropagation();

            if (!isMenuOpen) {
                profileMenu.classList.remove("sa-menu--hiding");
                profileMenu.classList.add("sa-menu--visible");
                isMenuOpen = true;
            } else {
                profileMenu.classList.remove("sa-menu--visible");
                profileMenu.classList.add("sa-menu--hiding");

                setTimeout(() => {
                    profileMenu.classList.remove("sa-menu--hiding");
                    isMenuOpen = false;
                }, 200);
            }
        });

        document.addEventListener("click", function (e) {
            if (isMenuOpen && !profileDropdown.contains(e.target)) {
                profileMenu.classList.remove("sa-menu--visible");
                profileMenu.classList.add("sa-menu--hiding");

                setTimeout(() => {
                    profileMenu.classList.remove("sa-menu--hiding");
                    isMenuOpen = false;
                }, 200);
            }
        });
    }
});
