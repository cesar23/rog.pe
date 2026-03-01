<?php
global $wpdb;

$table_name =SOLU_PRODUCT_LOGS_TABLE;
// Obtener el usuario seleccionado del formulario
$selected_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ( isset($_POST['clear_logs']) ) {
    clear_product_logs_table();
    echo '<div class="notice notice-success is-dismissible"><p>La tabla de logs ha sido limpiada. actualize la pagina</p></div>';
}

// Configurar el número de registros por página
$logs_per_page = 10;

// Obtener el número total de logs (filtrar por usuario si se seleccionó uno)
if ($selected_user_id) {
    $total_logs = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table_name} WHERE user_id = %d", $selected_user_id));
} else {
    $total_logs = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
}


// Calcular el número total de páginas
$total_pages = ceil($total_logs / $logs_per_page);

// Obtener la página actual desde la URL
$current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;

// Calcular el desplazamiento (OFFSET) para la consulta SQL
$offset = ($current_page - 1) * $logs_per_page;

// Obtener los logs con LIMIT y OFFSET, y filtrar por usuario si se seleccionó uno
if ($selected_user_id) {
    $logs = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$table_name} WHERE user_id = %d ORDER BY id DESC LIMIT %d OFFSET %d",
        $selected_user_id,
        $logs_per_page,
        $offset
    ));
} else {
    $logs = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$table_name} ORDER BY id DESC LIMIT %d OFFSET %d",
        $logs_per_page,
        $offset
    ));
}




// Mostrar el título
echo '<div class="container-fluid">';
echo '<span class="badge bg-dark">Creado por: Cesar Auris</span> <span class="badge bg-info text-dark">Version: '.SOLU_PRODUCT_LOGS_VERSION.'</span>';
echo '<h1 class="mb-4">Product Logs</h1>  ';

// Obtener la lista de administradores para el select
$admin_users = get_admin_users();

?>

    <div class="row" >
        <div class="col-4">
            <form method="GET" action="" class="form-row">
                <div  class="mb-3">
                    <label for="user_id">Filtrar por usuario administrador:</label>
                    <select name="user_id" id="user_id" class="form-select" onchange="this.form.submit()">
                        <option value="0">Todos los usuarios</option>
                        <?php foreach ($admin_users as $admin) : ?>
                            <option value="<?php echo esc_attr($admin['user_id']); ?>" <?php selected($selected_user_id, $admin['user_id']); ?>>
                                <?php echo esc_html($admin['display_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div  class="mb-3">
                    <!-- Asegurarse de mantener la página actual si hay paginación -->
                    <input type="hidden" name="page" value="solu-product-logs" />
                    <?php if (isset($_GET['paged'])) : ?>
                        <input type="hidden" name="paged" value="<?php echo esc_attr(intval($_GET['paged'])); ?>" />
                    <?php endif; ?>
                </div>

            </form>
        </div>

        <div class="col-4">

            <form method="POST" action="" class="form-row">
                <div  class="mb-3">
                    <input type="hidden" name="clear_logs" value="1">
                    <button type="submit" class="button button-primary" onclick="return confirm('¿Estás seguro de que deseas limpiar la tabla de logs? Esta acción no se puede deshacer.');">
                        Limpiar tabla de logs
                    </button>                </div>

            </form>
        </div>

    </div>



<?php
$site_url = site_url();
// Verificar si hay logs
if ($logs) {
    // Mostrar los registros en una tabla con estilo Bootstrap
    echo '<table class="table table-striped table-hover">';
    echo '<thead class="table-dark"><tr>';
    echo '<th>ID</th><th>Product ID</th><th>User</th><th>Name</th><th>URL</th><th>Action</th><th>Changes</th><th>IP Address</th><th>Date</th>';
    echo '</tr></thead><tbody>';

    foreach ($logs as $log) {
        echo '<tr>';
        echo '<td>' . esc_html($log->id) . '</td>';
        echo '<td>' . esc_html($log->product_id) . '</td>';
        echo '<td>' . esc_html($log->username) . '</td>';
        echo '<td>' . esc_html($log->product_name) . '</td>';
        echo '<td><a href="' . esc_url($log->product_url) . '" target="_blank">Ver Producto</a></td>';
        echo '<td>' . esc_html($log->action) . '</td>';

        // Formatear los cambios de forma más legible
        $changes = json_decode($log->changes, true);
        if ($changes) {
            echo '<td><ul>';
            foreach ($changes as $key => $change) {
                // Verificar si "before" y "after" son arrays
                // Usar la función customizeMessage para formatear los valores
                $before = customizeMessage($change['before'],$key,$site_url);
                $after = customizeMessage($change['after'],$key,$site_url);

//                $before = is_array($change['before']) ? implode(', ', $change['before']) : esc_html($change['before']);
//                $after = is_array($change['after']) ? implode(', ', $change['after']) : esc_html($change['after']);

                // Mostrar los cambios de forma legible
                echo '<li><strong>' . ucfirst($key) . ' modificado:</strong> <br><span class="badge bg-success">Ahora</span>: ' . $after . '<br><span class="badge bg-secondary">Antes</span>: ' . $before . '</li>';
            }
            echo '</ul></td>';
        } else {
            echo '<td>No changes</td>';
        }

        echo '<td>' . esc_html($log->ip_address) . '</td>';
        echo '<td>' . esc_html($log->created_at) . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';

    // Mostrar la paginación usando clases de Bootstrap
    $pagination_args = array(
        'base' => add_query_arg('paged', '%#%'),
        'format' => '',
        'prev_text' => __('&laquo; Previous'),
        'next_text' => __('Next &raquo;'),
        'total' => $total_pages,
        'current' => $current_page,
        'type' => 'array', // Asegurarse de que paginate_links devuelva un array
    );

    // Obtener los enlaces de paginación como un array
    $pagination_links = paginate_links($pagination_args);

    // Verificar que paginate_links devuelva un array antes de usar foreach
    if (is_array($pagination_links)) {
        echo '<nav aria-label="Page navigation example"><ul class="pagination justify-content-center">';
        foreach ($pagination_links as $link) {
            // Verificar si es el enlace activo (con la clase 'current')
            if (strpos($link, 'current') !== false) {
                // Convertir el <span> en un <a> para el enlace activo
                $link = str_replace('<span', '<a href="#"', $link);
                $link = str_replace('</span>', '</a>', $link);
                echo '<li class="page-item active">' . str_replace('page-numbers', 'page-link', $link) . '</li>';
            } else {
                echo '<li class="page-item">' . str_replace('page-numbers', 'page-link', $link) . '</li>';
            }
        }
        echo '</ul></nav>';
    }


} else {
    echo '<p class="alert alert-info">No logs found.</p>';
}

echo '</div>';
