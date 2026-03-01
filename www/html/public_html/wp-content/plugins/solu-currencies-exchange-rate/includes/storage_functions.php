<?php

require_once SOLU_CURRENCIES_EXCHANGE_PATH . 'includes/entities/SoluCurrenciesExchange.php';
require_once SOLU_CURRENCIES_EXCHANGE_PATH . 'includes/entities/SoluCurrencyAlternative.php';

/**
 * Guarda las monedas locales en un archivo JSON.
 *
 * Obtiene todas las monedas locales (`currency_local=1`) de la base de datos
 * y las guarda como objetos SoluCurrenciesExchange en un archivo JSON.
 *
 * @return array El contenido JSON generado.
 * @global wpdb $wpdb Instancia global de la base de datos de WordPress.
 */
function saveStorageTable()
{
    global $wpdb;
    $json_file = SOLU_CURRENCIES_EXCHANGE_STORAGE_JSON;

    maybe_create_file($json_file);

    $table_name = SOLU_CURRENCIES_EXCHANGE_TABLE;
    $results = $wpdb->get_results("
        SELECT 
            id,  currency_name, currency_description, currency_symbol, currency_code, currency_value, currency_local, currency_order
        FROM {$table_name}
        where  active=1 ", ARRAY_A);
    $data = array();
    foreach ($results as $result) {
        $currency = new SoluCurrenciesExchange(
            $result['id'],
            '',
            '',
            $result['currency_name'],
            $result['currency_description'],
            $result['currency_symbol'],
            $result['currency_code'],
            $result['currency_value'],
            $result['currency_local'],
            $result['currency_order'],
            '',
            '',
            ''
        );
        $data[] = $currency;
    }
    $json = json_encode($data);
    file_put_contents($json_file, $json);
    return $data;
}

/**
 * Obtiene todas las monedas locales desde el archivo JSON.
 *
 * Lee el archivo JSON de almacenamiento. Si el archivo no existe o tiene más de 2 horas,
 * se regenera llamando a saveStorageTable().
 *
 * @return SoluCurrenciesExchange[] Un array de objetos SoluCurrenciesExchange.
 */
function getStorageTableAll()
{
    $json_file = SOLU_CURRENCIES_EXCHANGE_STORAGE_JSON;

    $hours = (2 * 3600); // 2 horas en segundos
    if (file_exists($json_file) && (time() - filemtime($json_file) < $hours)) {
        // Usar el archivo si existe y es reciente (< 2 horas)
        $data_array = saveStorageTable();
//        $data_json = json_decode($json, true);
        return $data_array;
    } else {
        $results = file_get_contents($json_file);
        $data_array= json_decode($results, true);
        return $data_array;
    }
}



/**
 * Obtiene una moneda específica por su código.
 *
 * Busca en el array de monedas locales una que coincida con el código de moneda proporcionado.
 *
 * @param string $currency El código de la moneda a buscar (ej. 'USD').
 * @return SoluCurrenciesExchange Devuelve un objeto SoluCurrenciesExchange, ya sea con los datos de la moneda encontrada o vacío si no se encuentra.
 */
function getStorageTableJsonRow($currency = 'USD'):SoluCurrenciesExchange
{
    $allResults = getStorageTableAll();
    $row = new SoluCurrenciesExchange();

    if (is_array($allResults)) {
        foreach ($allResults as $result) {
            if (is_object($result)) {
                $currency_code = $result->currency_code;
            } else {
                $currency_code = $result['currency_code'];
            }
            if ($currency_code === $currency) {
                $row = new SoluCurrenciesExchange(
                    is_object($result) ? $result->id : $result['id'],
                    is_object($result) ? $result->user_id : $result['user_id'],
                    is_object($result) ? $result->username : $result['username'],
                    is_object($result) ? $result->currency_name : $result['currency_name'],
                    is_object($result) ? $result->currency_description : $result['currency_description'],
                    is_object($result) ? $result->currency_symbol : $result['currency_symbol'],
                    is_object($result) ? $result->currency_code : $result['currency_code'],
                    is_object($result) ? $result->currency_value : $result['currency_value'],
                    is_object($result) ? $result->currency_local : $result['currency_local'],
                    is_object($result) ? $result->currency_order : $result['currency_order'],
                    is_object($result) ? $result->active : $result['active'],
                    is_object($result) ? $result->created_at : $result['created_at'],
                    is_object($result) ? $result->update_at : $result['update_at']
                );
                break;
            }
        }
    }

    return $row;
}

function getStorageTableJsonRowLocal():SoluCurrenciesExchange
{
    $allResults = getStorageTableAll();
    $row = new SoluCurrenciesExchange();

    if (is_array($allResults)) {
        foreach ($allResults as $result) {
            if (is_object($result)) {
                $currency_local = $result->currency_local;
            } else {
                $currency_local = $result['currency_local'];
            }
            if ($currency_local === '1') {
                $row = new SoluCurrenciesExchange(
                    is_object($result) ? $result->id : $result['id'],
                    is_object($result) ? $result->user_id : $result['user_id'],
                    is_object($result) ? $result->username : $result['username'],
                    is_object($result) ? $result->currency_name : $result['currency_name'],
                    is_object($result) ? $result->currency_description : $result['currency_description'],
                    is_object($result) ? $result->currency_symbol : $result['currency_symbol'],
                    is_object($result) ? $result->currency_code : $result['currency_code'],
                    is_object($result) ? $result->currency_value : $result['currency_value'],
                    is_object($result) ? $result->currency_local : $result['currency_local'],
                    is_object($result) ? $result->currency_order : $result['currency_order'],
                    is_object($result) ? $result->active : $result['active'],
                    is_object($result) ? $result->created_at : $result['created_at'],
                    is_object($result) ? $result->update_at : $result['update_at']
                );
                break;
            }
        }
    }

    return $row;
}


/**
 * Guarda la moneda alternativa en un archivo JSON.
 *
 * Recibe un objeto SoluCurrencyAlternative y lo guarda en un archivo JSON.
 *
 * @param SoluCurrencyAlternative $data El objeto SoluCurrencyAlternative a guardar.
 * @return SoluCurrencyAlternative|WP_Error El objeto SoluCurrencyAlternative guardado, o un objeto WP_Error si hay un error.
 */
function saveStorageCurrencyAlternative(SoluCurrencyAlternative $data)
{
    maybe_create_file(SOLU_CURRENCY_ALTERNATIVE_STORAGE_JSON);
    if (!($data instanceof SoluCurrencyAlternative)) {
        return new WP_Error('invalid_data', __('El parámetro debe ser un objeto de tipo SoluCurrencyAlternative.', 'solu-currencies-exchange'));
    }
    $json = json_encode($data);
    file_put_contents(SOLU_CURRENCY_ALTERNATIVE_STORAGE_JSON, $json);
    return $data;
}

/**
 * Obtiene la moneda alternativa desde un archivo JSON.
 *
 * Lee el archivo JSON y devuelve el objeto SoluCurrencyAlternative.
 *
 * @return SoluCurrencyAlternative|null El objeto SoluCurrencyAlternative, o null si no se encuentra.
 */
function getStorageCurrencyAlternative()
{
    $json_file = SOLU_CURRENCY_ALTERNATIVE_STORAGE_JSON;

    if (!file_exists($json_file)) {
        return null;
    }

    $json = file_get_contents($json_file);
    $data = json_decode($json);

    if (empty($data)) {
        return null;
    }

    $currency = new SoluCurrencyAlternative(
        currency_alternative_code: $data->currency_alternative_code
    );

    return $currency;
}
/**
 * Crea un archivo si no existe y asigna permisos.
 *
 * @param string $filepath La ruta del archivo a crear.
 * @return bool True si el archivo se creó o ya existía, false si hubo un error.
 */
function maybe_create_file( $filepath ) {
    $dir = dirname( $filepath );
    if ( ! is_dir( $dir ) ) {
        $mkdir = @mkdir( $dir, 0755, true ); // Crea el directorio recursivamente
        if ( ! $mkdir ) {
            // Log de error si no se pudo crear el directorio
            wp_die(
                'No se pudo crear el directorio: ' . $dir,
                "Personalizado del Plugin:".SOLU_CURRENCIES_EXCHANGE_NAME_PLUGIN, // Título de la página de error
                array(
                    'response'  => 500, // Código de estado HTTP (ej. 500 Internal Server Error)
                    'back_link' => true // Muestra un enlace para volver a la página anterior
                )
            );
            return false;
        }
    }
    if ( ! file_exists( $filepath ) ) {
        $file = fopen( $filepath, 'w' );
        if ( $file ) {
            fclose( $file );
            $chmod = @chmod( $filepath, 0644 ); // Permisos para WordPress
            if ( $chmod ) {
                return true;
            } else {
                // Log de error si no se pudieron establecer los permisos
                wp_die(
                    'No se pudieron establecer los permisos para el archivo: ' . $filepath,
                    "Personalizado del Plugin:".SOLU_CURRENCIES_EXCHANGE_NAME_PLUGIN, // Título de la página de error
                    array(
                        'response'  => 500, // Código de estado HTTP (ej. 500 Internal Server Error)
                        'back_link' => true // Muestra un enlace para volver a la página anterior
                    )
                );
                return false;
            }
        } else {
            // Log de error si no se pudo crear el archivo
             wp_die(
                'No se pudo crear el archivo: ' . $filepath,
                "Personalizado del Plugin:".SOLU_CURRENCIES_EXCHANGE_NAME_PLUGIN, // Título de la página de error
                array(
                    'response'  => 500, // Código de estado HTTP (ej. 500 Internal Server Error)
                    'back_link' => true // Muestra un enlace para volver a la página anterior
                )
            );
            return false;
        }
    }
    return true; // El archivo ya existe
}