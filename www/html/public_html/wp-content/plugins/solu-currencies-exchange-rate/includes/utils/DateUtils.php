<?php

if (!function_exists('solu_format_date')) {
    /**
     * Formats a date according to the specified format.
     *
     * @param string $date The date to format (e.g., 'YYYY-MM-DD').
     * @param string $format The desired format (e.g., 'MM/DD/YYYY').
     * @return string The formatted date, or an empty string if the date is invalid.
     */
    function solu_format_date(string $date, string $format): string
    {
        try {
            $dateTime = new DateTime($date, new DateTimeZone('America/Lima'));
            return $dateTime->format($format);
        } catch (Exception $e) {
            return '';
        }
    }
}

if (!function_exists('solu_validate_date')) {
    /**
     * Validates if a date is valid.
     *
     * @param string $date The date to validate (e.g., 'YYYY-MM-DD').
     * @param string $format The format to validate against (e.g., 'YYYY-MM-DD').  Defaults to 'YYYY-MM-DD'.
     * @return bool True if the date is valid, false otherwise.
     */
    function solu_validate_date(string $date, string $format = 'Y-m-d'): bool
    {
        $dateTime = DateTime::createFromFormat($format, $date, new DateTimeZone('America/Lima'));
        return $dateTime && ($dateTime->format($format) === $date);
    }
}

if (!function_exists('solu_date_difference')) {
    /**
     * Calculates the difference between two dates in days.
     *
     * @param string $date1 The first date (e.g., 'YYYY-MM-DD').
     * @param string $date2 The second date (e.g., 'YYYY-MM-DD').
     * @return int The difference between the two dates in days, or 0 if either date is invalid.
     */
    function solu_date_difference(string $date1, string $date2): int
    {
        try {
            $dateTime1 = new DateTime($date1, new DateTimeZone('America/Lima'));
            $dateTime2 = new DateTime($date2, new DateTimeZone('America/Lima'));
            $interval = $dateTime1->diff($dateTime2);
            return (int) $interval->format('%a');
        } catch (Exception $e) {
            return 0;
        }
    }
}

if (!function_exists('solu_first_day_of_month')) {
    /**
     * Gets the first day of the month for a given date.
     *
     * @param string $date The date to get the first day of the month from (e.g., 'YYYY-MM-DD').
     * @param string $format The desired format (e.g., 'YYYY-MM-DD').
     * @return string The first day of the month in the specified format, or an empty string if the date is invalid.
     */
    function solu_first_day_of_month(string $date, string $format = 'Y-m-d'): string
    {
        try {
            $dateTime = new DateTime($date, new DateTimeZone('America/Lima'));
            $firstDayOfMonth = $dateTime->modify('first day of this month');
            return $firstDayOfMonth->format($format);
        } catch (Exception $e) {
            return '';
        }
    }
}

if (!function_exists('solu_get_date_hour_pe')) {
    /**
     * Gets the current date and time in America/Lima, and formatted as YYYY-MM-DD_HH:MM:SS.
     *
     * @return string The formatted date and time string.
     */
    function solu_get_date_hour_pe(): string
    {
        $dateTime = new DateTime("now", new DateTimeZone("America/Lima"));
        return $dateTime->format('Y-m-d_H:i:s');
    }
}

if (!function_exists('solu_validate_currency_fields')) {
    function solu_validate_currency_fields($data)
    {
        if (empty($data['country']) || empty($data['currency']) || empty($data['code'])) {
            echo '<div class="error"><p>Los campos País, Moneda y Código son obligatorios. <a href="?page=solu-currencies-exchange">Regresar</a></p></div>';
            return false;
        }
        return true;
    }
}
