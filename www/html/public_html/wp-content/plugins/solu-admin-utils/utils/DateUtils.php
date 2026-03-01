<?php

/**
 * Solu_Admin_Utils_DateUtils - Clase utilitaria para manejo de fechas
 * 
 * Esta clase proporciona métodos para el manejo, formateo, validación y manipulación
 * de fechas en el plugin Solu Admin Utils. Incluye soporte para diferentes zonas
 * horarias y operaciones comunes con fechas.
 * 
 * @package Solu_Admin_Utils
 * @since 1.2.0
 * 
 * @example
 * // Usar función de conveniencia (recomendado)
 * $dateUtils = solu_date_utils();
 * 
 * // O crear instancia directamente
 * $dateUtils = new Solu_Admin_Utils_DateUtils();
 * 
 * // Obtener fecha y hora actual
 * $fecha_actual = $dateUtils->getCurrentDate(); // 2024-01-15
 * $hora_actual = $dateUtils->getCurrentTime(); // 14:30:25
 * $fecha_hora = $dateUtils->getCurrentDateTime(); // 2024-01-15 14:30:25
 * 
 * @example
 * // Zonas horarias disponibles para usar en el constructor
 * 
 * // América del Norte (usando función de conveniencia)
 * $dateUtilsNY = solu_date_utils('America/New_York');        // Nueva York (EST/EDT)
 * $dateUtilsLA = solu_date_utils('America/Los_Angeles');     // Los Ángeles (PST/PDT)
 * $dateUtilsChicago = solu_date_utils('America/Chicago');    // Chicago (CST/CDT)
 * $dateUtilsDenver = solu_date_utils('America/Denver');      // Denver (MST/MDT)
 * $dateUtilsToronto = solu_date_utils('America/Toronto');    // Toronto (EST/EDT)
 * $dateUtilsVancouver = solu_date_utils('America/Vancouver'); // Vancouver (PST/PDT)
 * 
 * // América del Sur
 * $dateUtilsLima = new Solu_Generate_HTML_DateUtils('America/Lima');          // Lima, Perú (PET)
 * $dateUtilsBogota = new Solu_Generate_HTML_DateUtils('America/Bogota');      // Bogotá, Colombia (COT)
 * $dateUtilsSantiago = new Solu_Generate_HTML_DateUtils('America/Santiago');  // Santiago, Chile (CLT/CLST)
 * $dateUtilsBuenosAires = new Solu_Generate_HTML_DateUtils('America/Argentina/Buenos_Aires'); // Buenos Aires (ART)
 * $dateUtilsSaoPaulo = new Solu_Generate_HTML_DateUtils('America/Sao_Paulo'); // São Paulo, Brasil (BRT/BRST)
 * $dateUtilsCaracas = new Solu_Generate_HTML_DateUtils('America/Caracas');    // Caracas, Venezuela (VET)
 * $dateUtilsQuito = new Solu_Generate_HTML_DateUtils('America/Guayaquil');    // Quito, Ecuador (ECT)
 * 
 * // Europa
 * $dateUtilsMadrid = new Solu_Generate_HTML_DateUtils('Europe/Madrid');       // Madrid, España (CET/CEST)
 * $dateUtilsLondon = new Solu_Generate_HTML_DateUtils('Europe/London');       // Londres, Reino Unido (GMT/BST)
 * $dateUtilsParis = new Solu_Generate_HTML_DateUtils('Europe/Paris');         // París, Francia (CET/CEST)
 * $dateUtilsBerlin = new Solu_Generate_HTML_DateUtils('Europe/Berlin');       // Berlín, Alemania (CET/CEST)
 * $dateUtilsRome = new Solu_Generate_HTML_DateUtils('Europe/Rome');           // Roma, Italia (CET/CEST)
 * $dateUtilsAmsterdam = new Solu_Generate_HTML_DateUtils('Europe/Amsterdam'); // Ámsterdam, Países Bajos (CET/CEST)
 * $dateUtilsMoscow = new Solu_Generate_HTML_DateUtils('Europe/Moscow');       // Moscú, Rusia (MSK)
 * $dateUtilsAthens = new Solu_Generate_HTML_DateUtils('Europe/Athens');       // Atenas, Grecia (EET/EEST)
 * 
 * // Asia
 * $dateUtilsTokyo = new Solu_Generate_HTML_DateUtils('Asia/Tokyo');           // Tokio, Japón (JST)
 * $dateUtilsBeijing = new Solu_Generate_HTML_DateUtils('Asia/Shanghai');      // Beijing, China (CST)
 * $dateUtilsSeoul = new Solu_Generate_HTML_DateUtils('Asia/Seoul');           // Seúl, Corea del Sur (KST)
 * $dateUtilsSingapore = new Solu_Generate_HTML_DateUtils('Asia/Singapore');   // Singapur (SGT)
 * $dateUtilsBangkok = new Solu_Generate_HTML_DateUtils('Asia/Bangkok');       // Bangkok, Tailandia (ICT)
 * $dateUtilsJakarta = new Solu_Generate_HTML_DateUtils('Asia/Jakarta');       // Yakarta, Indonesia (WIB)
 * $dateUtilsManila = new Solu_Generate_HTML_DateUtils('Asia/Manila');         // Manila, Filipinas (PHT)
 * $dateUtilsDubai = new Solu_Generate_HTML_DateUtils('Asia/Dubai');           // Dubái, UAE (GST)
 * $dateUtilsMumbai = new Solu_Generate_HTML_DateUtils('Asia/Kolkata');        // Mumbai, India (IST)
 * 
 * // Oceanía
 * $dateUtilsSydney = new Solu_Generate_HTML_DateUtils('Australia/Sydney');    // Sídney, Australia (AEST/AEDT)
 * $dateUtilsMelbourne = new Solu_Generate_HTML_DateUtils('Australia/Melbourne'); // Melbourne, Australia (AEST/AEDT)
 * $dateUtilsAuckland = new Solu_Generate_HTML_DateUtils('Pacific/Auckland');  // Auckland, Nueva Zelanda (NZST/NZDT)
 * $dateUtilsHonolulu = new Solu_Generate_HTML_DateUtils('Pacific/Honolulu');  // Honolulu, Hawaii (HST)
 * 
 * // África
 * $dateUtilsCairo = new Solu_Generate_HTML_DateUtils('Africa/Cairo');         // El Cairo, Egipto (EET/EEST)
 * $dateUtilsJohannesburg = new Solu_Generate_HTML_DateUtils('Africa/Johannesburg'); // Johannesburgo, Sudáfrica (SAST)
 * $dateUtilsLagos = new Solu_Generate_HTML_DateUtils('Africa/Lagos');         // Lagos, Nigeria (WAT)
 * $dateUtilsCasablanca = new Solu_Generate_HTML_DateUtils('Africa/Casablanca'); // Casablanca, Marruecos (WET/WEST)
 * 
 * @example
 * // Comparar fechas en diferentes zonas horarias
 * $dateUtilsLima = new Solu_Generate_HTML_DateUtils('America/Lima');
 * $dateUtilsNY = new Solu_Generate_HTML_DateUtils('America/New_York');
 * $dateUtilsMadrid = new Solu_Generate_HTML_DateUtils('Europe/Madrid');
 * $dateUtilsTokyo = new Solu_Generate_HTML_DateUtils('Asia/Tokyo');
 * 
 * echo "Lima: " . $dateUtilsLima->getCurrentDateTime() . "\n";
 * echo "Nueva York: " . $dateUtilsNY->getCurrentDateTime() . "\n";
 * echo "Madrid: " . $dateUtilsMadrid->getCurrentDateTime() . "\n";
 * echo "Tokio: " . $dateUtilsTokyo->getCurrentDateTime() . "\n";
 * 
 * 
 * @see Solu_Generate_HTML_StringUtils Para operaciones con strings
 * @see Solu_Generate_HTML_Backend_Functions Para funciones del backend
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Solo definir la clase si no existe ya
if (!class_exists('Solu_Admin_Utils_DateUtils')) {
    
class Solu_Admin_Utils_DateUtils
{
    /**
     * Zona horaria por defecto
     * 
     * @var string
     */
    private $timezone;

    /**
     * Constructor de la clase
     * 
     * @param string $timezone Zona horaria (por defecto 'America/Lima')
     */
    public function __construct($timezone = 'America/Lima')
    {
        $this->timezone = $timezone;
    }

    /**
     * Formatea una fecha según el formato especificado
     *
     * @param string $date La fecha a formatear (ej: 'YYYY-MM-DD')
     * @param string $format El formato deseado (ej: 'MM/DD/YYYY')
     * @return string La fecha formateada, o cadena vacía si la fecha es inválida
     */
    public function formatDate(string $date, string $format): string
    {
        try {
            $dateTime = new DateTime($date, new DateTimeZone($this->timezone));
            return $dateTime->format($format);
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Valida si una fecha es válida
     *
     * @param string $date La fecha a validar (ej: 'YYYY-MM-DD')
     * @param string $format El formato para validar (ej: 'YYYY-MM-DD'). Por defecto 'Y-m-d'
     * @return bool True si la fecha es válida, false en caso contrario
     */
    public function validateDate(string $date, string $format = 'Y-m-d'): bool
    {
        $dateTime = DateTime::createFromFormat($format, $date, new DateTimeZone($this->timezone));
        return $dateTime && ($dateTime->format($format) === $date);
    }

    /**
     * Calcula la diferencia entre dos fechas en días
     *
     * @param string $date1 La primera fecha (ej: 'YYYY-MM-DD')
     * @param string $date2 La segunda fecha (ej: 'YYYY-MM-DD')
     * @return int La diferencia entre las dos fechas en días, o 0 si alguna fecha es inválida
     */
    public function dateDifference(string $date1, string $date2): int
    {
        try {
            $dateTime1 = new DateTime($date1, new DateTimeZone($this->timezone));
            $dateTime2 = new DateTime($date2, new DateTimeZone($this->timezone));
            $interval = $dateTime1->diff($dateTime2);
            return (int) $interval->format('%a');
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Obtiene el primer día del mes para una fecha dada
     *
     * @param string $date La fecha para obtener el primer día del mes (ej: 'YYYY-MM-DD')
     * @param string $format El formato deseado (ej: 'YYYY-MM-DD')
     * @return string El primer día del mes en el formato especificado, o cadena vacía si la fecha es inválida
     */
    public function firstDayOfMonth(string $date, string $format = 'Y-m-d'): string
    {
        try {
            $dateTime = new DateTime($date, new DateTimeZone($this->timezone));
            $firstDayOfMonth = $dateTime->modify('first day of this month');
            return $firstDayOfMonth->format($format);
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Obtiene el último día del mes para una fecha dada
     *
     * @param string $date La fecha para obtener el último día del mes (ej: 'YYYY-MM-DD')
     * @param string $format El formato deseado (ej: 'YYYY-MM-DD')
     * @return string El último día del mes en el formato especificado, o cadena vacía si la fecha es inválida
     */
    public function lastDayOfMonth(string $date, string $format = 'Y-m-d'): string
    {
        try {
            $dateTime = new DateTime($date, new DateTimeZone($this->timezone));
            $lastDayOfMonth = $dateTime->modify('last day of this month');
            return $lastDayOfMonth->format($format);
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Obtiene la fecha y hora actual en la zona horaria configurada
     *
     * @param string $format El formato deseado (por defecto 'Y-m-d H:i:s')
     * @return string La fecha y hora actual formateada
     */
    public function getCurrentDateTime(string $format = 'Y-m-d H:i:s'): string
    {
        $dateTime = new DateTime("now", new DateTimeZone($this->timezone));
        return $dateTime->format($format);
    }

    /**
     * Obtiene la fecha y hora actual en formato YYYY-MM-DD_HH:MM:SS
     *
     * @return string La fecha y hora actual formateada
     */
    public function getCurrentDateTimeFormatted(): string
    {
        return $this->getCurrentDateTime('Y-m-d_H:i:s');
    }

    /**
     * Obtiene solo la fecha actual
     *
     * @param string $format El formato deseado (por defecto 'Y-m-d')
     * @return string La fecha actual formateada
     */
    public function getCurrentDate(string $format = 'Y-m-d'): string
    {
        $dateTime = new DateTime("now", new DateTimeZone($this->timezone));
        return $dateTime->format($format);
    }

    /**
     * Obtiene solo la hora actual
     *
     * @param string $format El formato deseado (por defecto 'H:i:s')
     * @return string La hora actual formateada
     */
    public function getCurrentTime(string $format = 'H:i:s'): string
    {
        $dateTime = new DateTime("now", new DateTimeZone($this->timezone));
        return $dateTime->format($format);
    }

    /**
     * Añade días a una fecha
     *
     * @param string $date La fecha base (ej: 'YYYY-MM-DD')
     * @param int $days Número de días a añadir
     * @param string $format El formato deseado (por defecto 'Y-m-d')
     * @return string La fecha resultante, o cadena vacía si la fecha es inválida
     */
    public function addDays(string $date, int $days, string $format = 'Y-m-d'): string
    {
        try {
            $dateTime = new DateTime($date, new DateTimeZone($this->timezone));
            $dateTime->add(new DateInterval("P{$days}D"));
            return $dateTime->format($format);
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Resta días a una fecha
     *
     * @param string $date La fecha base (ej: 'YYYY-MM-DD')
     * @param int $days Número de días a restar
     * @param string $format El formato deseado (por defecto 'Y-m-d')
     * @return string La fecha resultante, o cadena vacía si la fecha es inválida
     */
    public function subtractDays(string $date, int $days, string $format = 'Y-m-d'): string
    {
        try {
            $dateTime = new DateTime($date, new DateTimeZone($this->timezone));
            $dateTime->sub(new DateInterval("P{$days}D"));
            return $dateTime->format($format);
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Verifica si una fecha es anterior a otra
     *
     * @param string $date1 La primera fecha
     * @param string $date2 La segunda fecha
     * @return bool True si date1 es anterior a date2
     */
    public function isDateBefore(string $date1, string $date2): bool
    {
        try {
            $dateTime1 = new DateTime($date1, new DateTimeZone($this->timezone));
            $dateTime2 = new DateTime($date2, new DateTimeZone($this->timezone));
            return $dateTime1 < $dateTime2;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Verifica si una fecha es posterior a otra
     *
     * @param string $date1 La primera fecha
     * @param string $date2 La segunda fecha
     * @return bool True si date1 es posterior a date2
     */
    public function isDateAfter(string $date1, string $date2): bool
    {
        try {
            $dateTime1 = new DateTime($date1, new DateTimeZone($this->timezone));
            $dateTime2 = new DateTime($date2, new DateTimeZone($this->timezone));
            return $dateTime1 > $dateTime2;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Verifica si una fecha está entre dos fechas (inclusive)
     *
     * @param string $date La fecha a verificar
     * @param string $startDate La fecha de inicio
     * @param string $endDate La fecha de fin
     * @return bool True si la fecha está en el rango
     */
    public function isDateBetween(string $date, string $startDate, string $endDate): bool
    {
        try {
            $dateTime = new DateTime($date, new DateTimeZone($this->timezone));
            $startDateTime = new DateTime($startDate, new DateTimeZone($this->timezone));
            $endDateTime = new DateTime($endDate, new DateTimeZone($this->timezone));

            return $dateTime >= $startDateTime && $dateTime <= $endDateTime;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Obtiene el nombre del día de la semana
     *
     * @param string $date La fecha
     * @param string $locale El idioma (por defecto 'es_ES')
     * @return string El nombre del día de la semana
     */
    public function getDayName(string $date, string $locale = 'es_ES'): string
    {
        try {
            $dateTime = new DateTime($date, new DateTimeZone($this->timezone));
            $dayNumber = $dateTime->format('w'); // 0 (domingo) a 6 (sábado)

            $days = [
                'es_ES' => ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                'en_US' => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
            ];

            return $days[$locale][$dayNumber] ?? $days['es_ES'][$dayNumber];
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Obtiene el nombre del mes
     *
     * @param string $date La fecha
     * @param string $locale El idioma (por defecto 'es_ES')
     * @return string El nombre del mes
     */
    public function getMonthName(string $date, string $locale = 'es_ES'): string
    {
        try {
            $dateTime = new DateTime($date, new DateTimeZone($this->timezone));
            $monthNumber = $dateTime->format('n') - 1; // 0-11

            $months = [
                'es_ES' => ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                'en_US' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
            ];

            return $months[$locale][$monthNumber] ?? $months['es_ES'][$monthNumber];
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Convierte una fecha a timestamp
     *
     * @param string $date La fecha a convertir
     * @return int El timestamp, o 0 si la fecha es inválida
     */
    public function dateToTimestamp(string $date): int
    {
        try {
            $dateTime = new DateTime($date, new DateTimeZone($this->timezone));
            return $dateTime->getTimestamp();
        } catch (Exception $e) {
            return 0;
        }
    }





    /**
     * Convierte un timestamp a fecha
     *
     * @param int $timestamp El timestamp a convertir
     * @param string $format El formato deseado (por defecto 'Y-m-d H:i:s')
     * @return string La fecha formateada
     */
    public function timestampToDate(int $timestamp, string $format = 'Y-m-d H:i:s'): string
    {
        try {
            $dateTime = new DateTime();
            $dateTime->setTimestamp($timestamp);
            $dateTime->setTimezone(new DateTimeZone($this->timezone));
            return $dateTime->format($format);
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Obtiene la zona horaria configurada
     *
     * @return string La zona horaria
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /**
     * Establece una nueva zona horaria
     *
     * @param string $timezone La nueva zona horaria
     */
    public function setTimezone(string $timezone): void
    {
        $this->timezone = $timezone;
    }
}

} // Fin de la verificación class_exists
