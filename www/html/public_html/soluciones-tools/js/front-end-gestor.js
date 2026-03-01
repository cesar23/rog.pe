// Versión del gestor de backend
const VERSION_FRONT_GESTOR = '1.0.6';

// Desestructuración de la URL actual para obtener las partes clave
const { host, hostname, href, origin, pathname, port, protocol, search } = window.location;

// Definición de la ruta actual utilizando pathname y search
const ruta = `${pathname}${search}`; // Ejemplo: /wp-admin/edit.php/?post_type=blocks



const showButtonPanelAdmin = async ()=> {
    // =============================================
    // 2.Agregar el boton por  javascript
    // =============================================

    // Crear el div para el enlace de regreso
    const adminBackLink = document.createElement("div");
    adminBackLink.id = "admin-back-link"; // Asignar un ID al div

    // Crear el ícono utilizando una etiqueta <i> (se puede usar Font Awesome u otra librería de íconos)
    const icon = document.createElement("i");
    icon.className = "fas fa-arrow-left"; // Asignar la clase para el ícono de Font Awesome (asegúrate de incluir Font Awesome en tu proyecto)

    // Crear el enlace dentro del div
    const link = document.createElement("a");
    link.href = "/wp-admin"; // Enlace que lleva al panel de administración de WordPress
    link.textContent = "Ir al panel admin"; // Texto del enlace

    // Agregar el ícono y el enlace dentro del div
    adminBackLink.appendChild(icon); // Agregar el ícono
    adminBackLink.appendChild(link); // Agregar el enlace

    // Finalmente, agregar el div al cuerpo del documento
    document.body.appendChild(adminBackLink);

}


// Inicializa el script cuando el DOM está listo
document.addEventListener("DOMContentLoaded", async function () {
    console.log(`VERSION_FRONT_GESTOR : ${VERSION_FRONT_GESTOR}`);

    try {
        // si no encontramos la  clase wp-admin que esta en el backend del usuario mostraremos el boton
        if (!document.querySelector('.wp-admin')){
            await showButtonPanelAdmin();
        }


    } catch (e) {
        console.error('#####################################');
        console.error('############## Error ################');
        console.error('### file: front-end-gestor.js ########');
        console.error('#####################################');
        console.error(e);
    }
});
