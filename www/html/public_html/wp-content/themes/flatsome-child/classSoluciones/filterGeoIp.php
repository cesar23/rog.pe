<?php
require "json_db.php";

/**
 * Class FilterGeoIp
 * Maneja las consultas de geolocalización IP.
 */
class FilterGeoIp
{
    private $path_json = '';

    /**
     * Constructor de la clase.
     */
    public function __construct() { }

    /**
     * Verifica si una cadena es un JSON válido.
     *
     * @param string $string Cadena a verificar.
     * @return bool True si es JSON válido, false en caso contrario.
     */
    public function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Realiza peticiones cURL.
     *
     * @param string $url URL a la que se hace la petición.
     * @param string $method Método HTTP (GET, POST, DELETE, PUT).
     * @param array $headers Encabezados de la petición.
     * @param mixed $postdata Datos a enviar en caso de petición POST/PUT.
     * @param int $timeout Tiempo de espera en segundos.
     * @return string Respuesta en formato JSON.
     */
    public function curl($url, $method = 'GET', $headers = [], $postdata = null, $timeout = 60)
    {
        $result = [
            "status" => true,
            "statusCode" => null,
            "error" => "",
            "message" => "",
            "data" => ""
        ];

        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, $url);
        if ($headers) curl_setopt($s, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($s, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($s, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($s, CURLOPT_MAXREDIRS, 3);
        curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($s, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($s, CURLOPT_HEADER, 0);
        curl_setopt($s, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)');
        curl_setopt($s, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($s, CURLOPT_SSL_VERIFYPEER, false);

        if (strtoupper($method) == 'POST') {
            curl_setopt($s, CURLOPT_POST, true);
            curl_setopt($s, CURLOPT_POSTFIELDS, $postdata);
        } else if (strtoupper($method) == 'DELETE') {
            curl_setopt($s, CURLOPT_CUSTOMREQUEST, 'DELETE');
        } else if (strtoupper($method) == 'PUT') {
            curl_setopt($s, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($s, CURLOPT_POSTFIELDS, $postdata);
        }

        $response = curl_exec($s);
        if (!$response) {
            $error = curl_error($s);
            $result["status"] = false;
            $result["statusCode"] = 500;
            $result["error"] = "CURL Error: = " . $error;
            $result["message"] = "CURL Error: = " . $error;
            $result["data"] = "CURL Error: = " . $error;
        } else {
            $http_status = curl_getinfo($s, CURLINFO_HTTP_CODE);
            $result["statusCode"] = $http_status;
            $body = $response;

            if ($this->isJson($body)) {
                $result["data"] = json_decode($body, true);
            } else {
                $result["data"] = $body;
            }
        }

        curl_close($s);
        return json_encode($result);
    }

    /**
     * Envía solicitudes a las APIs de geolocalización.
     *
     * @param object $cuenta Objeto con información de la cuenta (URL y clave API).
     * @param string $ip Dirección IP a consultar.
     * @return array Datos de la geolocalización.
     */
    function sendRequest($cuenta, $ip)
    {
        $data = [
            "error" => true,
            "message" => "No se encontró URL",
            "country_code" => "",
            "country_name" => "",
            "code" => 0
        ];

        try {
            switch ($cuenta->url) {
                case "http://api.ipstack.com":
                    $response = file_get_contents("http://api.ipstack.com/{$ip}?access_key={$cuenta->key}");
                    if (!$this->isJson($response)) {
                        throw new Exception("Respuesta de API no es un JSON válido.");
                    }
                    $response_data = json_decode($response);

                    if ($response_data->country_code) {
                        $data = [
                            "error" => false,
                            "code" => 0,
                            "country_code" => $response_data->country_code,
                            "country_name" => $response_data->country_name,
                            "message" => "request desde {$cuenta->url}"
                        ];
                    } else {
                        $this->handleApiError($data, $response_data, $cuenta, $ip);
                    }
                    break;

                case "https://api.ipdata.co":
                    $url = $cuenta->url . "/{$ip}?api-key={$cuenta->key}";
                    $headers = [
                        'User-Agent: PostmanRuntime/7.28.4',
                        'Accept: */*',
                        'Content-Type:application/json',
                        'Cache-Control: no-cache'
                    ];

                    $response = $this->curl($url, 'GET', $headers, null, 70);
                    if (!$this->isJson($response)) {
                        throw new Exception("Respuesta de API no es un JSON válido. URL_API:{$url}");
                    }
                    $response_json = json_decode($response, true);

                    if ($response_json['statusCode'] === 200) {

                        $resData = $response_json['data'];
                        if ($resData['country_code']) {
                            $data = [
                                "error" => false,
                                "code" => 0,
                                "country_code" => $resData['country_code'],
                                "country_name" => $resData['country_name'],
                                "message" => "{$cuenta->url}"
                            ];
                        } else {
                            $msg = $resData['reason'] ?? ($resData['continent_name'] ?? "No se obtuvo data");
                            $data = [
                                "error" => true,
                                "code" => 0,
                                "message" => "API:{$url} {$msg}, para la key:[{$cuenta->key}], provider: {$cuenta->provider}"
                            ];
                        }
                    } elseif ($response_json['statusCode'] === 403) {
                        $data = [
                            "error" => true,
                            "code" => 104,
                            "message" => "Quota exceeded 403, para la key:[{$cuenta->key}], provider: {$cuenta->provider}"
                        ];
                    } else {
                        $data = [
                            "error" => true,
                            "code" => 0,
                            "message" => "Error: {$response_json['data']} , provider: {$cuenta->provider}"
                        ];
                    }
                    break;

                default:
                    throw new Exception("URL: [{$cuenta->url}], IP: [{$ip}].  de la API no reconocida.");
            }
        } catch (Exception $e) {
            $data = [
                "error" => true,
                "message" => "Exception: " . $e->getMessage(),
                "code" => $e->getCode()
            ];
        }

        return $data;
    }

    /**
     * Maneja errores en la API de geolocalización.
     *
     * @param array $data Referencia al array de datos de respuesta.
     * @param object $response_data Datos de respuesta de la API.
     * @param object $cuenta Información de la cuenta (URL y clave API).
     * @param string $ip Dirección IP consultada.
     */
    private function handleApiError(&$data, $response_data, $cuenta, $ip)
    {
        if ($response_data->error->code === 104) {
            $data = [
                "error" => true,
                "code" => 104,
                "message" => "Error límite excedido para la key:[{$cuenta->key}], provider: {$cuenta->provider}"
            ];
        } elseif ($response_data->error->code === 106) {
            $data = [
                "error" => true,
                "code" => 106,
                "message" => "IP inválida:[{$ip}], api.ipstack.com"
            ];
        } elseif ($response_data->error->code === 101) {
            $data = [
                "error" => true,
                "code" => 101,
                "message" => "No se ha proporcionado una clave de acceso API, provider: {$cuenta->provider}"
            ];
        } else {
            $data = [
                "error" => true,
                "code" => $response_data->error.code,
                "message" => "Otros errores, api.ipstack.com"
            ];
        }
    }

    /**
     * Obtiene la clave API activa.
     *
     * @return array Clave API activa.
     * @throws Exception Si no hay claves API activas.
     */
    private function getKeyApi()
    {
        $CUR_DIR = dirname(__FILE__);
        $path_file = "$CUR_DIR/data_geo_keys.json";
        $obj = new JsonDb(str_replace("\\", "/", $path_file));
        $keys = $obj->getTableFilter('access_keys', 'active', true);

        $current_date = date("Y-m-d");
        $keys = array_filter($keys, function ($key) use ($current_date) {
            return strtotime($current_date) > strtotime($key['active_date_desde']);
        });

        if (count($keys) <= 0) {
            throw new Exception('No hay API keys activas para validar IPs');
        }

        shuffle($keys);
        return $keys[0];
    }

    /**
     * Valida IP y país, incluyendo verificación de IP local.
     *
     * @param string $ip Dirección IP a validar.
     * @param array $country_allows Lista de países permitidos.
     * @param array $ips_allows Lista de IPs permitidas.
     * @return object Resultado de la validación.
     */
    public function validIpCountry($ip, $country_allows = [], $ips_allows = [])
    {
        $_resOutput = (object)[
            "success" => false,
            "error_code" => null,
            "messages" => [],
            "debug" => [],
            "country_code" => "",
            "country_name" => ""
        ];

        try {
            $_cuenta = (object)$this->getKeyApi();

            if ($this->isLocalIp($ip)) {
                $_resOutput->success = true;
                $_resOutput->country_code = "LOCAL";
                $_resOutput->country_name = "Local IP";
                $_resOutput->messages[] = "La IP: [{$ip}] es una IP local.";
                $_resOutput->debug[] = "La IP: [{$ip}] es una IP local.";
                return $_resOutput;
            }

            if (in_array($ip, $ips_allows)) {
                $_resOutput->success = true;
                $_resOutput->messages[] = "La IP: [{$ip}] se encuentra en la lista blanca";
                $_resOutput->debug[] = "La IP: [{$ip}] se encuentra en la lista blanca.";
                return $_resOutput;
            }

            $_res_data = $this->sendRequest($_cuenta, $ip);

            if ($_res_data['error'] === true) {
                if ($_res_data['code'] === 104) {
                    $_resOutput->error_code = $_res_data['code'];
                    $_resOutput->messages[] = $_res_data['message'];
                    $_resOutput->debug[] = "{$_res_data['message']} - Superó el límite de request. 104";

                    $CUR_DIR = dirname(__FILE__);
                    $path_file = "$CUR_DIR/data_geo_keys.json";
                    $obj = new JsonDb($path_file);
                    $date_new_active = date('Y-m-d', strtotime('+30 days'));
                    $obj->rowUpdate('access_keys', 'active_date_desde', $date_new_active, 'key', $_cuenta->key);
                } else {
                    $_resOutput->error_code = $_res_data['code'];
                    $_resOutput->messages[] = $_res_data['message'];
                    $_resOutput->debug[] = "{$_res_data['message']} - Sucedió otro error: {$_res_data['code']}";
                }
            } else {
                $_resOutput->success = true;
                $_resOutput->country_code = $_res_data['country_code'];
                $_resOutput->country_name = $_res_data['country_name'];
                $_resOutput->messages[] = $_res_data['message'];
                $_resOutput->debug[] = "{$_res_data['message']}";
            }

            if (in_array($_resOutput->country_code, $country_allows)) {
                $_resOutput->success = true;
            } else {
                $_resOutput->success = false;
                $_resOutput->messages[] = "El país [{$_resOutput->country_name}] no está permitido, {$_res_data['message']}";
                $_resOutput->debug[] = "El país [{$_resOutput->country_name}] no está permitido, {$_res_data['message']}";
            }
        } catch (Exception $e) {
            $_resOutput->success = false;
            $_resOutput->messages[] = $e->getMessage();
        }

        return $_resOutput;
    }

    /**
     * Verifica si una dirección IP es local.
     *
     * @param string $ip Dirección IP a verificar.
     * @return bool True si es IP local, false en caso contrario.
     */
    private function isLocalIp($ip)
    {
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return true;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $long_ip = ip2long($ip);
            if (($long_ip & 0xFF000000) === 0x0A000000 || // 10.0.0.0/8
                ($long_ip & 0xFFF00000) === 0xAC100000 || // 172.16.0.0/12
                ($long_ip & 0xFFFF0000) === 0xC0A80000) { // 192.168.0.0/16
                return true;
            }
        }

        return false;
    }

    /**
     * Valida IP y país con todas las claves API disponibles.
     *
     * @param string $ip Dirección IP a validar.
     * @param array $allows Lista de países permitidos.
     * @return array|null Resultado de la validación o null en caso de error.
     */
    public function validIpCountryAll($ip, $allows = [])
    {
        $key = $this->getKeyApi();

        try {
            $country_allows = $allows;
            $response = file_get_contents("http://api.ipstack.com/{$ip}?access_key={$key}");
            return json_decode($response, true);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Destructor de la clase.
     */
    public function __destruct() { }
}

?>
