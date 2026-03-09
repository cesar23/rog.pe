<?php

function solu_currencies_exchange_add_admin_menus()
{
    global $allowed_emails_currencies;
    $current_user = wp_get_current_user();
    $admin = new Solu_Currencies_Exchange_Admin();
//    var_dump($current_user->user_email,$allowed_emails_currencies);

    if (in_array($current_user->user_email, $allowed_emails_currencies)) {
        add_menu_page(
            __('Solu Currencies Exchange', 'solu-currencies-exchange'), // Page title
            __('Listado de monedas', 'solu-currencies-exchange'),      // Menu title
            'manage_options',           // Capability required to access the menu
            'solu-currencies-exchange', // Menu slug (should be unique)
            array($admin, 'display_currencies_page'), // Callback function to display the page content
            'dashicons-money',          // Icon URL or Dashicon class (e.g., dashicons-money, dashicons-chart-bar)
            26                          // Position in the menu order
        );

        add_submenu_page(
            'solu-currencies-exchange',            // Parent slug (main menu page)
            __('Definir Moneda tipo Cambio', 'solu-currencies-exchange'),   // Page title
            __('Definir Moneda', 'solu-currencies-exchange'),        // Menu title
            'manage_options',                // Capability required
            'solu-currencies-exchange-definir-moneda',    // Menu slug (unique)
            array($admin, 'display_definir_moneda_page')    // Callback function to display the page content
        );

        add_submenu_page(
            'solu-currencies-exchange',            // Parent slug (main menu page)
            __('Acortar Titulos productos', 'solu-currencies-exchange'),   // Page title
            __('Acortar Titulos', 'solu-currencies-exchange'),        // Menu title
            'manage_options',                // Capability required
            'solu-currencies-exchange-shorten-titles',    // Menu slug (unique)
            array($admin, 'display_shorten_titles_page')    // Callback function to display the page content
        );

        add_submenu_page(
            'solu-currencies-exchange',            // Parent slug (main menu page)
            __('Ayuda e Información', 'solu-currencies-exchange'),   // Page title
            __('Ayuda', 'solu-currencies-exchange'),        // Menu title
            'manage_options',                // Capability required
            'solu-currencies-exchange-help',    // Menu slug (unique)
            array($admin, 'display_info_page')    // Callback function to display the page content
        );




    }
}