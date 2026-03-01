<?php
// Verificar que no se acceda directamente
if (!defined('ABSPATH')) {
    exit;
}

// Extraer datos del array $template_data
$selected_categories = $template_data['selected_categories'];
$total_selected = $template_data['total_selected'];
$instance_backend_function = $template_data['instance_backend_function'];
$GLOBAL_TIPOS_GRUPOS = $template_data['GLOBAL_TIPOS_GRUPOS'];

?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-yes-alt" style="margin-right: 10px;"></span>
        Categorías Seleccionadas
    </h1>
    <a href="?page=solu-generate-html&action=categories" class="page-title-action">← Volver a la lista</a>

    <hr class="wp-header-end">

    <div class="notice notice-success">
        <p>
            <strong>✅ Se seleccionaron <?php echo $total_selected; ?> categoría<?php echo $total_selected !== 1 ? 's' : ''; ?>:</strong>
        </p>
    </div>

    <!-- Tabla de categorías seleccionadas -->
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col" class="manage-column column-name">
                    <strong>Nombre</strong>
                </th>
                <th scope="col" class="manage-column column-slug">
                    <strong>Slug</strong>
                </th>
                <th scope="col" class="manage-column column-description">
                    <strong>Descripción</strong>
                </th>
                <th scope="col" class="manage-column column-count">
                    <strong>Productos</strong>
                </th>
                <th scope="col" class="manage-column column-actions">
                    <strong>Acciones</strong>
                </th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($selected_categories as $category): ?>
                <tr>
                    <td class="column-name">
                        <strong>
                            <?php
                            $admin_link = admin_url('edit.php?post_type=product&product_cat=' . urlencode($category['slug']));
                            if (!empty($category['slug']) && $category['slug'] !== '') {
                                echo '<a href="' . esc_url($admin_link) . '" target="_blank">';
                                echo esc_html($category['name']);
                                echo '</a>';
                            } else {
                                echo esc_html($category['name']);
                            }
                            ?>
                        </strong>
                    </td>

                    <td class="column-slug">
                        <?php if (!empty($category['slug']) && $category['slug'] !== ''): ?>
                            <code><?php echo esc_html($category['slug']); ?></code>
                        <?php else: ?>
                            <span style="color: #999; font-style: italic;">Slug no disponible</span>
                        <?php endif; ?>
                    </td>

                    <td class="column-description">
                        <?php if (!empty($category['description'])): ?>
                            <?php echo esc_html(wp_trim_words($category['description'], 10)); ?>
                        <?php else: ?>
                            <span style="color: #999;">Sin descripción</span>
                        <?php endif; ?>
                    </td>

                    <td class="column-count">
                        <span class="count-bubble <?php echo $category['count'] > 0 ? 'has-products' : 'no-products'; ?>">
                            <?php echo esc_html($category['count']); ?>
                        </span>
                    </td>

                    <td class="column-actions">
                        <div class="row-actions">
                            <span class="products">
                                <?php
                                $products_link = admin_url('edit.php?post_type=product&product_cat=' . urlencode($category['slug']));
                                if (!empty($category['slug']) && $category['slug'] !== '') {
                                    echo '<a href="' . esc_url($products_link) . '" class="button button-small" target="_blank">Ver Productos</a>';
                                } else {
                                    echo '<span class="button button-small" style="opacity: 0.5; cursor: not-allowed;">Ver Productos</span>';
                                }
                                ?>
                            </span>
                            <span class="edit-cat">
                                <a href="<?php echo esc_url(admin_url('edit-tags.php?action=edit&taxonomy=product_cat&tag_ID=' . $category['term_id'])); ?>"
                                    class="button button-small"
                                    target="_blank">
                                    Editar
                                </a>
                            </span>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Información adicional -->
    <div class="tablenav bottom">
        <div class="alignleft actions">
            <p class="description">
                <strong>Total seleccionadas:</strong> <?php echo $total_selected; ?> categoría<?php echo $total_selected !== 1 ? 's' : ''; ?>
            </p>
        </div>
        <div class="alignright">
            <a href="?page=solu-generate-html&action=categories" class="button">← Volver a la lista</a>
        </div>
    </div>

    <!-- Botón para generar HTML -->
    <div style="margin-top: 30px;">
        <button id="generar-html-categorias" class="button button-primary" style="font-size:16px; padding:10px 24px;">Generar HTML de categorías</button>
    </div>
    <div id="html-categorias-resultado-preview" style="margin-top: 20px;"></div>
    <div id="html-categorias-resultado" style="margin-top: 20px;"></div>

    <div id="form-container" style="margin-top: 30px; display: none;">
        <form id="form-select-group" style="margin-bottom: 20px;">
            <label for="select-group" style="font-weight: bold; margin-right: 10px;">Selecciona el grupo donde guardar el código:</label>
            <select id="select-group" name="select_group" style="min-width: 220px; padding: 4px 8px;">
                <?php
                $groups = $instance_backend_function->get_all_groups();
                ?>
                <?php if (!empty($groups)): ?>
                    <?php foreach ($groups as $group): ?>
                        <?php
                        $group_name = is_array($group) ? $group['name_group'] : $group;
                        ?>
                        <option value="<?php echo esc_attr($group_name); ?>"><?php echo esc_html($group_name); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
                <option value="nuevo_grupo">+ Crear nuevo grupo...</option>
            </select>
            <input type="text" id="nuevo-grupo-input" name="nuevo_grupo" placeholder="Ej: category_electronica" style="display:none; margin-left:10px; min-width:180px; padding: 4px 8px; border: 1px solid #ddd; border-radius: 3px;" />
            <small id="nuevo-grupo-help" style="display:none; margin-left:10px; color: #0073aa; font-style: italic;">
                Ingresa el nombre del nuevo grupo con uno de estos prefijos:<br>
                <strong>category_</strong> (para categorías) |
                <strong>brand_</strong> (para marcas) |
                <strong>label_</strong> (para etiquetas) |
                <strong>products_</strong> (para productos) |
                <strong>other</strong> (para otros)
            </small>
            <div id="nuevo-grupo-error" style="display:none; margin-left:10px; color: #dc3232; font-style: italic; font-weight: bold;"></div>

            <!-- Campo para el identificador del código -->
            <div style="margin-top: 15px;">
                <label for="name-code-input" style="font-weight: bold; margin-right: 10px;">Identificador del código:</label>
                <input type="text" id="name-code-input" name="name_code" placeholder="Ej: categorias_mouse_2025" style="min-width: 220px; padding: 4px 8px;" required />
                <small style="display: block; margin-top: 5px; color: #666;">Identificador único para este código HTML</small>
            </div>

            <!-- Botón de guardar dentro del formulario -->
            <button type="submit" id="save_code_html" class="button button-primary" style="display:none; font-size:16px; padding:10px 24px; margin-top: 15px;">Guardar el código</button>
        </form>
        <script>
            jQuery(document).ready(function($) {
                // Prefijos válidos para grupos
                var prefijosValidos = ['category_', 'brand_', 'label_', 'products_', 'other'];

                // Función para validar el nombre del grupo
                function validarNombreGrupo(nombre) {
                    if (!nombre || nombre.trim() === '') {
                        return {
                            valido: false,
                            mensaje: 'El nombre del grupo no puede estar vacío.'
                        };
                    }

                    // Verificar si comienza con uno de los prefijos válidos
                    var tienePrefijoValido = false;
                    for (var i = 0; i < prefijosValidos.length; i++) {
                        if (nombre.toLowerCase().startsWith(prefijosValidos[i])) {
                            tienePrefijoValido = true;
                            break;
                        }
                    }

                    if (!tienePrefijoValido) {
                        return {
                            valido: false,
                            mensaje: 'El nombre del grupo debe comenzar con uno de estos prefijos: ' + prefijosValidos.join(', ')
                        };
                    }

                    // Verificar que tenga al menos un carácter después del prefijo
                    var nombreSinPrefijo = nombre.substring(nombre.indexOf('_') + 1);
                    if (nombreSinPrefijo.trim() === '') {
                        return {
                            valido: false,
                            mensaje: 'El nombre del grupo debe tener contenido después del prefijo.'
                        };
                    }

                    return {
                        valido: true,
                        mensaje: ''
                    };
                }

                // Manejar el cambio en el select de grupos
                $('#select-group').on('change', function() {
                    console.log('Select cambiado a:', $(this).val()); // Debug
                    if ($(this).val() === 'nuevo_grupo') {
                        $('#nuevo-grupo-input').show().focus();
                        $('#nuevo-grupo-help').show();
                        $('#nuevo-grupo-error').hide();
                        console.log('Mostrando campo nuevo grupo'); // Debug
                    } else {
                        $('#nuevo-grupo-input').hide();
                        $('#nuevo-grupo-help').hide();
                        $('#nuevo-grupo-error').hide();
                        console.log('Ocultando campo nuevo grupo'); // Debug
                    }
                });

                // Validar el nombre del grupo en tiempo real
                $('#nuevo-grupo-input').on('input', function() {
                    var nombre = $(this).val();
                    var validacion = validarNombreGrupo(nombre);

                    if (!validacion.valido) {
                        $('#nuevo-grupo-error').text(validacion.mensaje).show();
                        $(this).css('border-color', '#dc3232');
                    } else {
                        $('#nuevo-grupo-error').hide();
                        $(this).css('border-color', '#7ad03a');
                    }
                });

                // Validar antes de enviar el formulario
                $('#form-select-group').on('submit', function(e) {
                    if ($('#select-group').val() === 'nuevo_grupo') {
                        var nombre = $('#nuevo-grupo-input').val();
                        var validacion = validarNombreGrupo(nombre);

                        if (!validacion.valido) {
                            e.preventDefault();
                            $('#nuevo-grupo-error').text(validacion.mensaje).show();
                            $('#nuevo-grupo-input').focus();
                            return false;
                        }
                    }
                });

                // También manejar el evento cuando se carga la página
                if ($('#select-group').val() === 'nuevo_grupo') {
                    $('#nuevo-grupo-input').show();
                    $('#nuevo-grupo-help').show();
                }
            });
        </script>
    </div>
    <div id="save_code_html_message" style="margin-top:15px;"></div>
</div>

<style>
    .count-bubble {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: bold;
        text-align: center;
        min-width: 20px;
    }

    .count-bubble.has-products {
        background-color: #0073aa;
        color: white;
    }

    .count-bubble.no-products {
        background-color: #f1f1f1;
        color: #666;
    }

    .column-actions .row-actions {
        margin-top: 5px;
    }

    .column-actions .button {
        margin-right: 5px;
    }

    .column-name strong a {
        color: #0073aa;
        text-decoration: none;
    }

    .column-name strong a:hover {
        color: #005a87;
    }

    .column-slug code {
        background: #f1f1f1;
        padding: 2px 4px;
        border-radius: 3px;
        font-size: 12px;
    }
</style>



<script>
    jQuery(document).ready(function($) {

        let html = '';

        $('#generar-html-categorias').on('click', function(e) {
            e.preventDefault();
            var categoria_ids = <?php echo json_encode(array_column($selected_categories, 'term_id')); ?>;
            $('#html-categorias-resultado-preview').html('<em>Generando HTML...</em>');
            $('#html-categorias-resultado').html('<em>Generando HTML...</em>');

            $.post(ajaxurl, {
                action: 'solu_generate_html_categorias',
                categoria_ids: categoria_ids,
                _ajax_nonce: '<?php echo wp_create_nonce('solu_generate_html_categorias'); ?>'
            }, function(response) {
                if (response.success) {
                    html = response.data.html;
                    let formattedHtml = html.replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    $('#html-categorias-resultado').html('<pre class="line-numbers"><code class="language-html">' + formattedHtml + '</code></pre>');

                    $('#html-categorias-resultado-preview').html(html);
                    if (window.Prism) {
                        Prism.highlightAll();
                    }

                    // Mostrar el formulario completo
                    $('#form-container').show();
                    // Mostrar el botón de guardar
                    $('#save_code_html').show();
                    console.log('Formulario y botón de guardar mostrados'); // Debug
                } else {
                    $('#html-categorias-resultado').html('<span style="color:red;">' + response.data + '</span>');
                }
            });
        });

        // Manejar el submit del formulario
        $('#form-select-group').on('submit', function(e) {
            e.preventDefault();

            // Validar que se haya generado HTML
            if (!html) {
                alert('Primero debes generar el HTML de las categorías');
                return;
            }

            // Validar que se haya ingresado un identificador
            const nameCode = $('#name-code-input').val().trim();
            if (!nameCode) {
                alert('Debes ingresar un identificador para el código');
                $('#name-code-input').focus();
                return;
            }

            // Obtener datos del formulario
            const selectGroup = $('#select-group').val();
            const nuevoGrupo = $('#nuevo-grupo-input').val().trim();

            // Determinar el grupo final
            let finalGroup = selectGroup;
            if (selectGroup === 'nuevo_grupo') {
                if (!nuevoGrupo) {
                    alert('Debes ingresar el nombre del nuevo grupo');
                    $('#nuevo-grupo-input').focus();
                    return;
                }
                finalGroup = nuevoGrupo;
            }

            console.log('Enviando datos:', {
                html: html,
                group: finalGroup,
                name_code: nameCode
            }); // Debug

            const data = {
                action: 'save_code_html',
                html: html,
                select_group: selectGroup,
                nuevo_grupo: nuevoGrupo,
                name_code: nameCode,
                _ajax_nonce: '<?php echo wp_create_nonce('solu_save_code_html'); ?>'
            }

            // Mostrar indicador de carga
            $('#save_code_html').prop('disabled', true).text('Guardando...');

            $.post(ajaxurl, data, function(response) {
                console.log('Respuesta del servidor:', response); // Debug
                console.log('Tipo de respuesta:', typeof response); // Debug
                console.log('Respuesta completa:', JSON.stringify(response, null, 2)); // Debug

                const msgDiv = document.getElementById('save_code_html_message');
                if (response && response.success) {
                    msgDiv.innerHTML = '<div class="notice notice-success"><p>' + response.data + '</p></div>';
                } else {
                    const errorMsg = response && response.data ? response.data : 'Error al guardar el código HTML.';
                    msgDiv.innerHTML = '<div class="notice notice-error"><p>' + errorMsg + '</p></div>';
                }

                // Restaurar botón
                $('#save_code_html').prop('disabled', false).text('Guardar el código');
            }).fail(function(xhr, status, error) {
                console.error('Error en la petición:', error); // Debug
                console.error('Status:', status); // Debug
                console.error('XHR:', xhr); // Debug
                console.error('ResponseText:', xhr.responseText); // Debug
                console.error('StatusText:', xhr.statusText); // Debug

                const msgDiv = document.getElementById('save_code_html_message');
                msgDiv.innerHTML = '<div class="notice notice-error"><p>Error de conexión al guardar el código HTML. Detalles: ' + error + '</p></div>';
                $('#save_code_html').prop('disabled', false).text('Guardar el código');
            });
        });

    });
</script>