<?php
/**
 * Plantilla para la página de bienvenida del plugin.
 *
 * Este fichero es responsable de renderizar el HTML de la página de bienvenida.
 * Utiliza las variables pasadas a través del array $template_data.
 *
 * @package SoluAdminUtils
 * @since 1.2.0
 */

// Prevenir el acceso directo al fichero.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Extraer datos del array $template_data con validaciones para evitar errores.
$titulo  = isset( $template_data['titulo'] ) ? $template_data['titulo'] : 'Bienvenido';
$message = isset( $template_data['message'] ) ? $template_data['message'] : 'Gracias por usar el plugin.';

// Verificar si WooCommerce está activo
$woocommerce_active = in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')));

?>

<div class="wrap">
	<h1><?php echo esc_html( $titulo ); ?></h1>

	<div class="notice notice-info inline">
		<p><?php echo wp_kses_post( $message ); ?></p>
	</div>


	<!-- ====================================================
	SECCIÓN: Cómo Usar las Funcionalidades
	==================================================== -->
	<div class="card" style="margin-top: 20px;">
		<h2><span class="dashicons dashicons-book" style="color: #27ae60;"></span> Cómo Usar las Funcionalidades</h2>
		
		<?php if ($woocommerce_active): ?>
			<div class="usage-section">
				<h3>🛒 Filtros de Productos en WooCommerce</h3>
				<div class="usage-steps">
					<ol>
						<li><strong>Accede al listado de productos:</strong> Ve a <code>Productos → Todos los productos</code></li>
						<li><strong>Localiza los filtros:</strong> En la parte superior verás los filtros de "Destacados" y "Stock"</li>
						<li><strong>Usa los filtros:</strong>
							<ul>
								<li><strong>Destacados:</strong> Filtra productos destacados o no destacados</li>
								<li><strong>Stock:</strong> Filtra productos en stock o sin stock</li>
							</ul>
						</li>
						<li><strong>Aplica filtros:</strong> Selecciona las opciones y haz clic en "Filtrar"</li>
					</ol>
				</div>
				
				<div class="feature-preview" style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 15px;">
					<h4>Vista Previa de los Filtros:</h4>
					<div style="display: flex; gap: 10px; flex-wrap: wrap;">
						<select style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); color: white; border: 1px solid #2c3e50; border-radius: 4px; padding: 5px 10px; min-width: 160px;">
							<option>Destacados (todos)</option>
							<option>Destacados: Sí</option>
							<option>Destacados: No</option>
						</select>
						<select style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); color: white; border: 1px solid #2c3e50; border-radius: 4px; padding: 5px 10px; min-width: 160px;">
							<option>Stock (todos)</option>
							<option>En stock</option>
							<option>Sin stock</option>
						</select>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<div class="usage-section">
			<h3>🔧 Utilidades del Sistema</h3>
			<div class="usage-steps">
				<ul>
					<li><strong>Logs:</strong> Todas las operaciones se registran automáticamente</li>
					<li><strong>Notificaciones:</strong> Sistema de alertas elegante y no intrusivo</li>
					<li><strong>Validaciones:</strong> Verificaciones automáticas de dependencias</li>
				</ul>
			</div>
		</div>
	</div>

	<!-- ====================================================
	SECCIÓN: Información Técnica
	==================================================== -->
	<div class="card" style="margin-top: 20px;">
		<h2><span class="dashicons dashicons-info" style="color: #e74c3c;"></span> Información Técnica</h2>
		
		<div class="row" style="margin: 0;">
			<div class="col-md-6">
				<h3>📋 Especificaciones</h3>
				<table class="form-table">
					<tr>
						<th>Versión del Plugin:</th>
						<td><code><?php echo esc_html(SOLU_ADMIN_UTIL_VERSION); ?></code></td>
					</tr>
					<tr>
						<th>Versión de WordPress:</th>
						<td><code><?php echo esc_html(get_bloginfo('version')); ?></code></td>
					</tr>
					<tr>
						<th>Versión de PHP:</th>
						<td><code><?php echo esc_html(PHP_VERSION); ?></code></td>
					</tr>
					<?php if ($woocommerce_active): ?>
					<tr>
						<th>WooCommerce:</th>
						<td><span style="color: #27ae60;">✅ Activo</span></td>
					</tr>
					<?php else: ?>
					<tr>
						<th>WooCommerce:</th>
						<td><span style="color: #e74c3c;">❌ Inactivo</span></td>
					</tr>
					<?php endif; ?>
				</table>
			</div>
			
			<div class="col-md-6">
				<h3>🏗️ Arquitectura</h3>
				<ul class="architecture-list">
					<li><strong>Arquitectura Hexagonal:</strong> Separación clara de responsabilidades</li>
					<li><strong>Clean Code:</strong> Código limpio y bien documentado</li>
					<li><strong>Patrón Singleton:</strong> Para clases de utilidades</li>
					<li><strong>Sistema de Templates:</strong> Separación de lógica y presentación</li>
					<li><strong>Hook System:</strong> Integración nativa con WordPress</li>
				</ul>
			</div>
		</div>
	</div>

	<!-- ====================================================
	SECCIÓN: Enlaces Útiles
	==================================================== -->
	<div class="card" style="margin-top: 20px;">
		<h2><span class="dashicons dashicons-admin-links" style="color: #9b59b6;"></span> Enlaces Útiles</h2>
		
		<div class="useful-links">
			<div class="row" style="margin: 0;">
				<div class="col-md-4">
					<h4>📚 Documentación</h4>
					<ul>
						<li><a href="<?php echo admin_url('admin.php?page=' . SOLU_ADMIN_UTIL_NAME_PLUGIN . '-help'); ?>" class="button button-secondary">Ayuda e Información</a></li>
						<li><a href="https://solucionessystem.com" target="_blank" class="button button-secondary">Documentación Online</a></li>
					</ul>
				</div>
				
				<div class="col-md-4">
					<h4>🛒 WooCommerce</h4>
					<?php if ($woocommerce_active): ?>
						<ul>
							<li><a href="<?php echo admin_url('edit.php?post_type=product'); ?>" class="button button-primary">Listado de Productos</a></li>
							<li><a href="<?php echo admin_url('admin.php?page=wc-admin'); ?>" class="button button-secondary">Dashboard WooCommerce</a></li>
						</ul>
					<?php else: ?>
						<p><em>Activa WooCommerce para acceder a estas funcionalidades.</em></p>
					<?php endif; ?>
				</div>
				
				<div class="col-md-4">
					<h4>📞 Soporte</h4>
					<ul>
						<li><a href="mailto:perucaos@gmail.com" class="button button-secondary">Soporte Técnico</a></li>
						<li><a href="https://solucionessystem.com/contacto" target="_blank" class="button button-secondary">Contacto</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<!-- ====================================================
	SECCIÓN: Estado del Sistema
	==================================================== -->
	<div class="card" style="margin-top: 20px;">
		<h2><span class="dashicons dashicons-yes-alt" style="color: #27ae60;"></span> Estado del Sistema</h2>
		
		<div class="system-status">
			<div class="status-item">
				<span class="dashicons dashicons-yes" style="color: #27ae60;"></span>
				<strong>Plugin activado correctamente</strong>
			</div>
			<div class="status-item">
				<span class="dashicons dashicons-yes" style="color: #27ae60;"></span>
				<strong>Sistema de logs funcionando</strong>
			</div>
			<div class="status-item">
				<span class="dashicons dashicons-yes" style="color: #27ae60;"></span>
				<strong>Utilidades cargadas</strong>
			</div>
			<?php if ($woocommerce_active): ?>
			<div class="status-item">
				<span class="dashicons dashicons-yes" style="color: #27ae60;"></span>
				<strong>Funcionalidades de WooCommerce disponibles</strong>
			</div>
			<?php else: ?>
			<div class="status-item">
				<span class="dashicons dashicons-warning" style="color: #f39c12;"></span>
				<strong>WooCommerce no detectado</strong>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<style>
.feature-list {
	list-style: none;
	padding-left: 0;
}

.feature-list li {
	margin-bottom: 10px;
	padding: 8px 0;
	border-bottom: 1px solid #f0f0f0;
}

.feature-list li:last-child {
	border-bottom: none;
}

.usage-steps ol, .usage-steps ul {
	padding-left: 20px;
}

.usage-steps li {
	margin-bottom: 8px;
}

.architecture-list {
	list-style: none;
	padding-left: 0;
}

.architecture-list li {
	margin-bottom: 8px;
	padding: 5px 0;
}

.useful-links ul {
	list-style: none;
	padding-left: 0;
}

.useful-links li {
	margin-bottom: 10px;
}

.system-status {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
	gap: 15px;
	margin-top: 15px;
}

.status-item {
	display: flex;
	align-items: center;
	gap: 10px;
	padding: 10px;
	background: #f8f9fa;
	border-radius: 5px;
	border-left: 4px solid #27ae60;
}

.usage-section {
	margin-bottom: 25px;
}

.usage-section h3 {
	color: #2c3e50;
	border-bottom: 2px solid #3498db;
	padding-bottom: 5px;
	margin-bottom: 15px;
}

.feature-preview {
	border: 1px solid #ddd;
}

@media (max-width: 768px) {
	.system-status {
		grid-template-columns: 1fr;
	}
}
</style>
