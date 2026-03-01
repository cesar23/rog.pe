<?php

/**
 * Funciones para manejar los menús de administración
 */

function solu_admin_utils_add_admin_menus()
{
  // Usar la nueva función de seguridad
  if (solu_admin_utils_user_can_access()) {
    $classInicio = new Solu_Admin_Utils_Home();
    // Menú principal - Inicio
    add_menu_page(
      __('Admin Utils', 'solu-admin-utils'),
      __('Admin Utils', 'solu-admin-utils'),
      'manage_options',
      SOLU_ADMIN_UTIL_NAME_PLUGIN.'-home',
      array($classInicio, 'display_inicio_page'),
      'dashicons-admin-tools',
      26
    );

    // Submenú de ayuda
    add_submenu_page(
      SOLU_ADMIN_UTIL_NAME_PLUGIN.'-home',
      __('Ayuda e Información', 'solu-admin-utils'),
      __('Ayuda', 'solu-admin-utils'),
      'manage_options',
      SOLU_ADMIN_UTIL_NAME_PLUGIN.'-help',
      array($classInicio, 'display_help_page')
    );
  }
}

add_action('admin_menu', 'solu_admin_utils_add_admin_menus');
