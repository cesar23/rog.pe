#######################################
# 📦 FUNCIÓN: find_env (v1.2 - 2024-05-07)
# ----------------------------------------
# 🔍 DESCRIPCIÓN:
#   Busca el valor de una variable de entorno dentro de un archivo `.env.development`.
#   Retorna solo el valor, limpiando espacios, saltos de línea y retornos de carro.
#
# 🧾 PARÁMETROS:
#   $1 -> Clave (nombre exacto de la variable, ej. MYSQL_HOST)
#   $2 -> Ruta al archivo `.env.development` (con soporte para rutas absolutas o relativas)
#
# 🔁 RETORNO:
#   Imprime por stdout el valor de la clave encontrada, sin salto de línea final.
#
# ✅ USO:
#   valor=$(find_env "MYSQL_DATABASE" ".env.development")
#   echo "Base de datos: $valor"
#
# 🧠 NOTAS:
#   - Ignora líneas comentadas con `#`.
#   - Solo considera la primera aparición de la clave.
#   - Soporta valores con varios signos `=` (p. ej., para contraseñas).
#   - Compatible con entornos bash estándar en Debian/Ubuntu.
#######################################
function find_env() {
    local key="$1"
    local path_file="$2"

    # Validación del archivo .env.development
    if [[ ! -f "$path_file" ]]; then
        echo "❌ Error: Archivo .env no encontrado: $path_file" >&2
        return 1
    fi

    # Buscar el valor de la clave y limpiar saltos de línea y retorno de carro
    local value
    value=$(awk -F '=' -v k="$key" '
        $1 ~ "^"k"$" && $0 !~ /^#/ {
            # Une las partes después del primer "=" (por si hay múltiples "=")
            $1=""; sub(/^ /, "", $0); print $0; exit
        }
    ' "$path_file" | tr -d '\r\n')

    # Retorna sin salto de línea final
    echo -n "$value"
}
