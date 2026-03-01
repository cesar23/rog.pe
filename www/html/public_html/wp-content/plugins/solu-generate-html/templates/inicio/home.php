<?php


// Extraer datos del array $template_data
$GLOBAL_TIPOS_GRUPOS = $template_data['GLOBAL_TIPOS_GRUPOS'];
$instance_backend_function = $template_data['instance_backend_function'];
?>
<div class="wrap">
    <h1>🏠 Inicio - Generar Códigos HTML</h1>
    <p>Bienvenido al panel de administración para gestionar códigos HTML de categorías, marcas y otros elementos de tu tienda.</p>

    <?php
    // Obtener estadísticas usando la clase Singleton

    $total_codes = $instance_backend_function->get_all_html_codes();
    $total_count = count($total_codes);


    // Usar la función global para obtener contadores
    $counters = $instance_backend_function->get_code_counters($total_codes);
    ?>

    <!-- Tarjetas de estadísticas -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;">
        <div class="card" style="text-align: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <h3 style="margin: 0; font-size: 2em;"><?php echo $total_count; ?></h3>
            <p style="margin: 5px 0 0 0; font-weight: 600;">Total de Códigos</p>
        </div>

        <?php foreach ($GLOBAL_TIPOS_GRUPOS as $prefix => $config): ?>
            <div class="card" style="text-align: center; background: <?php echo $config['color']; ?>; color: white;">
                <h3 style="margin: 0; font-size: 2em;"><?php echo $counters[$prefix]; ?></h3>
                <p style="margin: 5px 0 0 0; font-weight: 600;"><?php echo $config['descripcion']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Acciones rápidas -->
    <div class="card">
        <h2>🚀 Acciones Rápidas</h2>
        <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-top: 15px;">
            <a href="?page=solu-generate-html&action=create" class="button button-primary button-hero">
                ➕ Crear Nuevo Código HTML
            </a>
            <a href="?page=solu-generate-html" class="button button-secondary button-hero">
                📋 Ver Todos los Códigos
            </a>
            <a href="?page=solu-generate-html-help" class="button button-secondary button-hero">
                ❓ Ayuda e Información
            </a>
        </div>
    </div>

    <!-- Códigos recientes -->
    <div class="card">
        <h2>📝 Códigos Recientes</h2>
        <?php
        $recent_codes = array_slice($total_codes, 0, 5); // Mostrar solo los 5 más recientes

        if (empty($recent_codes)) {
            echo '<p>No hay códigos HTML creados aún. <a href="?page=solu-generate-html&action=create">¡Crea el primero!</a></p>';
        } else {
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr>';
            echo '<th>Nombre del Grupo</th>';
            echo '<th>Tipo</th>';
            echo '<th>Fecha de Creación</th>';
            echo '<th>Acciones</th>';
            echo '</tr></thead><tbody>';

            foreach ($recent_codes as $html_code) {
                // Determinar el tipo usando las variables globales
                $type = 'Otro';
                $type_class = 'other';
                foreach ($GLOBAL_TIPOS_GRUPOS as $prefix => $config) {
                    if ($prefix !== 'other' && strpos($html_code['name_group'], $prefix) === 0) {
                        $type = $config['nombre'];
                        $type_class = str_replace('_', '', $prefix);
                        break;
                    }
                }

                echo '<tr>';
                echo '<td><strong>' . esc_html($html_code['name_group']) . '</strong></td>';
                echo '<td><span class="badge badge-' . $type_class . '">' . esc_html($type) . '</span></td>';
                echo '<td>' . esc_html($html_code['created_at']) . '</td>';
                echo '<td>';
                echo '<a href="?page=solu-generate-html&action=edit&id=' . esc_attr($html_code['id']) . '" class="button action">Editar</a> ';
                echo '<a href="?page=solu-generate-html&action=delete&id=' . esc_attr($html_code['id']) . '" class="button action">Borrar</a>';
                echo '</td>';
                echo '</tr>';
            }

            echo '</tbody></table>';

            if (count($total_codes) > 5) {
                echo '<p style="margin-top: 15px;"><a href="?page=solu-generate-html" class="button">Ver todos los códigos (' . count($total_codes) . ')</a></p>';
            }
        }
        ?>
    </div>

    <!-- Información del sistema -->
    <div class="card">
        <h2>ℹ️ Información del Sistema</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 15px;">
            <div>
                <h4>📋 Tipos de Códigos Soportados</h4>
                <ul>
                    <li><strong>Categorías:</strong> Prefijo "category_"</li>
                    <li><strong>Marcas:</strong> Prefijo "brand_"</li>
                    <li><strong>Otros:</strong> Sin prefijo específico</li>
                </ul>
            </div>
            <div>
                <h4>🔧 Funcionalidades</h4>
                <ul>
                    <li>Crear y editar códigos HTML</li>
                    <li>Soporte para PHP y HTML</li>
                    <li>Resaltado de sintaxis</li>
                    <li>Logs de actividad</li>
                </ul>
            </div>
        </div>
    </div>
</div>