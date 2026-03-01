<?php

/**
 * Clase para manejar notificaciones del admin de WordPress
 * 
 * Proporciona métodos para mostrar diferentes tipos de notificaciones
 * (error, warning, success, info) en el panel de administración.
 * 
 * @package Solu_Admin_Utils
 * @since 1.2.0
 * @author César Auris [perucaos@gmail.com]
 * 
 * @example
 * // Mostrar notificación de error
 * AdminNotices::show_error('Plugin no activado: WooCommerce no está disponible');
 * 
 * @example
 * // Mostrar notificación de éxito
 * AdminNotices::show_success('Plugin activado correctamente');
 */

if (!defined('ABSPATH')) exit;

class AdminNotices {

  /**
   * Array para almacenar las notificaciones
   * 
   * @var array
   * @since 1.2.0
   */
  private static $notices = [];

  /**
   * Inicializa el sistema de notificaciones
   * 
   * @return void
   * @since 1.2.0
   */
  public static function init(): void {
    add_action('admin_notices', [self::class, 'display_notices']);
  }

  /**
   * Agrega una notificación de error
   * 
   * @param string $message Mensaje a mostrar
   * @param bool $dismissible Si la notificación se puede cerrar
   * @return void
   * @since 1.2.0
   */
  public static function show_error(string $message, bool $dismissible = true): void {
    self::add_notice('error', $message, $dismissible);
  }

  /**
   * Agrega una notificación de advertencia
   * 
   * @param string $message Mensaje a mostrar
   * @param bool $dismissible Si la notificación se puede cerrar
   * @return void
   * @since 1.2.0
   */
  public static function show_warning(string $message, bool $dismissible = true): void {
    self::add_notice('warning', $message, $dismissible);
  }

  /**
   * Agrega una notificación de éxito
   * 
   * @param string $message Mensaje a mostrar
   * @param bool $dismissible Si la notificación se puede cerrar
   * @return void
   * @since 1.2.0
   */
  public static function show_success(string $message, bool $dismissible = true): void {
    self::add_notice('success', $message, $dismissible);
  }

  /**
   * Agrega una notificación de información
   * 
   * @param string $message Mensaje a mostrar
   * @param bool $dismissible Si la notificación se puede cerrar
   * @return void
   * @since 1.2.0
   */
  public static function show_info(string $message, bool $dismissible = true): void {
    self::add_notice('info', $message, $dismissible);
  }

  /**
   * Agrega una notificación personalizada
   * 
   * @param string $type Tipo de notificación (error, warning, success, info)
   * @param string $message Mensaje a mostrar
   * @param bool $dismissible Si la notificación se puede cerrar
   * @return void
   * @since 1.2.0
   */
  public static function add_notice(string $type, string $message, bool $dismissible = true): void {
    self::$notices[] = [
      'type' => $type,
      'message' => $message,
      'dismissible' => $dismissible
    ];
  }

  /**
   * Muestra todas las notificaciones almacenadas
   * 
   * @return void
   * @since 1.2.0
   */
  public static function display_notices(): void {
    foreach (self::$notices as $notice) {
      $dismissible_class = $notice['dismissible'] ? 'is-dismissible' : '';
      $icon = self::get_icon_for_type($notice['type']);
      
      printf(
        '<div class="notice notice-%s %s">
          <p>%s %s</p>
        </div>',
        esc_attr($notice['type']),
        esc_attr($dismissible_class),
        $icon,
        wp_kses_post($notice['message'])
      );
    }
    
    // Limpiar las notificaciones después de mostrarlas
    self::$notices = [];
  }

  /**
   * Obtiene el ícono correspondiente al tipo de notificación
   * 
   * @param string $type Tipo de notificación
   * @return string Ícono HTML
   * @since 1.2.0
   */
  private static function get_icon_for_type(string $type): string {
    $icons = [
      'error' => '<span style="color: #dc3232;">⚠️</span>',
      'warning' => '<span style="color: #ffb900;">⚠️</span>',
      'success' => '<span style="color: #46b450;">✅</span>',
      'info' => '<span style="color: #00a0d2;">ℹ️</span>'
    ];

    return $icons[$type] ?? '';
  }

  /**
   * Muestra una notificación específica para dependencias faltantes
   * 
   * @param string $plugin_name Nombre del plugin requerido
   * @param string $dependency_name Nombre de la dependencia faltante
   * @return void
   * @since 1.2.0
   */
  public static function show_missing_dependency(string $plugin_name, string $dependency_name): void {
    $message = sprintf(
      '<strong>%s</strong> requiere que <strong>%s</strong> esté activado.',
      esc_html($plugin_name),
      esc_html($dependency_name)
    );
    
    self::show_error($message);
  }

  /**
   * Limpia todas las notificaciones almacenadas
   * 
   * @return void
   * @since 1.2.0
   */
  public static function clear_notices(): void {
    self::$notices = [];
  }
}
