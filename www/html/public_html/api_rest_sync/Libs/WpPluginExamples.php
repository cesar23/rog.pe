<?php

/**
 * EJEMPLOS DE USO DE LA CLASE WpPlugin
 *
 * Ejemplos de cómo usar funciones del plugin Solu Currencies Exchange
 * desde tu API REST.
 *
 * NO EJECUTAR ESTE ARCHIVO DIRECTAMENTE - Solo para referencia
 */

namespace Libs;

use Libs\WpPlugin;
use Exception;

class WpPluginExamples
{
    private $wpPlugin;

    public function __construct()
    {
        try {
            // Inicializar WpPlugin (carga WordPress automáticamente)
            $this->wpPlugin = new WpPlugin();

            // Cargar el plugin Solu Currencies Exchange
            $this->wpPlugin->loadSoluCurrenciesExchange();

        } catch (Exception $e) {
            die("Error al cargar WordPress/Plugin: " . $e->getMessage());
        }
    }

    // ==========================================
    // EJEMPLO 1: GUARDAR MONEDAS EN JSON
    // ==========================================

    /**
     * Ejemplo: Usar saveStorageTable() para guardar monedas
     *
     * Esta es la función que querías usar desde tu API
     */
    public function ejemplo1_saveStorageTable()
    {
        try {
            // Llamar a saveStorageTable() del plugin
            $monedas = $this->wpPlugin->saveCurrenciesStorage();

            echo "Archivo JSON actualizado correctamente\n";
            echo "Total de monedas guardadas: " . count($monedas) . "\n";

            // Mostrar monedas
            foreach ($monedas as $moneda) {
                if (is_object($moneda)) {
                    echo "- {$moneda->currency_name} ({$moneda->currency_code}): {$moneda->currency_symbol}{$moneda->currency_value}\n";
                }
            }

            return [
                'status' => 'ok',
                'message' => 'Archivo JSON actualizado',
                'total' => count($monedas),
                'monedas' => $monedas
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    // ==========================================
    // EJEMPLO 2: OBTENER TODAS LAS MONEDAS
    // ==========================================

    /**
     * Ejemplo: Obtener todas las monedas desde el JSON
     */
    public function ejemplo2_getAllCurrencies()
    {
        try {
            $monedas = $this->wpPlugin->getCurrenciesStorage();

            return [
                'status' => 'ok',
                'total' => count($monedas),
                'monedas' => $monedas
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    // ==========================================
    // EJEMPLO 3: OBTENER MONEDA POR CÓDIGO
    // ==========================================

    /**
     * Ejemplo: Obtener una moneda específica (USD, PEN, EUR)
     */
    public function ejemplo3_getCurrencyByCode()
    {
        try {
            // Obtener dólar
            $usd = $this->wpPlugin->getCurrencyByCode('USD');

            if ($usd) {
                echo "Moneda: {$usd->currency_name}\n";
                echo "Código: {$usd->currency_code}\n";
                echo "Símbolo: {$usd->currency_symbol}\n";
                echo "Valor: {$usd->currency_value}\n";
            }

            return [
                'status' => 'ok',
                'moneda' => $usd
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    // ==========================================
    // EJEMPLO 4: OBTENER MONEDA LOCAL
    // ==========================================

    /**
     * Ejemplo: Obtener la moneda local configurada
     */
    public function ejemplo4_getLocalCurrency()
    {
        try {
            $moneda_local = $this->wpPlugin->getLocalCurrency();

            if ($moneda_local) {
                echo "Moneda Local: {$moneda_local->currency_name} ({$moneda_local->currency_code})\n";
            }

            return [
                'status' => 'ok',
                'moneda_local' => $moneda_local
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    // ==========================================
    // EJEMPLO 5: ACTUALIZAR VALOR DE MONEDA
    // ==========================================

    /**
     * Ejemplo: Actualizar el tipo de cambio de una moneda
     */
    public function ejemplo5_updateExchangeRate()
    {
        try {
            // Obtener moneda USD
            $usd = $this->wpPlugin->getCurrency(2); // ID de USD

            if (!$usd) {
                throw new Exception("Moneda no encontrada");
            }

            // Datos a actualizar
            $data = [
                'currency_value' => 3.85, // Nuevo tipo de cambio
                'update_at' => date('Y-m-d H:i:s')
            ];

            // Actualizar en BD
            $this->wpPlugin->updateCurrency($usd->id, $data);

            // Regenerar archivo JSON
            $this->wpPlugin->saveCurrenciesStorage();

            return [
                'status' => 'ok',
                'message' => 'Tipo de cambio actualizado',
                'moneda' => $usd->currency_code,
                'nuevo_valor' => $data['currency_value']
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    // ==========================================
    // EJEMPLO 6: DEFINIR MONEDA ALTERNATIVA
    // ==========================================

    /**
     * Ejemplo: Establecer moneda alternativa (USD, EUR, etc)
     */
    public function ejemplo6_setCurrencyAlternative()
    {
        try {
            // Establecer USD como moneda alternativa
            $currency_alternative = $this->wpPlugin->saveCurrencyAlternative('USD');

            return [
                'status' => 'ok',
                'message' => 'Moneda alternativa configurada',
                'currency_code' => $currency_alternative->currency_alternative_code
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    // ==========================================
    // EJEMPLO 7: OBTENER MONEDA ALTERNATIVA
    // ==========================================

    /**
     * Ejemplo: Obtener la moneda alternativa configurada
     */
    public function ejemplo7_getCurrencyAlternative()
    {
        try {
            $currency_alternative = $this->wpPlugin->getCurrencyAlternative();

            if ($currency_alternative) {
                echo "Moneda alternativa: {$currency_alternative->currency_alternative_code}\n";
            }

            return [
                'status' => 'ok',
                'currency_alternative' => $currency_alternative
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    // ==========================================
    // EJEMPLO 8: VERIFICAR CONSTANTES DEL PLUGIN
    // ==========================================

    /**
     * Ejemplo: Obtener rutas y constantes del plugin
     */
    public function ejemplo8_getPluginConstants()
    {
        try {
            $json_path = $this->wpPlugin->getCurrenciesJsonPath();
            $table_name = $this->wpPlugin->getCurrenciesTableName();

            echo "Archivo JSON: {$json_path}\n";
            echo "Tabla BD: {$table_name}\n";

            return [
                'status' => 'ok',
                'json_path' => $json_path,
                'table_name' => $table_name
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    // ==========================================
    // EJEMPLO 9: SINCRONIZAR TIPO DE CAMBIO DESDE API EXTERNA
    // ==========================================

    /**
     * Ejemplo: Obtener tipo de cambio desde API externa y actualizar
     */
    public function ejemplo9_syncFromExternalAPI()
    {
        try {
            // Simular obtener tipo de cambio desde API externa
            $tipo_cambio_actual = 3.87; // Esto vendría de una API como SUNAT, BCR, etc.

            // Obtener moneda USD desde BD
            $usd = $this->wpPlugin->getCurrency(2);

            if (!$usd) {
                throw new Exception("Moneda USD no encontrada");
            }

            // Actualizar solo si el valor cambió
            if ($usd->currency_value != $tipo_cambio_actual) {
                // Actualizar en BD
                $this->wpPlugin->updateCurrency($usd->id, [
                    'currency_value' => $tipo_cambio_actual,
                    'update_at' => date('Y-m-d H:i:s')
                ]);

                // Regenerar JSON
                $this->wpPlugin->saveCurrenciesStorage();

                return [
                    'status' => 'ok',
                    'message' => 'Tipo de cambio actualizado',
                    'anterior' => $usd->currency_value,
                    'nuevo' => $tipo_cambio_actual,
                    'diferencia' => round($tipo_cambio_actual - $usd->currency_value, 4)
                ];
            } else {
                return [
                    'status' => 'ok',
                    'message' => 'Tipo de cambio sin cambios',
                    'valor_actual' => $tipo_cambio_actual
                ];
            }

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}

// ==========================================
// EJEMPLO 10: USO EN UN CONTROLADOR REAL
// ==========================================

/**
 * Ejemplo de Controller usando WpPlugin para el plugin Solu Currencies Exchange
 */
class CurrencyControllerExample
{
    private $wpPlugin;

    public function __construct()
    {
        try {
            $this->wpPlugin = new WpPlugin();
            $this->wpPlugin->loadSoluCurrenciesExchange();
        } catch (Exception $e) {
            die(json_encode([
                'status' => 'error',
                'message' => 'Error al cargar plugin: ' . $e->getMessage()
            ]));
        }
    }

    /**
     * Endpoint: POST /currency/update-storage
     *
     * Regenera el archivo JSON de monedas
     */
    public function updateCurrenciesStorage()
    {
        try {
            // Llamar a saveStorageTable()
            $monedas = $this->wpPlugin->saveCurrenciesStorage();

            http_response_code(200);
            echo json_encode([
                'status' => 'ok',
                'message' => 'Archivo JSON actualizado correctamente',
                'total_monedas' => count($monedas),
                'archivo' => $this->wpPlugin->getCurrenciesJsonPath(),
                'fecha_actualizacion' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Endpoint: GET /currency/all
     *
     * Obtiene todas las monedas
     */
    public function getAllCurrencies()
    {
        try {
            $monedas = $this->wpPlugin->getCurrenciesStorage();

            http_response_code(200);
            echo json_encode([
                'status' => 'ok',
                'total' => count($monedas),
                'monedas' => $monedas
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Endpoint: GET /currency/by-code?code=USD
     *
     * Obtiene una moneda por código
     */
    public function getCurrencyByCode()
    {
        try {
            $code = $_GET['code'] ?? null;

            if (!$code) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'El parámetro code es requerido'
                ]);
                return;
            }

            $moneda = $this->wpPlugin->getCurrencyByCode($code);

            if (!$moneda) {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => "Moneda {$code} no encontrada"
                ]);
                return;
            }

            http_response_code(200);
            echo json_encode([
                'status' => 'ok',
                'moneda' => [
                    'id' => $moneda->id,
                    'nombre' => $moneda->currency_name,
                    'codigo' => $moneda->currency_code,
                    'simbolo' => $moneda->currency_symbol,
                    'valor' => $moneda->currency_value,
                    'es_local' => $moneda->currency_local === '1'
                ]
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Endpoint: POST /currency/update-exchange-rate
     * Body: {"currency_id": 2, "value": 3.87}
     *
     * Actualiza el tipo de cambio y regenera JSON
     */
    public function updateExchangeRate()
    {
        try {
            $postBody = file_get_contents("php://input");
            $data = json_decode($postBody);

            if (!isset($data->currency_id) || !isset($data->value)) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'currency_id y value son requeridos'
                ]);
                return;
            }

            // Validar que value sea numérico
            if (!is_numeric($data->value) || $data->value <= 0) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'value debe ser un número mayor a 0'
                ]);
                return;
            }

            // Obtener moneda actual
            $moneda = $this->wpPlugin->getCurrency($data->currency_id);

            if (!$moneda) {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Moneda no encontrada'
                ]);
                return;
            }

            // Actualizar en BD
            $this->wpPlugin->updateCurrency($data->currency_id, [
                'currency_value' => $data->value,
                'update_at' => date('Y-m-d H:i:s')
            ]);

            // Regenerar JSON
            $this->wpPlugin->saveCurrenciesStorage();

            http_response_code(200);
            echo json_encode([
                'status' => 'ok',
                'message' => 'Tipo de cambio actualizado correctamente',
                'moneda' => $moneda->currency_code,
                'valor_anterior' => $moneda->currency_value,
                'valor_nuevo' => $data->value,
                'diferencia' => round($data->value - $moneda->currency_value, 4),
                'fecha_actualizacion' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Endpoint: GET /currency/local
     *
     * Obtiene la moneda local configurada
     */
    public function getLocalCurrency()
    {
        try {
            $moneda_local = $this->wpPlugin->getLocalCurrency();

            if (!$moneda_local) {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'No hay moneda local configurada'
                ]);
                return;
            }

            http_response_code(200);
            echo json_encode([
                'status' => 'ok',
                'moneda_local' => [
                    'id' => $moneda_local->id,
                    'nombre' => $moneda_local->currency_name,
                    'codigo' => $moneda_local->currency_code,
                    'simbolo' => $moneda_local->currency_symbol,
                    'valor' => $moneda_local->currency_value
                ]
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Endpoint: POST /currency/set-alternative
     * Body: {"currency_code": "USD"}
     *
     * Establece la moneda alternativa
     */
    public function setAlternativeCurrency()
    {
        try {
            $postBody = file_get_contents("php://input");
            $data = json_decode($postBody);

            if (!isset($data->currency_code)) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'currency_code es requerido'
                ]);
                return;
            }

            // Verificar que la moneda existe
            $moneda = $this->wpPlugin->getCurrencyByCode($data->currency_code);

            if (!$moneda) {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => "Moneda {$data->currency_code} no encontrada"
                ]);
                return;
            }

            // Establecer como alternativa
            $this->wpPlugin->saveCurrencyAlternative($data->currency_code);

            http_response_code(200);
            echo json_encode([
                'status' => 'ok',
                'message' => 'Moneda alternativa configurada',
                'currency_code' => $data->currency_code,
                'currency_name' => $moneda->currency_name
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
