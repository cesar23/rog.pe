<?php
/**
 * Archivo para definir las constantes del plugin.
 */

$solu_currencies = array(
    'PEN' => array(
        'currency_name' => 'Soles',
        'currency_description' => 'Soles',
        'currency_symbol' => 'S/.',
        'image' => SOLU_CURRENCIES_EXCHANGE_URL . 'assets/img/PEN.svg'
    ),
    'USD' => array(
        'currency_name' => 'Dolares',
        'currency_description' => 'Dólares',
        'currency_symbol' => '$',
        'image' => SOLU_CURRENCIES_EXCHANGE_URL . 'assets/img/USD.svg'
    ),
    'EUR' => array(
        'currency_name' => 'Euros',
        'currency_description' => 'Euros',
        'currency_symbol' => '€',
        'image' => SOLU_CURRENCIES_EXCHANGE_URL . 'assets/img/EUR.svg'
    )
);

// SOLO ESTOS USUARIOS PUEDEN VER EL MENU
$allowed_emails_currencies = array('perucaos@gmail.com','cgms@rog.pe', 'editor2@solucionesssystem.com', 'juan@gmail.com', 'ventas@pcbyte.com.pe'); // Solo estos correos tendrán acceso