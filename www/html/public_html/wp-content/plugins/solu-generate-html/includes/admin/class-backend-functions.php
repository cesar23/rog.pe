<?php

/**
 * Clase Singleton para manejar las funciones del backend del plugin
 * 
 * Esta clase implementa el patrón Singleton para optimizar el uso de memoria
 * y garantizar que solo exista una instancia en toda la aplicación.
 * 
 * @package Solu_Generate_HTML
 * @since 1.0.0
 * 
 * @example
 * // Obtener la instancia única de la clase
 * $backend = Solu_Generate_HTML_Backend_Functions::getInstance();
 * 
 * // Usar métodos de la clase
 * $categories = $backend->get_categories('product_cat');
 * $admins = $backend->get_admin_users();
 * 
 * @example
 * // Usar las funciones wrapper (método recomendado)
 * $categories = solu_generate_html_get_categories('product_cat');
 * $admins = solu_generate_html_get_admin_users();
 * 
 * @example
 * // Operaciones CRUD para códigos HTML
 * $html_data = array(
 *     'name_group' => 'Mi Grupo',
 *     'name_code' => 'Mi Código',
 *     'code' => '<div>Mi HTML</div>',
 *     'created_at' => current_time('mysql'),
 *     'update_at' => current_time('mysql')
 * );
 * 
 * // Insertar nuevo código
 * $id = insert_html_code($html_data);
 * 
 * // Obtener código por ID
 * $code = get_html_code($id);
 * 
 * // Actualizar código
 * $html_data['code'] = '<div>HTML Actualizado</div>';
 * $html_data['update_at'] = current_time('mysql');
 * update_html_code($id, $html_data);
 * 
 * // Eliminar código
 * delete_html_code($id);
 * 
 * // Obtener todos los códigos
 * $all_codes = get_all_html_codes();
 * 
 * // Obtener todos los grupos
 * $groups = solu_generate_html_get_all_groups();
 */
class Solu_Generate_HTML_Backend_Functions
{
  /**
   * Instancia única de la clase (Singleton)
   *
   * @var Solu_Generate_HTML_Backend_Functions
   */
  private static $instance = null;

  /**
   * Constructor privado de la clase (Singleton)
   */
  private function __construct()
  {
    // Constructor privado para evitar instanciación directa
  }

  /**
   * Obtiene la instancia única de la clase (Singleton)
   * 
   * Este método implementa el patrón Singleton, garantizando que solo
   * exista una instancia de la clase en toda la aplicación. Si la
   * instancia no existe, la crea; si ya existe, la devuelve.
   *
   * @return Solu_Generate_HTML_Backend_Functions La instancia única de la clase
   * 
   * @example
   * // Obtener la instancia única
   * $backend = Solu_Generate_HTML_Backend_Functions::getInstance();
   * 
   * // Usar métodos de la instancia
   * $categories = $backend->get_categories('product_cat');
   * $admins = $backend->get_admin_users();
   * 
   * @example
   * // Múltiples llamadas devuelven la misma instancia
   * $instance1 = Solu_Generate_HTML_Backend_Functions::getInstance();
   * $instance2 = Solu_Generate_HTML_Backend_Functions::getInstance();
   * var_dump($instance1 === $instance2); // true
   */
  public static function getInstance()
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /**
   * Previene la clonación de la instancia (Singleton)
   */
  public function __clone()
  {
    // Prevenir clonación
    throw new Exception('No se puede clonar un Singleton');
  }

  /**
   * Previene la deserialización de la instancia (Singleton)
   */
  public function __wakeup()
  {
    // Prevenir deserialización
    throw new Exception('No se puede deserializar un Singleton');
  }

  /**
   * Función que formatea el valor de un cambio, ya sea un array o un string
   * 
   * Esta función toma un valor de cambio y lo formatea de manera legible
   * según el tipo de cambio especificado. Para imágenes destacadas,
   * genera una etiqueta HTML con la imagen.
   *
   * @param mixed $change El valor del cambio (puede ser un array o un string)
   * @param string $key Tipo de cambio ('featured_image', 'image_gallery', 'content', 'title')
   * @param string $site_url URL del sitio
   * @return string El cambio formateado como texto legible
   * 
   * @example
   * $backend = Solu_Generate_HTML_Backend_Functions::getInstance();
   * 
   * // Formatear imagen destacada
   * $formatted = $backend->customize_message(123, 'featured_image', 'https://example.com');
   * // Resultado: '<img src="https://example.com/?p=123" width="100px" class="img-thumbnail">'
   * 
   * // Formatear contenido de texto
   * $formatted = $backend->customize_message('Mi contenido', 'content', 'https://example.com');
   * // Resultado: 'Mi contenido'
   * 
   * // Formatear array de galería
   * $formatted = $backend->customize_message(['img1.jpg', 'img2.jpg'], 'image_gallery', 'https://example.com');
   * // Resultado: 'img1.jpg, img2.jpg'
   */
  public function customize_message($change, $key, $site_url)
  {
    $type_change = $key; // Featured_image ,Image_gallery ,Content ,Title
    $result = "";
    if (is_array($change)) {
      $result .= implode(', ', array_map('esc_html', $change));
    } else {
      if ($type_change == "featured_image") {
        $result .= '<img src="' . $site_url . '/?p=' . esc_html($change) . '" width="100px" class="img-thumbnail" >';
      } else {
        $result .= esc_html($change);
      }
    }
    return $result;
  }

  /**
   * Función para obtener todos los usuarios con rol de administrador
   *
   * @return array Lista de nombres de usuario, correos electrónicos y IDs de los administradores
   */
  public function get_admin_users()
  {
    // Obtener todos los usuarios con rol de administrador
    $admin_users = get_users(array(
      'role'    => 'administrator', // Filtrar por rol de administrador
      'orderby' => 'display_name',  // Ordenar por nombre mostrado
      'order'   => 'ASC'            // Orden ascendente
    ));

    // Preparar un array para almacenar los datos
    $admins = array();

    // Recorrer los usuarios y almacenar el ID, nombre y el correo
    foreach ($admin_users as $user) {
      $admins[] = array(
        'user_id'      => $user->ID,         // Agregar el ID del usuario
        'display_name' => $user->display_name,
        'email'        => $user->user_email
      );
    }

    return $admins;
  }

  /**
   * Función para obtener el listado de todas las categorías
   * 
   * Esta función obtiene todas las categorías de una taxonomía específica
   * con validaciones de seguridad para evitar errores con términos inválidos.
   * Incluye filtros para categorías vacías, ordenamiento y límites.
   *
   * @param string $taxonomy Tipo de taxonomía (por defecto 'category' para posts, 'product_cat' para productos)
   * @param array $args Argumentos adicionales para la consulta de términos
   * @return array Lista de categorías con ID, nombre, slug y descripción
   * 
   * @example
   * $backend = Solu_Generate_HTML_Backend_Functions::getInstance();
   * 
   * // Obtener todas las categorías de productos
   * $product_categories = $backend->get_categories('product_cat');
   * 
   * // Obtener categorías de posts con argumentos específicos
   * $post_categories = $backend->get_categories('category', array(
   *     'hide_empty' => true,  // Solo categorías con contenido
   *     'orderby' => 'count',  // Ordenar por número de posts
   *     'order' => 'DESC',     // Orden descendente
   *     'number' => 10         // Limitar a 10 resultados
   * ));
   * 
   * // Obtener subcategorías de una categoría específica
   * $subcategories = $backend->get_categories('product_cat', array(
   *     'parent' => 15  // ID de la categoría padre
   * ));
   * 
   * @example
   * // Estructura de respuesta
   * array(
   *     array(
   *         'term_id' => 1,
   *         'name' => 'Electrónicos',
   *         'slug' => 'electronicos',
   *         'description' => 'Productos electrónicos',
   *         'count' => 25,
   *         'parent' => 0,
   *         'term_taxonomy_id' => 1
   *     ),
   *     // ... más categorías
   * )
   */
  public function get_categories($taxonomy = 'category', $args = array())
  {
    // Argumentos por defecto
    $default_args = array(
      'hide_empty' => false,  // Incluir categorías vacías
      'orderby'    => 'name', // Ordenar por nombre
      'order'      => 'ASC',  // Orden ascendente
      'number'     => 0       // Sin límite de resultados
    );

    // Combinar argumentos por defecto con los proporcionados
    $query_args = wp_parse_args($args, $default_args);

    // Preparar argumentos para get_terms
    $get_terms_args = array(
      'taxonomy'   => $taxonomy,
      'hide_empty' => $query_args['hide_empty'],
      'orderby'    => $query_args['orderby'],
      'order'      => $query_args['order'],
      'number'     => $query_args['number']
    );

    // Agregar filtro de parent si está especificado
    if (isset($query_args['parent'])) {
      $get_terms_args['parent'] = $query_args['parent'];
    }

    // Obtener los términos de la taxonomía
    $terms = get_terms($get_terms_args);

    // Verificar si hay errores
    if (is_wp_error($terms)) {
      return array();
    }

    // Preparar array para almacenar las categorías
    $categories = array();

    // Recorrer los términos y almacenar la información
    foreach ($terms as $term) {
      // Verificar que el término sea válido
      if (!$term || !isset($term->term_id) || !isset($term->name) || !isset($term->slug)) {
        continue; // Saltar términos inválidos
      }

      // Verificar que el slug no esté vacío o sea problemático
      if (empty($term->slug) || $term->slug === '' || $term->slug === null) {
        continue; // Saltar términos sin slug válido
      }

      $categories[] = array(
        'term_id'          => $term->term_id,
        'name'             => $term->name,
        'slug'             => $term->slug,
        'description'      => $term->description,
        'count'            => $term->count,
        'parent'           => $term->parent,
        'term_taxonomy_id' => $term->term_taxonomy_id
      );
    }

    return $categories;
  }

  /**
   * Función para obtener categorías de productos específicamente
   *
   * @param array $args Argumentos adicionales para la consulta
   * @param bool $parent_only Si es true, solo devuelve categorías padre (parent = 0)
   * @return array Lista de categorías de productos
   */
  public function get_product_categories($args = array(), $parent_only = false)
  {
    if ($parent_only) {
      // Agregar filtro para obtener solo categorías padre
      $args['parent'] = 0;
    }

    return $this->get_categories('product_cat', $args);
  }

  /**
   * Función para obtener categorías de posts específicamente
   *
   * @param array $args Argumentos adicionales para la consulta
   * @return array Lista de categorías de posts
   */
  public function get_post_categories($args = array())
  {
    return $this->get_categories('category', $args);
  }

  /**
   * Función para obtener el enlace seguro de una categoría
   *
   * @param int $term_id ID del término
   * @param string $taxonomy Taxonomía (por defecto 'product_cat')
   * @return string Enlace seguro o mensaje de error
   */
  public function get_safe_term_link($term_id, $taxonomy = 'product_cat')
  {
    $term_link = get_term_link($term_id, $taxonomy);

    if (is_wp_error($term_link)) {
      return false;
    }

    return $term_link;
  }

  /**
   * Función para obtener información segura de una categoría padre
   *
   * @param int $parent_id ID de la categoría padre
   * @param string $taxonomy Taxonomía (por defecto 'product_cat')
   * @return array|false Información de la categoría padre o false si no existe
   */
  public function get_safe_parent_category($parent_id, $taxonomy = 'product_cat')
  {
    if ($parent_id <= 0) {
      return false;
    }

    $parent = get_term($parent_id, $taxonomy);

    if (is_wp_error($parent) || !$parent || !$parent->term_id) {
      return false;
    }

    return array(
      'term_id' => $parent->term_id,
      'name' => $parent->name,
      'slug' => $parent->slug
    );
  }

  /**
   * Función para detectar y reportar categorías problemáticas
   *
   * @param string $taxonomy Taxonomía a verificar (por defecto 'product_cat')
   * @return array Lista de categorías con problemas
   */
  public function detect_problematic_categories($taxonomy = 'product_cat')
  {
    global $wpdb;

    $problems = array();

    // Obtener términos con slugs vacíos o nulos
    $empty_slugs = $wpdb->get_results($wpdb->prepare(
      "SELECT t.term_id, t.name, t.slug 
             FROM {$wpdb->terms} t 
             INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id 
             WHERE tt.taxonomy = %s AND (t.slug IS NULL OR t.slug = '' OR t.slug = 'null')",
      $taxonomy
    ));

    foreach ($empty_slugs as $term) {
      $problems[] = array(
        'type' => 'empty_slug',
        'term_id' => $term->term_id,
        'name' => $term->name,
        'description' => 'Término con slug vacío o nulo'
      );
    }

    // Obtener términos con padres inexistentes
    $orphaned_terms = $wpdb->get_results($wpdb->prepare(
      "SELECT t.term_id, t.name, tt.parent 
             FROM {$wpdb->terms} t 
             INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id 
             WHERE tt.taxonomy = %s AND tt.parent > 0 
             AND tt.parent NOT IN (SELECT term_id FROM {$wpdb->terms})",
      $taxonomy
    ));

    foreach ($orphaned_terms as $term) {
      $problems[] = array(
        'type' => 'orphaned_parent',
        'term_id' => $term->term_id,
        'name' => $term->name,
        'parent_id' => $term->parent,
        'description' => 'Término con categoría padre inexistente'
      );
    }

    return $problems;
  }

  // ====================================================
  // FUNCIONES CRUD PARA CÓDIGOS HTML
  // ====================================================

  /**
   * Inserta un nuevo código HTML en la base de datos
   * 
   * Esta función inserta un nuevo registro de código HTML en la tabla
   * personalizada del plugin. Incluye validación de errores y logging.
   *
   * @param array $data Array con los datos del código HTML
   * @return int|false El ID del registro insertado o false si hay error
   * 
   * @example
   * $backend = Solu_Generate_HTML_Backend_Functions::getInstance();
   * 
   * // Preparar datos del código HTML
   * $html_data = array(
   *     'name_group' => 'Componentes UI',
   *     'name_code' => 'Botón Principal',
   *     'code' => '<button class="btn-primary">Click aquí</button>',
   *     'created_at' => current_time('mysql'),
   *     'update_at' => current_time('mysql')
   * );
   * 
   * // Insertar el código
   * $id = $backend->insert_html_code($html_data);
   * 
   * if ($id !== false) {
   *     echo "Código HTML insertado con ID: " . $id;
   * } else {
   *     echo "Error al insertar el código HTML";
   * }
   * 
   * @example
   * // Usar la función wrapper
   * $id = insert_html_code($html_data);
   */
  public function insert_html_code($data)
  {
    global $wpdb;
    $table_name = SOLU_GENERATE_HTML_TABLE;

    $result = $wpdb->insert(
      $table_name,
      array(
        'name_group' => $data['name_group'],
        'name_code' => $data['name_code'],
        'code' => $data['code'],
        'created_at' => $data['created_at'],
        'update_at' => $data['update_at']
      ),
      array('%s', '%s', '%s', '%s', '%s')
    );

    if ($result === false) {
      solu_log("Error al insertar DB table {$table_name}: " . $wpdb->last_error, 'error');
      return false;
    }

    return $wpdb->insert_id;
  }

  /**
   * Actualiza un código HTML existente en la base de datos
   *
   * @param int $id ID del código HTML a actualizar
   * @param array $data Array con los datos actualizados
   * @return bool True si se actualizó correctamente, false si hay error
   */
  public function update_html_code($id, $data)
  {
    global $wpdb;
    $table_name = SOLU_GENERATE_HTML_TABLE;

    $result = $wpdb->update(
      $table_name,
      array(
        'name_group' => $data['name_group'],
        'name_code' => $data['name_code'],
        'code' => $data['code'],
        'update_at' => $data['update_at']
      ),
      array('id' => $id),
      array('%s', '%s', '%s', '%s'),
      array('%d')
    );

    if ($result === false) {
      solu_log("Error al actualizar DB table {$table_name}: " . $wpdb->last_error, 'error');
      error_log("Error al actualizar código HTML: " . $wpdb->last_error);
      return false;
    }

    return true;
  }

  /**
   * Elimina un código HTML de la base de datos
   *
   * @param int $id ID del código HTML a eliminar
   * @return bool True si se eliminó correctamente, false si hay error
   */
  public function delete_html_code($id)
  {
    global $wpdb;
    $table_name = SOLU_GENERATE_HTML_TABLE;

    $result = $wpdb->delete(
      $table_name,
      array('id' => $id),
      array('%d')
    );

    if ($result === false) {
      solu_log("Error al eliminar registro DB table {$table_name}: " . $wpdb->last_error, 'error');
      error_log("Error al eliminar código HTML: " . $wpdb->last_error);
      return false;
    }

    return true;
  }

  /**
   * Obtiene un código HTML por su ID
   *
   * @param int $id ID del código HTML
   * @return array|false Array con los datos del código HTML o false si no se encuentra
   */
  public function get_html_code($id)
  {
    global $wpdb;
    $table_name = SOLU_GENERATE_HTML_TABLE;

    $result = $wpdb->get_row(
      $wpdb->prepare(
        "SELECT * FROM {$table_name} WHERE id = %d",
        $id
      ),
      ARRAY_A
    );

    return $result;
  }

  /**
   * Obtiene todos los códigos HTML
   * 
   * Esta función recupera todos los códigos HTML almacenados en la base de datos,
   * ordenados por fecha de creación (más recientes primero).
   *
   * @return array Array con todos los códigos HTML
   * 
   * @example
   * $backend = Solu_Generate_HTML_Backend_Functions::getInstance();
   * 
   * // Obtener todos los códigos HTML
   * $all_codes = $backend->get_all_html_codes();
   * 
   * // Mostrar los códigos
   * foreach ($all_codes as $code) {
   *     echo "Grupo: " . $code['name_group'] . "\n";
   *     echo "Código: " . $code['name_code'] . "\n";
   *     echo "HTML: " . $code['code'] . "\n";
   *     echo "Creado: " . $code['created_at'] . "\n";
   *     echo "---\n";
   * }
   * 
   * @example
   * // Usar la función wrapper
   * $all_codes = get_all_html_codes();
   * 
   * @example
   * // Estructura de respuesta
   * array(
   *     array(
   *         'id' => 1,
   *         'name_group' => 'Componentes UI',
   *         'name_code' => 'Botón Principal',
   *         'code' => '<button class="btn-primary">Click aquí</button>',
   *         'created_at' => '2024-01-15 10:30:00',
   *         'update_at' => '2024-01-15 10:30:00'
   *     ),
   *     // ... más códigos
   * )
   */
  public function get_all_html_codes()
  {
    global $wpdb;
    $table_name = SOLU_GENERATE_HTML_TABLE;

    $results = $wpdb->get_results(
      "SELECT * FROM {$table_name} ORDER BY created_at DESC",
      ARRAY_A
    );

    return $results;
  }

  /**
   * Obtiene todos los grupos únicos de códigos HTML
   *
   * @return array Array con todos los grupos únicos
   */
  public function get_all_groups()
  {
    global $wpdb;
    $table_name = SOLU_GENERATE_HTML_TABLE;

    $results = $wpdb->get_results(
      "SELECT DISTINCT name_group FROM {$table_name} ORDER BY name_group ASC",
      ARRAY_A
    );

    return $results;
  }

  /**
   * Procesa y formatea códigos HTML usando StringUtils
   * 
   * Este método demuestra el uso de la clase Solu_Generate_HTML_StringUtils
   * para procesar y formatear códigos HTML de manera consistente.
   *
   * @param array $html_codes Array de códigos HTML a procesar
   * @param int $truncate_length Longitud máxima para truncar el código (por defecto 200)
   * @return array Array de códigos HTML procesados
   * 
   * @example
   * $backend = Solu_Generate_HTML_Backend_Functions::getInstance();
   * 
   * // Obtener todos los códigos
   * $all_codes = $backend->get_all_html_codes();
   * 
   * // Procesar los códigos con StringUtils
   * $processed_codes = $backend->process_html_codes_with_string_utils($all_codes, 150);
   * 
   * // Mostrar los códigos procesados
   * foreach ($processed_codes as $code) {
   *     echo "Grupo: " . $code['name_group'] . "\n";
   *     echo "Código: " . $code['name_code'] . "\n";
   *     echo "HTML (truncado): " . $code['code_truncated'] . "\n";
   *     echo "Slug: " . $code['slug'] . "\n";
   *     echo "---\n";
   * }
   */
  public function process_html_codes_with_string_utils($html_codes, $truncate_length = 200)
  {
    // Crear instancia de StringUtils
    $stringUtils = new Solu_Generate_HTML_StringUtils();

    $processed_codes = array();

    foreach ($html_codes as $code) {
      $processed_code = $code;

      // Truncar el código HTML para mostrar
      $processed_code['code_truncated'] = $stringUtils->truncate($code['code'], $truncate_length);

      // Generar slug del nombre del grupo
      $processed_code['group_slug'] = $stringUtils->generateSlug($code['name_group']);

      // Generar slug del nombre del código
      $processed_code['code_slug'] = $stringUtils->generateSlug($code['name_code']);

      // Limpiar y formatear el nombre del grupo
      $processed_code['name_group_clean'] = $stringUtils->titleCase($stringUtils->clean($code['name_group']));

      // Limpiar y formatear el nombre del código
      $processed_code['name_code_clean'] = $stringUtils->titleCase($stringUtils->clean($code['name_code']));

      // Contar caracteres del código HTML
      $processed_code['char_count'] = $stringUtils->charCount($code['code']);

      // Contar palabras del código HTML
      $processed_code['word_count'] = $stringUtils->wordCount($code['code']);

      $processed_codes[] = $processed_code;
    }

    return $processed_codes;
  }

  /**
   * Valida y limpia datos de entrada usando StringUtils
   * 
   * Este método utiliza StringUtils para validar y limpiar los datos
   * antes de insertarlos en la base de datos.
   *
   * @param array $data Datos a validar y limpiar
   * @return array Array con los datos validados y limpios
   * 
   * @example
   * $backend = Solu_Generate_HTML_Backend_Functions::getInstance();
   * 
   * $raw_data = array(
   *     'name_group' => '  Mi Grupo  ',
   *     'name_code' => 'Mi-Código_HTML',
   *     'code' => '<div>Mi HTML</div>'
   * );
   * 
   * $clean_data = $backend->validate_and_clean_data($raw_data);
   * // Resultado: array con datos limpios y validados
   */
  public function validate_and_clean_data($data)
  {
    $stringUtils = new Solu_Generate_HTML_StringUtils();

    $clean_data = array();

    // Limpiar y validar nombre del grupo
    if (isset($data['name_group'])) {
      $clean_data['name_group'] = $stringUtils->clean($data['name_group']);

      // Verificar que no esté vacío
      if ($stringUtils->isEmpty($clean_data['name_group'])) {
        throw new Exception('El nombre del grupo no puede estar vacío');
      }
    }

    // Limpiar y validar nombre del código
    if (isset($data['name_code'])) {
      $clean_data['name_code'] = $stringUtils->clean($data['name_code']);

      // Verificar que no esté vacío
      if ($stringUtils->isEmpty($clean_data['name_code'])) {
        throw new Exception('El nombre del código no puede estar vacío');
      }
    }

    // Limpiar código HTML
    if (isset($data['code'])) {
      $clean_data['code'] = $stringUtils->clean($data['code']);

      // Verificar que no esté vacío
      if ($stringUtils->isEmpty($clean_data['code'])) {
        throw new Exception('El código HTML no puede estar vacío');
      }
    }

    return $clean_data;
  }

  function get_code_counters($total_codes)
  {
    // variable de las  globales
    global $GLOBAL_TIPOS_GRUPOS;

    $counters = array();

    // Inicializar contadores
    foreach ($GLOBAL_TIPOS_GRUPOS as $prefix => $config) {
      $counters[$prefix] = 0;
    }

    // Contar códigos por tipo
    foreach ($total_codes as $code) {
      $found = false;
      foreach ($GLOBAL_TIPOS_GRUPOS as $prefix => $config) {
        if ($prefix !== 'other' && strpos($code['name_group'], $prefix) === 0) {
          $counters[$prefix]++;
          $found = true;
          break;
        }
      }
      if (!$found) {
        $counters['other']++;
      }
    }

    return $counters;
  }

}
