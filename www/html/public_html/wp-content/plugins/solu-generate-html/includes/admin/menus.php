<?php

/**
 * Funciones para manejar los menús de administración
 */

function solu_generate_html_add_admin_menus()
{
  global $allowed_emails;
  $current_user = wp_get_current_user();
  $classCategoria = new Solu_Generate_HTML_Categoria();
  $classInicio = new Solu_Generate_HTML_Inicio();

  if (in_array($current_user->user_email, $allowed_emails)) {
    // Menú principal - Inicio
    add_menu_page(
      __('Generador de HTML', 'solu-generate-html'),
      __('Generador de HTML', 'solu-generate-html'),
      'manage_options',
      'solu-generate-html-inicio',
      array($classInicio, 'display_inicio_page'),
      'dashicons-editor-code',
      26
    );

    // Submenú de generar HTML
    add_submenu_page(
      'solu-generate-html-inicio',
      __('Generar Códigos HTML', 'solu-generate-html'),
      __('HTML Categorias', 'solu-generate-html'),
      'manage_options',
      'solu-generate-html',
      array($classCategoria, 'display_main_page')
    );

    // Submenú de ayuda
    add_submenu_page(
      'solu-generate-html-inicio',
      __('Ayuda e Información', 'solu-generate-html'),
      __('Ayuda', 'solu-generate-html'),
      'manage_options',
      'solu-generate-html-help',
      array($classCategoria, 'display_help_page')
    );
  }
}

add_action('admin_menu', 'solu_generate_html_add_admin_menus');
