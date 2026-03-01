// Versión del gestor de backend
const VERSION_FRONTEND = '2.9.4';


// Solo ejecutar si estamos en el backend (wp-admin)
console.log('✅ front-end.js: Ejecutando en área de administración');
// Desestructuración de la URL actual
const {
    host, hostname, href, origin, pathname, port, protocol, search
} = window.location;

// Definición de la ruta actual (corregido: sin barra extra)
const ruta = `${pathname}${search}`; // Ejemplo: /wp-admin/edit.php?post_type=blocks

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
        // Verificar si el script ya está cargado
        const existingScript = document.querySelector(`script[src="${url}"]`);
        if (existingScript) {
            resolve();
            return;
        }

        const script = document.createElement('script');
        script.src = url;
        script.type = 'text/javascript';
        script.async = true;

        // Resolver la promesa cuando el script ha sido cargado
        script.onload = function () {
            console.log(`✅ Script cargado: ${url}`);
            resolve();
        };

        // Rechazar la promesa si ocurre un error durante la carga
        script.onerror = function () {
            console.error(`❌ Error al cargar el script: ${url}`);
            reject(new Error(`Script load error for ${url}`));
        };

        document.head.appendChild(script);
    });
};
/**
 * Listado de URLs permitidas
 * Las URLs que no estén en este listado serán bloqueadas
 */
const pagesBlock = [
    '/wp-admin/index.php',
    '/wp-admin/media-new.php',
    // ------ Woocommerce
    '/wp-admin/edit.php?post_type=shop_order',
];


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
    return arrayList.some(e => e.indexOf(rutaPath) !== -1);
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



/*
* ===============================================================
* Seccion para Checkout - YAPE
* ===============================================================
* */

const renderYapeHtml = () => {
    console.log('ejecutado:renderYapeHtml')
    const container = document.getElementById('radio-control-wc-payment-method-options-cheque__content');

    if (container) {
        document.getElementById('radio-control-wc-payment-method-options-cheque__content').innerHTML = `
<div style="background:#742284; color:#fff; border-radius:16px; padding:30px 20px; max-width:800px; width:100%; box-shadow:0 6px 12px rgba(0,0,0,0.15); margin:auto; display:flex; flex-wrap:wrap; gap:20px; justify-content:center;">

    <!-- Columna izquierda -->
    <div style="flex:1 1 280px; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center;">
        <p style="font-size:1rem; line-height:1.6; margin-bottom:20px;">
            Escanea nuestro QR con tu aplicación Yape y realiza tu pago. Envíanos una captura por WhatsApp <strong>902523099</strong> o al correo <strong>cgms@rog.pe</strong>.
        </p>

        <div>
            <img src="https://rog.pe/wp-content/uploads/2025/11/rog_qr_yape.png" alt="QR Yape" style="width:100%; max-width:280px; border-radius:12px; margin-bottom:10px;">
            <div style="font-weight:bold; margin-bottom:25px; font-size:1rem;">Cesar Hugo Escriba Gutierrez</div>
        </div>
    </div>

    <!-- Columna derecha -->
    <div style="flex:1 1 280px; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center;">
        <img src="https://cesar23.github.io/cdn_webs/iconos_png/logo-yape.png" alt="Logo Yape" style="width:100px; margin-bottom:20px;">

        <a href="https://wa.me/51902523099" target="_blank"
           style="display:inline-flex; align-items:center; justify-content:center; gap:10px; background:#f39314; color:#fff; font-weight:bold; padding:14px 22px; border-radius:12px; text-decoration:none; font-size:1rem; margin-top:10px; box-shadow:0 4px 10px rgba(0,0,0,0.15); transition:all 0.3s ease; min-width:280px;">
            <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" style="width:20px; height:20px; vertical-align:middle;">
            <span style="white-space:nowrap;">Enviar comprobante por WhatsApp</span>
        </a>
    </div>

</div>

    
        `
    }

}



/**
 * Controlador de páginas para el usuario.
 * Define qué acciones ejecutar dependiendo de la pgina.
 */
class PagesUserController {
    /**
     * Oculta los bloques que no están permitidos para el usuario tipo gestor.
     */
    static async pagePostTypeBlock() {
        const theList = document.getElementById('the-list');
        if (!theList) return;

        theList.querySelectorAll('tr').forEach((ele) => {
            const strong = ele.querySelector('strong');
            if (strong && !titlesEnables.includes(strong.textContent.trim())) {
                ele.style.display = 'none';
            }
        });

        const addButton = document.querySelector('#wpbody-content > div.wrap > a');
        hideElement(addButton);
        hideElement(document.querySelector('div.wrap > ul'));
    }

    /**
     * Personaliza la página de inicio de WooCommerce.
     */
    static async pageCheckoutWooComerce() {
        console.group('Yape checkout - log')

        // Declarar la función attachRadioEvent ANTES de usarla
        const attachRadioEvent = () => {
            document.querySelectorAll('[name="radio-control-wc-payment-method-options"]').forEach((radio) => {
                if (!radio.dataset.listener) {
                    radio.dataset.listener = "true"; // Evitar duplicados
                    radio.addEventListener("change", function () {
                        renderYapeHtml();
                    });
                }
            });
        };

        // Detectar cambios en el DOM
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === "childList") {
                    attachRadioEvent(); // Vuelve a adjuntar los eventos
                }
            });
        });

        // Observar cambios en todo el documento o en un contenedor específico
        observer.observe(document.body, { childList: true, subtree: true });
        await sleep(2);
        renderYapeHtml();
        console.groupEnd()
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











const runAllPages = async () => {
    console.log(`Script ejecutado para  todas las paginas: ${ruta}`)
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
    console.group(`🚀 VERSION_FRONTEND: ${VERSION_FRONTEND}`);
    try {
        // Cargar un script externo antes de continuar 
        try {
            await loadExternalScript(`${origin}/soluciones-tools/js/gestor/utils.js`);
            if (typeof getCurrentDayName === 'function') {
                const dia = getCurrentDayName();
                console.log(`📅 Día actual: ${dia}`);
            }
        } catch (error) {
            console.warn('⚠️ No se pudo cargar utils.js:', error);
        }


        await runAllPages();

        if (isCurrentPathUrl('/wp-admin/edit.php') && isCurrentPathUrl('post_type=blocks')) {
            console.log('📄 Página: Bloques');
            await PagesUserController.pagePostTypeBlock();
        } else if (isCurrentPathUrl('/marcas/')) {
            // Espera a que terminen las llamadas AJAX
            await waitForAjaxCompletion();
            console.log('📄 Página: Marcas');
        } else if (isCurrentPathUrl('/checkout') || isCurrentPathUrl('/checkout-cesar')) {
            // Espera a que terminen las llamadas AJAX
            await waitForAjaxCompletion();
            console.log('Pagina de realizar la compra');
            await PagesUserController.pageCheckoutWooComerce();
        }
        // se ejecuta todas las paginas en una ruta
        if (searchPage(pagesBlock, ruta)) {
            //await PagesUserController.blockedPage();
        }


        console.log('✅ Success');

    } catch (e) {
        console.error('❌ Error en front-end.js:', e);
        console.error('Stack trace:', e.stack);
    }
    console.groupEnd();
});

