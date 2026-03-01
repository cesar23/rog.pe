<?php

// Extraer datos del array $template_data

$instance_backend_function = $template_data['instance_backend_function'];
$GLOBAL_TIPOS_GRUPOS = $template_data['GLOBAL_TIPOS_GRUPOS'];


?>
<div class="wrap">
    <h1>Generar Códigos HTML</h1>
    <p>Gestiona códigos HTML para categorías, marcas y otros elementos de tu tienda.</p>


    <a href="?page=solu-generate-html&action=create" class="page-title-action">Agregar Nuevo Código HTML</a>
    <a href="?page=solu-generate-html&action=categories" class="page-title-action">Generar Codigos [Categorias Megamenu]</a>
    <hr class="wp-header-end">

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre del Grupo</th>
                <th>Name codigo</th>

                <th>Fecha de Creación</th>
                <th>Última Actualización</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $html_codes = $instance_backend_function->get_all_html_codes();
            if (empty($html_codes)) {
                echo '<tr><td colspan="7">No se encontraron códigos HTML.</td></tr>';
            } else {
                foreach ($html_codes as $html_code) {
                    // Determinar el tipo y color del grupo
                    $tipo_grupo = 'other';
                    $color_grupo = $GLOBAL_TIPOS_GRUPOS['other']['color'];

                    foreach ($GLOBAL_TIPOS_GRUPOS as $prefix => $config) {
                        if ($prefix !== 'other' && strpos($html_code['name_group'], $prefix) === 0) {
                            $tipo_grupo = str_replace('_', '', $prefix);
                            $color_grupo = $config['color'];
                            break;
                        }
                    }

                    // Truncar el código HTML
                    $code_preview = strlen($html_code['code']) > 100 ?
                        substr($html_code['code'], 0, 100) . '...' :
                        $html_code['code'];

                    echo '<tr>';
                    echo '<td>' . esc_html($html_code['id']) . '</td>';
                    echo '<td><span class="badge badge-' . $tipo_grupo . '" style="background: ' . $color_grupo . '; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">' . esc_html($html_code['name_group']) . '</span></td>';
                    echo '<td><strong>' . esc_html($html_code['name_code']) . '</strong></td>';
                    echo '<td>' . esc_html($html_code['created_at']) . '</td>';
                    echo '<td>' . esc_html($html_code['update_at']) . '</td>';
                    echo '<td>
                            <a href="?page=solu-generate-html&action=edit&id=' . esc_attr($html_code['id']) . '" class="button action">Editar</a>
                            <a href="?page=solu-generate-html&action=delete&id=' . esc_attr($html_code['id']) . '" class="button action">Borrar</a>
                          </td>';
                    echo '</tr>';
                }
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th>ID</th>
                <th>Nombre del Grupo</th>
                <th>Name codigo</th>

                <th>Fecha de Creación</th>
                <th>Última Actualización</th>
                <th>Acciones</th>
            </tr>
        </tfoot>
    </table>
</div>