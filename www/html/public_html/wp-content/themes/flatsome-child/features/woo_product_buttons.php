<?php
/**
 * WooCommerce Product Buttons
 *
 * Agrega botones y avisos de WhatsApp en las páginas de producto de WooCommerce.
 *
 * @package    WooCommerce_WhatsApp_Buttons
 * @subpackage WooCommerce
 * @author     —
 * @version    1.1.0
 */

// Seguridad: evitar acceso directo al archivo.
defined( 'ABSPATH' ) || exit;

// Verificar que WooCommerce esté activo antes de registrar cualquier hook.
if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

// =============================================================================
// HOOKS
// =============================================================================

/**
 * Muestra el aviso de verificación de stock por WhatsApp
 * en productos específicos, justo debajo del stock.
 *
 * Hook:     woocommerce_single_product_summary
 * Priority: 30 (después del stock, que se renderiza en 20)
 */
add_action( 'woocommerce_single_product_summary', 'woo_wpb_aviso_verificar_stock', 30 );

/**
 * Muestra el botón "Consultar por WhatsApp"
 * inmediatamente después del botón "Añadir al carrito".
 *
 * Hook:     woocommerce_after_add_to_cart_button
 * Priority: 10 (valor por defecto)
 */
add_action( 'woocommerce_after_add_to_cart_button', 'woo_wpb_boton_whatsapp' );

/**
 * Encola el CSS del aviso de WhatsApp solo en páginas de producto.
 */
add_action( 'wp_enqueue_scripts', 'woo_wpb_encolar_estilos_producto' );

// =============================================================================
// FUNCIONES
// =============================================================================

/**
 * Encola los estilos para los elementos de WhatsApp en producto.
 *
 * Solo se carga en páginas de producto singular para no
 * añadir CSS innecesario en el resto del sitio.
 *
 * @return void
 */
function woo_wpb_encolar_estilos_producto() {
	if ( ! is_product() ) {
		return;
	}

	$css = '
		.woo-wpb-stock-notice {
			display: flex;
			align-items: center;
			gap: 15px;
			margin: 15px 0 20px;
			padding: 15px 20px;
			background: linear-gradient(135deg, #fff3cd 0%, #fff8e1 100%);
			border-left: 5px solid #ffc107;
			border-radius: 8px;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
			animation: woo-wpb-slide-in 0.5s ease-out;
		}
		.woo-wpb-stock-notice__icon {
			display: flex;
			align-items: center;
			justify-content: center;
			flex-shrink: 0;
			width: 45px;
			height: 45px;
			background: #ffc107;
			border-radius: 50%;
			box-shadow: 0 2px 6px rgba(255, 193, 7, 0.3);
		}
		.woo-wpb-stock-notice__icon i {
			color: #fff;
			font-size: 24px;
		}
		.woo-wpb-stock-notice__body {
			flex: 1;
		}
		.woo-wpb-stock-notice__title {
			margin: 0 0 5px;
			color: #856404;
			font-size: 16px;
			font-weight: 700;
			letter-spacing: 0.3px;
		}
		.woo-wpb-stock-notice__text {
			margin: 0;
			color: #856404;
			font-size: 14px;
			line-height: 1.5;
		}
		.woo-wpb-stock-notice__btn {
			background-color: #25D366 !important;
			border-color: #25D366 !important;
			color: #fff !important;
			padding: 8px 16px !important;
			font-size: 13px !important;
			white-space: nowrap;
			box-shadow: 0 3px 8px rgba(37, 211, 102, 0.3);
			transition: transform 0.3s ease, box-shadow 0.3s ease;
		}
		.woo-wpb-stock-notice__btn:hover {
			transform: translateY(-2px);
			box-shadow: 0 5px 12px rgba(37, 211, 102, 0.4);
		}
		@keyframes woo-wpb-slide-in {
			from {
				opacity: 0;
				transform: translateX(-30px);
			}
			to {
				opacity: 1;
				transform: translateX(0);
			}
		}
		@media (max-width: 549px) {
			.woo-wpb-stock-notice {
				flex-direction: column;
				text-align: center;
				padding: 15px;
			}
			.woo-wpb-stock-notice__title {
				font-size: 14px;
			}
			.woo-wpb-stock-notice__text {
				font-size: 13px;
			}
		}
	';

	// Registrar y encolar como inline CSS adjunto a woocommerce-general.
	wp_add_inline_style( 'woocommerce-general', $css );
}

/**
 * Muestra un aviso de verificación de stock para productos específicos.
 *
 * Solo se renderiza si el ID del producto actual está en la lista
 * $productos_con_aviso. Utiliza el objeto global $product de WooCommerce.
 *
 * @return void
 */
function woo_wpb_aviso_verificar_stock() {
if ( ! is_product() ) {
		return;
	}
	// IDs de productos que deben mostrar el aviso de verificación de stock.
	$productos_con_aviso = array( 123, 29896 );

	$product = wc_get_product( get_the_ID() );

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	if ( ! in_array( $product->get_id(), $productos_con_aviso, true ) ) {
		return;
	}

	$whatsapp_link = add_query_arg(
		'text',
		rawurlencode( 'Hola, quiero verificar el stock del producto: ' . $product->get_name() ),
		'https://wa.me/51946480897'
	);
	?>
	<div class="woo-wpb-stock-notice">

		<div class="woo-wpb-stock-notice__icon">
			<i class="icon-whatsapp" aria-hidden="true"></i>
		</div>

		<div class="woo-wpb-stock-notice__body">
			<h4 class="woo-wpb-stock-notice__title">
				⚠️ <?php esc_html_e( 'IMPORTANTE - Verificar Stock', 'woocommerce' ); ?>
			</h4>
			<p class="woo-wpb-stock-notice__text">
				<?php esc_html_e( 'El stock puede variar.', 'woocommerce' ); ?>
				<strong><?php esc_html_e( 'Verifica la disponibilidad por WhatsApp antes de comprar.', 'woocommerce' ); ?></strong>
			</p>
		</div>

		<a href="<?php echo esc_url( $whatsapp_link ); ?>"
		   target="_blank"
		   rel="noopener noreferrer"
		   class="button primary is-small woo-wpb-stock-notice__btn"
		   aria-label="<?php esc_attr_e( 'Consultar disponibilidad por WhatsApp', 'woocommerce' ); ?>">
			<i class="icon-whatsapp" aria-hidden="true"></i>
			<?php esc_html_e( 'Consultar', 'woocommerce' ); ?>
		</a>

	</div>
	<?php
}

/**
 * Muestra el botón "Consultar por WhatsApp" después del botón de compra.
 *
 * Construye un enlace de WhatsApp que incluye el nombre y la URL
 * del producto para facilitar la consulta del cliente.
 *
 * @return void
 */
function woo_wpb_boton_whatsapp() {
if ( ! is_product() ) {
		return;
	}

	$numero_whatsapp = '51946118274';
	$producto_nombre = get_the_title();
	$producto_url    = get_permalink();

	$whatsapp_link = add_query_arg(
		array(
			'phone' => $numero_whatsapp,
			'text'  => rawurlencode( 'Hola, me interesa este producto: ' . $producto_nombre . ' - ' . $producto_url ),
		),
		'https://api.whatsapp.com/send'
	);

	$img_src = '/soluciones-tools/images/WhatsApp.svg';
	?>
	<div style="display: block; width: 100%; margin-top: 8px;">
		<a href="<?php echo esc_url( $whatsapp_link ); ?>"
		   target="_blank"
		   rel="noopener noreferrer"
		   aria-label="<?php esc_attr_e( 'Consultar por WhatsApp', 'woocommerce' ); ?>"
		   style="
			   display: inline-flex;
			   align-items: center;
			   gap: 8px;
			   background-color: #25D366;
			   color: #fff !important;
			   padding: 10px 18px;
			   border-radius: 4px;
			   text-decoration: none !important;
			   font-weight: bold;
			   font-size: 14px;
			   line-height: 1;
			   white-space: nowrap;
		   ">
			<img src="<?php echo esc_url( $img_src ); ?>"
			     width="20"
			     height="20"
			     loading="lazy"
			     style="display:inline-block; vertical-align:middle; flex-shrink:0;"
			     alt="WhatsApp">
			<?php esc_html_e( 'Consultar por WhatsApp', 'woocommerce' ); ?>
		</a>
	</div>
	<?php
}