<?php

/**
 * Funciones para el manejo de precios y monedas.
 */

if (!function_exists('solu_format_currency')) {
    /**
     * Formatea un monto como moneda.
     *
     * @param float $amount El monto a formatear.
     * @param string $currencySymbol El símbolo de la moneda (por defecto, '$').
     * @param int $decimals El número de decimales (por defecto, 2).
     * @param string $decimalSeparator El separador decimal (por defecto, '.').
     * @param string $thousandsSeparator El separador de miles (por defecto, ',').
     * @return string El monto formateado como moneda.
     *
     * Ejemplo de uso:
     * $precio = 1234.56;
     * $precioFormateado = solu_format_currency($precio, 'S/ ', 2, ',', '.'); // Resultado: S/ 1.234,56
     */
    function solu_format_currency(float $amount, string $currencySymbol = '$', int $decimals = 2, string $decimalSeparator = '.', string $thousandsSeparator = ','): string {
        return $currencySymbol . number_format($amount, $decimals, $decimalSeparator, $thousandsSeparator);
    }
}

if (!function_exists('solu_increase_amount_by_percentage')) {
    /**
     * Aumenta un monto por un porcentaje dado.
     *
     * @param float $amount El monto a aumentar.
     * @param float $percentage El porcentaje a aumentar (ej. 10 para 10%).
     * @return float El monto aumentado.
     *
     * Ejemplo de uso:
     * $precio = 100;
     * $precioAumentado = solu_increase_amount_by_percentage($precio, 10); // Resultado: 110
     */
    function solu_increase_amount_by_percentage(float $amount, float $percentage): float {
        return $amount * (1 + ($percentage / 100));
    }
}

if (!function_exists('solu_decrease_amount_by_percentage')) {
    /**
     * Disminuye un monto por un porcentaje dado.
     *
     * @param float $amount El monto a disminuir.
     * @param float $percentage El porcentaje a disminuir (ej. 10 para 10%).
     * @return float El monto disminuido.
     *
     * Ejemplo de uso:
     * $precio = 100;
     * $precioDisminuido = solu_decrease_amount_by_percentage($precio, 10); // Resultado: 90
     */
    function solu_decrease_amount_by_percentage(float $amount, float $percentage): float {
        return $amount * (1 - ($percentage / 100));
    }
}

if (!function_exists('solu_convert_currency')) {
    /**
     * Convierte un monto de una moneda a otra (simulado).
     *
     * @param float $amount El monto a convertir.
     * @param float $exchangeRate La tasa de cambio.
     * @return float El monto convertido.
     *
     * Ejemplo de uso:
     * $precio = 100;
     * $tasaCambio = 3.5;
     * $precioConvertido = solu_convert_currency($precio, $tasaCambio); // Resultado: 350
     */
    function solu_convert_currency(float $amount, float $exchangeRate): float {
        return $amount * $exchangeRate;
    }
}