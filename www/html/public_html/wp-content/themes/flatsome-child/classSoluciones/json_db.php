<?php

/**
 * Class JsonDb
 * Maneja una base de datos en formato JSON.
 */
class JsonDb
{
    private $json_estructure = [
        "access_keys" => [
            "cuenta",
            "url",
            "key",
            "active",
            "active_date_desde",
        ]
    ];
    private $key_uniq = [
        "access_keys" => "key",
    ];
    private $path_json_db;

    /**
     * Constructor de la clase JsonDb.
     *
     * @param string $path_json_db Ruta al archivo JSON.
     * @throws Exception Si no se puede crear el archivo.
     */
    public function __construct($path_json_db)
    {
        $this->createArchive($path_json_db);
        $this->path_json_db = $path_json_db;
    }

    /**
     * Crea el archivo JSON si no existe.
     *
     * @param string $path_file Ruta al archivo JSON.
     * @throws Exception Si no se puede crear el archivo.
     */
    private function createArchive($path_file)
    {
        if (!file_exists($path_file)) {
            $file_create = fopen($path_file, "w+");
            if ($file_create === false) {
                throw new Exception("No se ha podido crear el archivo en el directorio $path_file");
            }
            fwrite($file_create, "[]");
            fclose($file_create);
            chmod($path_file, 0644);
        }
    }

    /**
     * Obtiene una tabla específica del archivo JSON.
     *
     * @param string $table Nombre de la tabla.
     * @return array Fila de la tabla.
     * @throws Exception Si no se puede leer el archivo.
     */
    public function getTable($table)
    {
        try {
            $items = file_get_contents($this->path_json_db);
            $items = json_decode($items, true);

            return $items[$table] ?? [];
        } catch (Exception $err) {
            throw $err;
        }
    }

    /**
     * Obtiene filas filtradas de una tabla.
     *
     * @param string $table Nombre de la tabla.
     * @param string $campo Nombre del campo a filtrar.
     * @param mixed $value Valor del campo a filtrar.
     * @return array Filas filtradas.
     * @throws Exception Si no se puede leer el archivo.
     */
    public function getTableFilter($table, $campo, $value)
    {
        try {
            $rows = $this->getTable($table);
            return array_filter($rows, fn($row) => $row[$campo] === $value);
        } catch (Exception $err) {
            throw $err;
        }
    }

    /**
     * Obtiene una fila específica de un array de filas.
     *
     * @param array $rows Array de filas.
     * @return array|null Fila encontrada o null si no se encuentra.
     */
    public function getRow($rows)
    {
        return $rows[0] ?? null;
    }

    /**
     * Valida si un array contiene una estructura específica.
     *
     * @param array $keys_search Claves a buscar.
     * @param array $data_array Array de datos a validar.
     * @return bool True si el array es válido, false en caso contrario.
     */
    private function validKeyArray($keys_search, $data_array)
    {
        return empty(array_diff_key(array_flip($keys_search), $data_array));
    }

    /**
     * Añade una fila a una tabla.
     *
     * @param string $table Nombre de la tabla.
     * @param array $row Fila a añadir.
     * @return bool True si se añade correctamente.
     * @throws Exception Si la estructura de la fila es inválida o si la clave es duplicada.
     */
    public function addRow($table, $row)
    {
        try {
            if (!$this->validKeyArray($this->json_estructure[$table], $row)) {
                throw new Exception('El valor del array no contiene la estructura');
            }

            $_DB = json_decode(file_get_contents($this->path_json_db), true);
            if (!isset($_DB[$table])) {
                throw new Exception('No se encontró la tabla.');
            }

            if (!empty($this->getTableFilter($table, $this->key_uniq[$table], $row[$this->key_uniq[$table]]))) {
                throw new Exception("La columna [{$this->key_uniq[$table]}] con el valor:[{$row[$this->key_uniq[$table]]}] es duplicado.");
            }

            $_DB[$table][] = $row;
            file_put_contents($this->path_json_db, json_encode($_DB));

            return true;
        } catch (Exception $err) {
            throw $err;
        }
    }

    /**
     * Elimina una fila de una tabla.
     *
     * @param string $table Nombre de la tabla.
     * @param string $where_campo Nombre del campo de condición.
     * @param mixed $where_value Valor del campo de condición.
     * @return bool True si se elimina correctamente.
     * @throws Exception Si no se encuentra la tabla.
     */
    public function rowDelete($table, $where_campo, $where_value)
    {
        try {
            $_DB = json_decode(file_get_contents($this->path_json_db), true);
            if (!isset($_DB[$table])) {
                throw new Exception('No se encontró la tabla.');
            }

            $_DB[$table] = array_filter($_DB[$table], fn($row) => $row[$where_campo] !== $where_value);
            file_put_contents($this->path_json_db, json_encode($_DB));

            return true;
        } catch (Exception $err) {
            throw $err;
        }
    }

    /**
     * Actualiza una fila de una tabla.
     *
     * @param string $table Nombre de la tabla.
     * @param string $update_campo Nombre del campo a actualizar.
     * @param mixed $update_value Valor del campo a actualizar.
     * @param string $where_campo Nombre del campo de condición.
     * @param mixed $where_value Valor del campo de condición.
     * @return bool True si se actualiza correctamente.
     * @throws Exception Si no se encuentra la tabla.
     */
    public function rowUpdate($table, $update_campo, $update_value, $where_campo, $where_value)
    {
        try {
            $_DB = json_decode(file_get_contents($this->path_json_db), true);
            if (!isset($_DB[$table])) {
                throw new Exception('No se encontró la tabla.');
            }

            foreach ($_DB[$table] as &$row) {
                if ($row[$where_campo] === $where_value) {
                    $row[$update_campo] = $update_value;
                }
            }

            file_put_contents($this->path_json_db, json_encode($_DB));
            return true;
        } catch (Exception $err) {
            throw $err;
        }
    }

    /**
     * Obtiene la hora actual en formato 'Y-m-d H:i:s'.
     *
     * @return string Hora actual.
     */
    private function currentTime()
    {
        return date("Y-m-d H:i:s");
    }
}
?>
