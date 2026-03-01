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
    function solu_format_currency(float $amount, string $currencySymbol = '$', int $decimals = 2, string $decimalSeparator = '.', string $thousandsSeparator = ','): string
    {
        return $currencySymbol . number_format($amount, $decimals, $decimalSeparator, $thousandsSeparator);
    }
}

if (!function_exists('solu_parse_formatted_number')) {
    /**
     * Convierte un string formateado a un número float.
     * 
     * Esta función elimina separadores de miles y convierte separadores decimales
     * para obtener un número válido.
     *
     * @param string $formatted_number El número formateado como string.
     * @return float El número como float.
     *
     * Ejemplo de uso:
     * $numero = solu_parse_formatted_number('1,234.56'); // Resultado: 1234.56
     * $numero = solu_parse_formatted_number('1.234,56'); // Resultado: 1234.56
     */
    function solu_parse_formatted_number(string $formatted_number): float
    {
        // Eliminar espacios y validar entrada
        $number = trim($formatted_number);

        if (empty($number)) {
            return 0.0;
        }

        // Si contiene coma como último separador (formato europeo: 1.234,56)
        if (strpos($number, ',') !== false && strpos($number, '.') !== false) {
            if (strrpos($number, ',') > strrpos($number, '.')) {
                // Formato europeo: 1.234,56
                $number = str_replace('.', '', $number); // Eliminar separador de miles
                $number = str_replace(',', '.', $number); // Convertir separador decimal
            } else {
                // Formato americano: 1,234.56
                $number = str_replace(',', '', $number); // Eliminar separador de miles
            }
        } else if (strpos($number, ',') !== false) {
            // Solo tiene comas, puede ser separador decimal o de miles
            $comma_count = substr_count($number, ',');
            if ($comma_count == 1 && strlen(substr($number, strrpos($number, ',') + 1)) <= 2) {
                // Es separador decimal
                $number = str_replace(',', '.', $number);
            } else {
                // Es separador de miles
                $number = str_replace(',', '', $number);
            }
        }

        $result = floatval($number);

        // Validar que el resultado sea un número válido
        if (!is_finite($result)) {
            throw new InvalidArgumentException("Invalid number format: {$formatted_number}");
        }

        return $result;
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
    function solu_increase_amount_by_percentage(float $amount, float $percentage): float
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Amount cannot be negative');
        }
        if ($percentage < 0) {
            throw new InvalidArgumentException('Percentage cannot be negative');
        }
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
    function solu_decrease_amount_by_percentage(float $amount, float $percentage): float
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Amount cannot be negative');
        }
        if ($percentage < 0 || $percentage > 100) {
            throw new InvalidArgumentException('Percentage must be between 0 and 100');
        }
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
    function solu_convert_currency(float $amount, float $exchangeRate): float
    {
        return $amount * $exchangeRate;
    }
}
