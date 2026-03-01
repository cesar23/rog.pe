<?php

/**
 * Clase que representa una moneda en el sistema de cambio de divisas.
 *
 * Esta clase encapsula toda la información relacionada con una moneda,
 * incluyendo su identificación, nombre, símbolo, código ISO, tasa de cambio,
 * y metadata de auditoría.
 *
 * @package SoluCurrenciesExchange
 * @since 1.0.0
 * @author César Auris <perucaos@gmail.com>
 *
 * @example
 * ```php
 * // Crear una nueva moneda
 * $usd = new SoluCurrenciesExchange(
 *     id: 1,
 *     currency_name: 'Dólar Estadounidense',
 *     currency_symbol: '$',
 *     currency_code: 'USD',
 *     currency_value: 1.00,
 *     currency_local: 1
 * );
 *
 * // Acceder a propiedades
 * echo $usd->currency_symbol; // Imprime: $
 * echo $usd->currency_code;   // Imprime: USD
 * ```
 */
class SoluCurrenciesExchange {

    /**
     * Identificador único de la moneda en la base de datos.
     *
     * @var int|null ID autoincremental de la moneda, null si aún no ha sido guardada.
     */
    public $id;

    /**
     * ID del usuario de WordPress que creó o modificó este registro.
     *
     * @var int|null ID del usuario de WordPress, null si no está disponible.
     */
    public $user_id;

    /**
     * Nombre de usuario de WordPress que creó o modificó este registro.
     *
     * @var string|null Login del usuario de WordPress (user_login), null si no está disponible.
     */
    public $username;

    /**
     * Nombre completo de la moneda.
     *
     * @var string|null Nombre descriptivo de la moneda (ej: 'Dólar Estadounidense', 'Sol Peruano').
     */
    public $currency_name;

    /**
     * Descripción adicional de la moneda.
     *
     * @var string|null Texto descriptivo o notas adicionales sobre la moneda.
     */
    public $currency_description;

    /**
     * Símbolo de la moneda.
     *
     * @var string|null Símbolo visual de la moneda (ej: '$', 'S/', '€').
     */
    public $currency_symbol;

    /**
     * Código ISO 4217 de la moneda.
     *
     * @var string|null Código de tres letras según estándar ISO 4217 (ej: 'USD', 'PEN', 'EUR').
     * @link https://es.wikipedia.org/wiki/ISO_4217
     */
    public $currency_code;

    /**
     * Tasa de cambio o valor de conversión de la moneda.
     *
     * Representa el factor de conversión con respecto a la moneda local.
     * - Si es moneda local: generalmente 1.00
     * - Si es moneda alternativa: factor de multiplicación para convertir
     *
     * @var float|string|null Valor numérico de la tasa de cambio.
     *
     * @example
     * ```php
     * // Si USD es local (1.00) y PEN es alternativa (3.75)
     * // Para convertir $100 USD a PEN:
     * $precio_usd = 100;
     * $precio_pen = $precio_usd * 3.75; // = 375 PEN
     * ```
     */
    public $currency_value;

    /**
     * Indica si esta es la moneda local del sitio.
     *
     * Solo puede haber una moneda marcada como local (currency_local = 1).
     * Esta es la moneda base de WooCommerce.
     *
     * @var int|string|null 1 si es moneda local, 0 si no lo es, null si no está definido.
     */
    public $currency_local;

    /**
     * Orden de visualización de la moneda.
     *
     * Determina el orden en que se muestran las monedas en listados o interfaces.
     * Números menores aparecen primero.
     *
     * @var int Número de orden (default: 0).
     */
    public $currency_order;

    /**
     * Estado de activación de la moneda.
     *
     * Define si la moneda está activa y disponible para uso en el sistema.
     *
     * @var int 1 si está activa, 0 si está inactiva (default: 0).
     */
    public $active;

    /**
     * Fecha y hora de creación del registro.
     *
     * @var string|null Fecha en formato 'Y-m-d H:i:s' (ej: '2025-11-27 10:30:00').
     */
    public $created_at;

    /**
     * Fecha y hora de última actualización del registro.
     *
     * @var string|null Fecha en formato 'Y-m-d H:i:s' (ej: '2025-11-27 15:45:30').
     */
    public $update_at;

    /**
     * Constructor de la clase SoluCurrenciesExchange.
     *
     * Inicializa una nueva instancia de la moneda con todos sus atributos.
     * Todos los parámetros son opcionales para permitir crear objetos vacíos
     * o parcialmente inicializados.
     *
     * @param int|null    $id                   ID único de la moneda en la base de datos.
     * @param int|null    $user_id              ID del usuario de WordPress que creó el registro.
     * @param string|null $username             Nombre de usuario de WordPress.
     * @param string|null $currency_name        Nombre completo de la moneda.
     * @param string|null $currency_description Descripción adicional de la moneda.
     * @param string|null $currency_symbol      Símbolo visual de la moneda (ej: '$', 'S/').
     * @param string|null $currency_code        Código ISO 4217 de 3 letras (ej: 'USD', 'PEN').
     * @param float|null  $currency_value       Tasa de cambio o valor de conversión.
     * @param int|null    $currency_local       1 si es moneda local, 0 si no lo es.
     * @param int         $currency_order       Orden de visualización (default: 0).
     * @param int         $active               1 si está activa, 0 si está inactiva (default: 0).
     * @param string|null $created_at           Fecha de creación en formato 'Y-m-d H:i:s'.
     * @param string|null $update_at            Fecha de última actualización en formato 'Y-m-d H:i:s'.
     *
     * @since 1.0.0
     *
     * @example
     * ```php
     * // Crear moneda completa
     * $pen = new SoluCurrenciesExchange(
     *     id: 2,
     *     user_id: 1,
     *     username: 'admin',
     *     currency_name: 'Sol Peruano',
     *     currency_description: 'Moneda oficial del Perú',
     *     currency_symbol: 'S/',
     *     currency_code: 'PEN',
     *     currency_value: 3.75,
     *     currency_local: 0,
     *     currency_order: 1,
     *     active: 1,
     *     created_at: '2025-11-27 10:00:00',
     *     update_at: '2025-11-27 10:00:00'
     * );
     *
     * // Crear objeto vacío para llenar después
     * $moneda = new SoluCurrenciesExchange();
     * $moneda->currency_code = 'EUR';
     * $moneda->currency_value = 0.92;
     * ```
     */
    public function __construct(
        $id = null,
        $user_id = null,
        $username = null,
        $currency_name = null,
        $currency_description = null,
        $currency_symbol = null,
        $currency_code = null,
        $currency_value = null,
        $currency_local = null,
        $currency_order = 0,
        $active = 0,
        $created_at = null,
        $update_at = null
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->username = $username;
        $this->currency_name = $currency_name;
        $this->currency_description = $currency_description;
        $this->currency_symbol = $currency_symbol;
        $this->currency_code = $currency_code;
        $this->currency_value = $currency_value;
        $this->currency_local = $currency_local;
        $this->currency_order = $currency_order;
        $this->active = $active;
        $this->created_at = $created_at;
        $this->update_at = $update_at;
    }

}