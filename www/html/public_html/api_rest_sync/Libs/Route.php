<?php

namespace Libs;

/**
 * Class Route
 *
 * Esta clase maneja el enrutamiento de las solicitudes HTTP en la aplicación.
 * Permite agregar rutas, definir manejadores para rutas no encontradas y métodos no permitidos,
 * y ejecutar la lógica de enrutamiento.
 */
class Route
{
    /**
     * @var array $routes Arreglo que almacena todas las rutas registradas.
     */
    private static $routes = [];

    /**
     * @var callable|null $pathNotFound Función a llamar si no se encuentra una ruta coincidente.
     */
    private static $pathNotFound = null;

    /**
     * @var callable|null $methodNotAllowed Función a llamar si el método HTTP no está permitido para una ruta coincidente.
     */
    private static $methodNotAllowed = null;

    /**
     * Agrega una nueva ruta al enrutador.
     *
     * @param string $expression Expresión de la ruta o cadena.
     * @param callable $function Función a llamar si se encuentra una coincidencia con la ruta y el método permitido.
     * @param string|array $method Método HTTP permitido o un arreglo con métodos permitidos (por defecto 'get').
     *
     * @return void
     */
    public static function add($expression, $function, $method = 'get')
    {
        self::$routes[] = [
            'expression' => $expression,
            'function' => $function,
            'method' => $method
        ];
    }

    /**
     * Obtiene todas las rutas registradas.
     *
     * @return array Arreglo con todas las rutas registradas.
     */
    public static function getAll()
    {
        return self::$routes;
    }

    /**
     * Define una función para manejar rutas no encontradas.
     *
     * @param callable $function Función a llamar si no se encuentra una ruta coincidente.
     *
     * @return void
     */
    public static function pathNotFound($function)
    {
        self::$pathNotFound = $function;
    }

    /**
     * Define una función para manejar métodos no permitidos.
     *
     * @param callable $function Función a llamar si el método HTTP no está permitido para una ruta coincidente.
     *
     * @return void
     */
    public static function methodNotAllowed($function)
    {
        self::$methodNotAllowed = $function;
    }

    /**
     * Ejecuta el enrutador para la ruta actual.
     *
     * @param string $basepath Ruta base de la aplicación (por defecto '').
     * @param bool $case_matters Si importa mayúsculas y minúsculas en las rutas (por defecto false).
     * @param bool $trailing_slash_matters Si importa la barra final en las rutas (por defecto false).
     * @param bool $multimatch Si debe coincidir con múltiples rutas (por defecto false).
     *
     * @return void
     */
    public static function run($basepath = '', $case_matters = false, $trailing_slash_matters = false, $multimatch = false)
    {
        // Eliminar la barra final de la ruta base si existe
        $basepath = rtrim($basepath, '/');

        // Analizar la URL actual
        $parsed_url = parse_url($_SERVER['REQUEST_URI']);
        $path = $parsed_url['path'] ?? '/';
        $path = $trailing_slash_matters ? $path : rtrim($path, '/');
        $path = urldecode($path);

        // Obtener el método de solicitud HTTP actual
        $method = $_SERVER['REQUEST_METHOD'];

        $path_match_found = false;
        $route_match_found = false;

        foreach (self::$routes as $route) {
            // Agregar basepath a la expresión de la ruta
            if ($basepath != '' && $basepath != '/') {
                $route['expression'] = '(' . $basepath . ')' . $route['expression'];
            }

            // Asegurarse de que la expresión coincida desde el principio hasta el final de la cadena
            $route['expression'] = '^' . $route['expression'] . '$';

            // Verificar si la ruta coincide
            if (preg_match('#' . $route['expression'] . '#' . ($case_matters ? '' : 'i') . 'u', $path, $matches)) {
                $path_match_found = true;

                // Convertir el método permitido en un arreglo si no lo es
                foreach ((array)$route['method'] as $allowedMethod) {
                    // Verificar si el método coincide
                    if (strtolower($method) == strtolower($allowedMethod)) {
                        array_shift($matches); // Eliminar el primer elemento, que contiene la cadena completa

                        if ($basepath != '' && $basepath != '/') {
                            array_shift($matches); // Eliminar el basepath
                        }

                        // Llamar a la función asociada con la ruta
                        if ($return_value = call_user_func_array($route['function'], $matches)) {
                            echo $return_value;
                        }

                        $route_match_found = true;

                        // No verificar otras rutas si se encuentra una coincidencia
                        if (!$multimatch) {
                            break;
                        }
                    }
                }
            }

            // Salir del bucle si se encontró una coincidencia de ruta y no se permiten múltiples coincidencias
            if ($route_match_found && !$multimatch) {
                break;
            }
        }

        // No se encontró ninguna ruta coincidente
        if (!$route_match_found) {
            // Se encontró un camino coincidente pero el método no está permitido
            if ($path_match_found) {
                if (self::$methodNotAllowed) {
                    call_user_func_array(self::$methodNotAllowed, [$path, $method]);
                }
            } else {
                if (self::$pathNotFound) {
                    call_user_func_array(self::$pathNotFound, [$path]);
                }
            }
        }
    }
}
