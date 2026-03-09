// Versión del gestor de backend
const VERSION_BACKEND_GESTOR = '2.9.2';

// Desestructuración de la URL actual
const {
    host, hostname, href, origin, pathname, port, protocol, search
} = window.location;

// Definición de la ruta actual
const ruta = `${pathname}/${search}`; // Ejemplo: /wp-admin/edit.php/?post_type=blocks

/**
 * Función para pausar la ejecución durante un número específico de segundos
 * @param {number} seconds - Número de segundos para pausar
 * @return {Promise} Promesa que se resuelve después de los segundos especificados
 * @example
 * await sleep(2); // Pausa por 2 segundos
 */
const sleep = (seconds) => {
    return new Promise(resolve => setTimeout(resolve, seconds * 1000));
};

/**
 * Función para cargar scripts externos de manera asíncrona
 * @param {string} url - URL del script que se va a cargar
 * @return {Promise} Promesa que indica cuándo el script ha sido cargado
 * @example
 * await loadExternalScript('https://example.com/script.js');
 */
const loadExternalScript = (url) => {
    return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = url;
        script.type = 'text/javascript';
        script.async = true;

        // Resolver la promesa cuando el script ha sido cargado
        script.onload = function () {
            console.log(`El archivo ${url} ha sido cargado.`);
            resolve();
        };

        // Rechazar la promesa si ocurre un error durante la carga
        script.onerror = function () {
            console.error(`Error al cargar el script ${url}.`);
            reject(new Error(`Script load error for ${url}`));
        };

        document.head.appendChild(script);
    });
};
/**
 * Listado de URLs permitidas
 * Las URLs que no estén en este listado serán bloqueadas
 */
const pagesAllow = [
    '/wp-admin/index.php',
    '/wp-admin/media-new.php',
    // ------ Woocommerce
    '/wp-admin/edit.php?post_type=shop_order',
    '/wp-admin/post-new.php?post_type=shop_order',
    '/wp-admin/admin.php?page=wc-admin&path=%2Fcustomers',
    '/wp-admin/admin.php?page=wc-reports',
    '/wp-admin/edit.php?post_type=product',
    '/wp-admin/post-new.php?post_type=product',
    '/wp-admin/post.php?post=',
    '/wp-admin/edit.php?post_status=',
    '/wp-admin/edit.php?yoast_filter=orphaned&post_type=product',
    '/wp-admin/edit.php?yoast_filter=stale-cornerstone-content&post_type=product',
    '/wp-admin/edit.php?yoast_filter=cornerstone&post_type=product',
    // ------- Woocommerce (marcas)
    '/wp-admin/edit-tags.php?taxonomy=pwb-brand&post_type=product',
    '/wp-admin/term.php?taxonomy=pwb-brand',
    '/wp-admin/edit.php?pwb-brand=',
    // ------- Woocommerce (categorías)
    '/wp-admin/edit-tags.php?taxonomy=product_cat',
    '/wp-admin/term.php?taxonomy=product_cat',
    '/wp-admin/edit.php?product_cat=',
    // ------- Woocommerce (etiquetas)
    '/wp-admin/edit-tags.php?taxonomy=product_tag',
    '/wp-admin/term.php?taxonomy=product_tag',
    // ------- Woocommerce (productos)
    '/wp-admin/edit.php?post_type=product',
    '/wp-admin/term.php?taxonomy=product_tag',
    // Subidas de WordPress
    '/wp-admin/upload.php',
    '/wp-admin/upload.php?page=add-external-media-without-import',
    // '/wp-admin/admin.php?page=solu-currencies-exchange',
];

/**
 * Listado de bloques permitidos para el usuario tipo gestor
 */
const titlesEnables = ["Slider"];

/**
 * ID de elementos del menú que solo deben aparecer para el usuario tipo gestor
 */
const li_activos_menu = ["menu-media", "menu-posts-product", "menu-dashboard", "toplevel_page_woocommerce","menu-posts-blocks","toplevel_page_solu-currencies-exchange"];
//const li_activos_menu = ["menu-media", "menu-posts-product", "menu-dashboard", "menu-posts-blocks", "toplevel_page_woocommerce","toplevel_page_solu-product-logs"];

/**
 * links del submenu de UX BLOCK de Flatsome que no deben aparecer
 */
const li_activos_menu_uxblock=[
    "post-new.php?post_type=blocks",
    "edit-tags.php?taxonomy=block_categories&post_type=blocks"
    ];


/**
 * links del submenu de WOOCOMERCE que no deben aparecer
 */
const li_activos_menu_submenu_woocomerce=[
    "admin.php?page=coupons-moved",
    "admin.php?page=checkout_form_designer",
    "admin.php?page=wc-settings",
    "admin.php?page=wc-admin&path=%2Fextensions",
    ];

/**
 * links del submenu de WOOCOMERCE Productos que no deben aparecer
 */
const li_activos_menu_submenu_products =[
    "edit.php?post_type=product&page=product_attributes",
    "edit.php?post_type=product&page=product-reviews"
    ];


/**
 * ID de elementos del navmenu que solo deben aparecer para el usuario tipo gestor
 */
const li_activos_nav = [
    "wp-admin-bar-site-name",
    "menu-posts-product",
    "menu-dashboard",
    "wp-admin-bar-view-site",
    "wp-admin-bar-view-store",
    "wp-admin-bar-my-account",
    "wp-admin-bar-litespeed-menu"
];

/**
 * Paneles activos en el dashboard
 */
const panel_activos_1 = ["custom_dashboard_widget", "dashboard_widget"];
const panel_activos_2 = ["otro"];
const paneles_activos = [...panel_activos_1, ...panel_activos_2];

/**
 * Verifica si la ruta actual coincide con el path dado.
 * @param {string} path - Ruta a verificar
 * @return {boolean} - true si la URL actual contiene el path
 * @example
 * isCurrentPathUrl('/wp-admin/edit.php'); // Verifica si estás en la página de edición
 */
const isCurrentPathUrl = (path) => {
    return ruta.indexOf(path) !== -1;
};

/**
 * Busca en el array si la URL está permitida.
 * @param {Array} arrayList - Lista de URLs permitidas
 * @param {string} rutaPath - Ruta a buscar
 * @return {boolean} - true si la URL está permitida
 */
const searchPage = (arrayList, rutaPath) => {
    let res = false;
    arrayList.forEach((e) => {
        if (e.indexOf(rutaPath) !== -1) res = true;
    });
    return res;
};

/**
 * Reemplaza texto en el contenido HTML.
 * @param {string} textSearch - Texto a buscar
 * @param {string} textReplace - Texto de reemplazo
 */
const replaceTextContentHtml = (textSearch, textReplace) => {
    let allElements = document.body.querySelectorAll("*");
    for (const element of allElements) {
        let tagNameElement = element.tagName.toLowerCase();
        if (['script', 'style'].includes(tagNameElement)) continue;

        if (element.textContent.includes(textSearch) && element.childNodes.length === 1) {
            element.innerText = element.innerText.replace(textSearch, textReplace);
        }
    }
};

/**
 * Controlador de páginas para el usuario.
 * Define qué acciones ejecutar dependiendo de la página.
 */
class PagesUserController {
    /**
     * Oculta los bloques que no están permitidos para el usuario tipo gestor.
     */
    static async pagePostTypeBlock() {
        document.getElementById('the-list').querySelectorAll('tr').forEach((ele) => {
            let title = ele.querySelector('strong').textContent;
            if (!titlesEnables.includes(title)) {
                ele.style.display = 'none';
            }
        });

        const addButton = document.querySelector('#wpbody-content > div.wrap > a');
        if (addButton) {
            addButton.style.display = 'none';
        }

        hideElement(document.querySelector('div.wrap > ul'));
    }

    /**
     * Personaliza la página de inicio de WooCommerce.
     */
    static async pageWooComerce() {
        const css = `
        .woocommerce-homescreen .woocommerce-homescreen-column:first-child {
            display: none;
        }
        .wrap.woocommerce .notice {
            background-color:red;
        }
        #message {
            display: none;
        }
        `;

        const style = document.createElement('style');
        style.type = 'text/css';
        style.id = 'pageWooComerce';
        if (style.styleSheet) {
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }

        document.head.appendChild(style);
    }

    /**
     * Personaliza la página de usuarios.
     */
    static async pageAdminUser() {
        const wpbody = document.getElementById('wpbody');
        if (wpbody) {
            wpbody.innerHTML = PagesUserController.pageMessage(`Página de Usuario`);
        }

        document.getElementById('wpbody-content').querySelectorAll('tr').forEach((ele) => {
            let title = ele.querySelector('strong').textContent;
            if (!titlesEnables.includes(title)) {
                ele.style.display = 'none';
            }
        });
    }

    /**
     * Personaliza la edición de usuario, deshabilitando el cambio de rol.
     */
    static async pageAdminEditUser() {
        const roleElement = document.getElementById('role');
        if (roleElement) {
            roleElement.disabled = true;
            roleElement.insertAdjacentHTML('afterend', `<div> Deshabilitado por seguridad</div>`);
        }
    }

    /**
     * Bloquea la página actual si no está permitida.
     */
    static async blockedPage() {
        const wpbody = document.getElementById('wpbody');
        if (wpbody) {
            wpbody.innerHTML = PagesUserController.pageMessage(`😥 Esta página está bloqueada`);
        }
    }

    /**
     * Muestra un mensaje cuando una página está bloqueada.
     * @param {string} namePage - Nombre de la página bloqueada
     * @return {string} - HTML con el mensaje de error
     */
    static pageMessage(namePage) {
        return `
        <div id="wpbody" role="main">
            <div id="wpbody-content">
                <div class="wrap">
                    <h1 class="wp-heading-inline">${namePage}</h1>
                    <div class="notice wa-order-notice-dismissible notice-warning is-dismissible">
                        <strong style="color: red;">¡Importante!</strong> La página <strong>${window.location.href}</strong> no está disponible. ¡Contacta con el administrador del servidor!
                        <button type="button" class="notice-dismiss"><span class="screen-reader-text">Descartar este aviso.</span></button>
                    </div>
                </div>
            </div>
        </div>
        `;
    }
}

/**
 * Oculta un elemento del DOM.
 * @param {HTMLElement} element - Elemento a ocultar
 */
const hideElement = (element) => {
    if (element) {
        element.style.display = "none";
    }
};

/**
 * Muestra un elemento del DOM.
 * @param {HTMLElement} element - Elemento a mostrar
 */
const showElement = (element) => {
    if (element) {
        element.style.display = "block";
    }
};

/**
 * Oculta elementos del panel del dashboard
 * @param {Array} paneles_activos Paneles que deben estar activos
 * @param {HTMLElement} containerElement Contenedor del panel
 */
const dashboardWidgetHideElement = async (paneles_activos, containerElement) => {
    if (document.querySelectorAll("#dashboard-widgets").length && containerElement) {
        let elements = containerElement.querySelectorAll('.postbox ')
        elements.forEach(element => {
            const id = element.getAttribute("id");
            element.style.display = "none";
            if (id && paneles_activos.includes(id)) {
                document.getElementById(id).style.display = "block";
            }
        });
    }
};

const hideSubMenu = async (elemento, array_names = []) => {
    // Seleccionamos todos los elementos 'li' dentro del 'wp-submenu'
    const submenuItems = elemento.querySelectorAll('.wp-submenu li');

    submenuItems.forEach(item => {
        // Verificamos si el texto del 'li' contiene alguna de las palabras en el array_names
        const shouldHide = array_names.some(name => item.textContent.includes(name));

        // Si no contiene ninguna de las palabras, lo ocultamos
        if (shouldHide) {
            item.style.display = 'none';
        }
    });
};


/**
 * Oculta elementos del submenú de WordPress basado en la URL (href) del enlace dentro de los elementos 'li'.
 * @param {HTMLElement} elemento - Elemento del menú que contiene los subelementos a ocultar.
 * @param {Array<string>} array_hrefs - Lista de partes de URL que deben coincidir para ocultar elementos.
 */
const hideSubMenuByHref = async (elemento, array_hrefs = []) => {
    // Seleccionamos todos los elementos 'li' dentro del 'wp-submenu'
    const submenuItems = elemento.querySelectorAll('.wp-submenu li');

    submenuItems.forEach(item => {
        // Verificamos si hay un enlace dentro del <li>
        const linkElement = item.querySelector('a');

        if (linkElement) {
            // Extraemos el atributo 'href'
            const href = linkElement.getAttribute('href');

            // Si la URL contiene alguno de los valores en array_hrefs, ocultamos el elemento
            const shouldHide = array_hrefs.some(url => href.includes(url));

            if (shouldHide) {
                item.style.display = 'none';
            }
        }
    });
};



/**
 * Oculta menús y elementos de la barra de navegación.
 */
const hideMenus = async (li_activos_menu, li_activos_nav) => {
    // Oculta o muestra menús dependiendo de la lista de IDs activos
    if (document.querySelector("#adminmenu")) {
        let elements = document.querySelectorAll("#adminmenu li");
        elements.forEach(elemento => {
            let id = elemento.getAttribute("id");
            if (id) {
                if(li_activos_menu.includes(id)){
                    elemento.style.display = "block";
                    switch (id) {
                        case "toplevel_page_woocommerce":
                            //hideSubMenu(elemento,li_activos_menu_submenu_woocomerce)
                            hideSubMenuByHref(elemento,li_activos_menu_submenu_woocomerce)
                            break;
                        case "menu-posts-product":
                            //hideSubMenu(elemento,li_activos_menu_submenu_products)
                            hideSubMenuByHref(elemento,li_activos_menu_submenu_products)
                            break;
                        case "menu-posts-blocks":
                                //hideSubMenu(elemento,li_activos_menu_submenu_products)
                                hideSubMenuByHref(elemento,li_activos_menu_uxblock)
                                break;
                    }
                }else{
                    elemento.style.display = "none";
                }

            }
        });
    }

    if (document.querySelector("#wp-admin-bar-root-default")) {
        let elements = document.querySelectorAll("#wp-admin-bar-root-default>li,#wp-admin-bar-top-secondary>li");
        elements.forEach(elemento => {
            let id = elemento.getAttribute("id");
            elemento.style.display = 'none';
            if (id && li_activos_nav.includes(id)) {
                document.getElementById(id).style.display = "block";
            }
        });
    }
};

/**
 * Configura los paneles del dashboard.
 */
const dashboardPanelDesktop = async (paneles_activos) => {
    await dashboardWidgetHideElement(paneles_activos, document.querySelector("#postbox-container-1"));
    await dashboardWidgetHideElement(paneles_activos, document.querySelector("#postbox-container-2"));
    hideElement(document.querySelector("#postbox-container-3"));
    hideElement(document.querySelector("#postbox-container-4"));
};

/**
 * Personaliza el menú y el dashboard del administrador.
 */
async function customizeMenuDashboard() {
    await hideMenus(li_activos_menu, li_activos_nav);
    await dashboardPanelDesktop(paneles_activos);
    showElement(document.getElementById("wpcontent"));
    showElement(document.getElementById("adminmenuwrap"));

    document.querySelectorAll(`.wrap .notice,.wrap .updated`).forEach(ele => {
        ele.style.display = "none";
    });

    replaceTextContentHtml('UX Blocks', 'Sistema Bloques');

    let links = document.querySelectorAll('a[href="update-core.php"]');
    if (links) {
        links.forEach(ele => {
            if (ele.parentElement) {
                ele.parentElement.classList.add('hidden');
            } else {
                ele.style.display = 'none';
            }
        });
    }
}

/**
 * Inicializa el script cuando el DOM está listo.
 */
const hideWooCommerceActivityPanel = async () => {
    const activityPanel = document.getElementById('woocommerce-activity-panel');
    if (activityPanel) {
        activityPanel.style.display = 'none';
    }
}

const customizeMenuWooComerce = async () => {
    const woocommerceElement = document.getElementById('toplevel_page_woocommerce');

    if (woocommerceElement) {
        const firstItemElement = woocommerceElement.querySelector('.wp-first-item');
        if (firstItemElement) {
            firstItemElement.style.display = 'none';
        }

        const menuNameElement = woocommerceElement.querySelector('.wp-menu-name');
        if (menuNameElement && menuNameElement.textContent === 'WooCommerce') {
            menuNameElement.textContent = 'Mis Pedidos';
        }



        woocommerceElement.querySelectorAll('.wp-submenu li').forEach(li => {
            const liFound=li.querySelector('a[href*="admin.php?page=wc-admin&path=/extensions"]')
            if(liFound){
                liFound.style.display = 'none';
            }
        });

    }
}
const hideMessagesWordpress = async ()=> {
    const css = `
      .notice {
       display:none;
      }
    `;

    const style = document.createElement('style');
    style.type = 'text/css';
    style.id = 'back-end-gestor'; // Agrega un id al elemento style
    if (style.styleSheet) {
        style.styleSheet.cssText = css;
    } else {
        style.appendChild(document.createTextNode(css));
    }

    document.head.appendChild(style);
}


const allPages = async () => {
    await hideMessagesWordpress();
    await hideWooCommerceActivityPanel();
    await customizeMenuWooComerce();
}

/**
 * Función para esperar a que todas las llamadas AJAX hayan terminado
 */
const waitForAjaxCompletion = async () => {
    return new Promise(resolve => {
        const checkAjaxComplete = () => {
            if (window.jQuery && window.jQuery.active === 0) {
                resolve();
            } else {
                setTimeout(checkAjaxComplete, 100); // Vuelve a comprobar después de 100ms
            }
        };
        checkAjaxComplete();
    });
}

// Inicializa el script cuando el DOM está listo
document.addEventListener("DOMContentLoaded", async function () {
    console.log(`VERSION_BACKEND_GESTOR : ${VERSION_BACKEND_GESTOR}`);

    // Agrega el texto de carga al HTML
    const loadingIconContainer = document.createElement('div');
    loadingIconContainer.id = 'loading-icon-container';
    loadingIconContainer.innerHTML = `
        <div id="loading-text">Soluciones System...</div>
    `;
    document.body.appendChild(loadingIconContainer);
    await sleep(1)

    // Cargar un script externo antes de continuar con el resto del código
    await loadExternalScript(`${origin}/soluciones-tools/js/gestor/utils.js`);
    let dia=getCurrentDayName()
    console.log(`getCurrentDayName:${dia}`)

    try {
        
        await customizeMenuDashboard();
        await allPages();

        if (isCurrentPathUrl('/wp-admin/edit.php') && isCurrentPathUrl('post_type=blocks')) {
            console.log('isCurrentPathUrl: Página bloques');
            await PagesUserController.pagePostTypeBlock();
        } else if (isCurrentPathUrl('/wp-admin/admin.php') && isCurrentPathUrl('page=wc-admin')) {
            console.log('isCurrentPathUrl: Página inicio Woocommerce');
            await waitForAjaxCompletion(); // Espera a que terminen las llamadas AJAX
            await PagesUserController.pageWooComerce();
        }
        else if (isCurrentPathUrl('/wp-admin/admin.php') && isCurrentPathUrl('page=wc-reports')) {
            console.log('isCurrentPathUrl: Página Woocommerce - reportes');
            await waitForAjaxCompletion(); // Espera a que terminen las llamadas AJAX
            await PagesUserController.pageWooComerce();
        }
        else if (isCurrentPathUrl('/wp-admin/users.php')) {
            await PagesUserController.pageAdminUser();
        } else if (isCurrentPathUrl('/wp-admin/user-edit.php')) {
            await PagesUserController.pageAdminEditUser();
        }

        if (searchPage(pagesAllow, ruta)) {
            await PagesUserController.blockedPage();
        }


        // Una vez que todo el script se ha ejecutado correctamente, mostramos el contenido
        // recuerda que el stulo css que lo oculta esta en el template_backend.php (hook admin_head)
        document.body.classList.add('visible'); // Agrega la clase 'visible

    } catch (e) {
        console.error('#####################################');
        console.error('############## Error ################');
        console.error('### file: back-end-gestor.js ########');
        console.error('#####################################');
        console.error(e);
    }
});
