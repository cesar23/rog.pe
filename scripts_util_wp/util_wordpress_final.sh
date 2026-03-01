#!/bin/bash

# Ubcacion del script por defecto:
# /home/navdyelstore.it/scripts
# /home/navdyelstore.it/scripts/util_wordpress_final.sh

# =============================================================================
# ⚙️ SECTION: Configuración Inicial
# =============================================================================
# Establece la codificación a UTF-8 para evitar problemas con caracteres especiales.


# Fecha y hora actual en formato: YYYY-MM-DD_HH:MM:SS (hora local)
DATE_HOUR=$(date "+%Y-%m-%d_%H:%M:%S")
# Fecha y hora actual en Perú (UTC -5)
DATE_HOUR_PE=$(date -u -d "-5 hours" "+%Y-%m-%d_%H:%M:%S" 2>/dev/null || TZ="America/Lima" date "+%Y-%m-%d_%H:%M:%S" 2>/dev/null || echo "$DATE_HOUR")
CURRENT_USER=$(id -un)             # Nombre del usuario actual.
CURRENT_USER_HOME="${HOME:-$USERPROFILE}"  # Ruta del perfil del usuario actual.
CURRENT_PC_NAME=$(hostname)        # Nombre del equipo actual.
MY_INFO="${CURRENT_USER}@${CURRENT_PC_NAME}"  # Información combinada del usuario y del equipo.
PATH_SCRIPT=$(readlink -f "${BASH_SOURCE:-$0}")  # Ruta completa del script actual.
SCRIPT_NAME=$(basename "$PATH_SCRIPT")           # Nombre del archivo del script.
CURRENT_DIR=$(dirname "$PATH_SCRIPT")            # Ruta del directorio donde se encuentra el script.
NAME_DIR=$(basename "$CURRENT_DIR")              # Nombre del directorio actual.
TEMP_PATH_SCRIPT=$(echo "$PATH_SCRIPT" | sed 's/.sh/.tmp/g')  # Ruta para un archivo temporal basado en el nombre del script.
TEMP_PATH_SCRIPT_SYSTEM=$(echo "${TMP}/${SCRIPT_NAME}" | sed 's/.sh/.tmp/g')  # Ruta para un archivo temporal en /tmp.
ROOT_PATH=$(realpath -m "${CURRENT_DIR}/..")


# =============================================================================
# 🎨 SECTION: Colores para su uso
# =============================================================================
# Definición de colores que se pueden usar en la salida del terminal.

# Colores Regulares
Color_Off='\033[0m'       # Reset de color.
Black='\033[0;30m'        # Negro.
Red='\033[0;31m'          # Rojo.
Green='\033[0;32m'        # Verde.
Yellow='\033[0;33m'       # Amarillo.
Blue='\033[0;34m'         # Azul.
Purple='\033[0;35m'       # Púrpura.
Cyan='\033[0;36m'         # Cian.
White='\033[0;37m'        # Blanco.
Gray='\033[0;90m'         # Gris.

# Colores en Negrita
BBlack='\033[1;30m'       # Negro (negrita).
BRed='\033[1;31m'         # Rojo (negrita).
BGreen='\033[1;32m'       # Verde (negrita).
BYellow='\033[1;33m'      # Amarillo (negrita).
BBlue='\033[1;34m'        # Azul (negrita).
BPurple='\033[1;35m'      # Púrpura (negrita).
BCyan='\033[1;36m'        # Cian (negrita).
BWhite='\033[1;37m'       # Blanco (negrita).
BGray='\033[1;90m'        # Gris (negrita).


# ----------------------------------------------------------------------
# -- check_error
# ----------------------------------------------------------------------
# Descripción:
#   Verifica el código de salida del último comando ejecutado y muestra un
#   mensaje de error personalizado si ocurrió una falla.
#
# Uso:
#   check_error "Mensaje de error personalizado"
# ----------------------------------------------------------------------
# =============================================================================
# 🔗 Función única para mensajes
# =============================================================================
message_process() {
  local exit_code=$?
  local message=$1
  if [ $exit_code -eq 0 ]; then
    echo -e "${BGreen}✅ ${message}${Color_Off}"
  else
    echo -e "${BRed}❌ Error: ${message}${Color_Off}"
    exit $exit_code
  fi
}



# ==============================================================================
# 📝 Función: msg
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Imprime mensajes con colores según el tipo. Formato limpio sin fecha ni etiquetas.
#
# 🔧 Parámetros:
#   $1 - Mensaje a mostrar (texto)
#   $2 - Tipo de mensaje (INFO | WARNING | ERROR | SUCCESS | DEBUG) [opcional, por defecto: INFO]
#
# 💡 Uso:
#   msg "Proceso completado"                  # Por defecto: INFO (azul)
#   msg "Revisar configuración" "WARNING"     # WARNING (amarillo)
#   msg "Conexión fallida" "ERROR"            # ERROR (rojo)
#   msg "Operación exitosa" "SUCCESS"         # SUCCESS (verde)
#   msg "Modo debug activado" "DEBUG"         # DEBUG (púrpura)
#
# 🎨 Requiere:
#   Variables de color: BBlue, BYellow, BRed, BGreen, BPurple, BGray, Color_Off
# ==============================================================================

msg() {
  local message="$1"
  local level="${2:-OTHER}"

  case "$level" in
    INFO)
      echo -e "${BBlue}${message}${Color_Off}"
      ;;
    WARNING)
      echo -e "${BYellow}${message}${Color_Off}"
      ;;
    DEBUG)
      echo -e "${BPurple}${message}${Color_Off}"
      ;;
    ERROR)
      echo -e "${BRed}${message}${Color_Off}"
      ;;
    SUCCESS)
      echo -e "${BGreen}${message}${Color_Off}"
      ;;
    *)
      echo -e "${BGray}${message}${Color_Off}"
      ;;
  esac
}

my_banner(){
  clear
  echo ""
  echo -e "  ${BRed}╔══════════════════════════════════════════════════╗${Color_Off}"
  echo -e "  ${BRed}║${Color_Off}  ${BWhite} ██████╗ █████╗ ${Color_Off}      ${BRed}██████╗ ███████╗██╗   ██╗${Color_Off} ${BRed}║${Color_Off}"
  echo -e "  ${BRed}║${Color_Off}  ${BWhite}██╔════╝██╔══██╗${Color_Off}      ${BRed}██╔══██╗██╔════╝██║   ██║${Color_Off} ${BRed}║${Color_Off}"
  echo -e "  ${BRed}║${Color_Off}  ${BWhite}██║     ███████║${Color_Off}█████╗${BRed}██║  ██║█████╗  ██║   ██║${Color_Off} ${BRed}║${Color_Off}"
  echo -e "  ${BRed}║${Color_Off}  ${BWhite}██║     ██╔══██║${Color_Off}╚════╝${BRed}██║  ██║██╔══╝  ╚██╗ ██╔╝${Color_Off} ${BRed}║${Color_Off}"
  echo -e "  ${BRed}║${Color_Off}  ${BWhite}╚██████╗██║  ██║${Color_Off}      ${BRed}██████╔╝███████╗ ╚████╔╝ ${Color_Off} ${BRed}║${Color_Off}"
  echo -e "  ${BRed}║${Color_Off}  ${BWhite} ╚═════╝╚═╝  ╚═╝${Color_Off}      ${BRed}╚═════╝ ╚══════╝  ╚═══╝  ${Color_Off} ${BRed}║${Color_Off}"
  echo -e "  ${BRed}║${Purple}          Cesar Auris - perucaos@gmail.com        ${BRed}║${Color_Off}"
  echo -e "  ${BRed}╚══════════════════════════════════════════════════╝${Color_Off}"
  echo -e "  ${Gray}┌──────────────────────────────────────────────────┐${Color_Off}"
  echo -e "  ${Gray}│${Color_Off} ${BRed}[${Color_Off}${BWhite}*${Color_Off}${BRed}]${Color_Off} Config: ${BYellow}${SSH_CONFIG}${Color_Off}${Gray}         │${Color_Off}"
  echo -e "  ${Gray}└──────────────────────────────────────────────────┘${Color_Off}"
}


# ------------------------------------------------------------------------------
# pause_continue
#
# Pausa la ejecución del script mostrando un mensaje en consola y espera que el
# usuario presione [ENTER] para continuar.
#
# @param $1: (opcional) Mensaje descriptivo del evento. Si no se indica, se usa
#            "Comando ejecutado" como mensaje por defecto.
# @return: No retorna valor. Pausa hasta que el usuario presione [ENTER].
# @example: pause_continue
#           # Muestra: "✅ Comando ejecutado. Presiona [ENTER] para continuar..."
# @example: pause_continue "Se instaló MySQL"
#           # Muestra: "🔹 Se instaló MySQL. Presiona [ENTER] para continuar..."
# ------------------------------------------------------------------------------
pause_continue() {
  # Determina el mensaje a mostrar según si se recibe argumento
  if [ -n "$1" ]; then
    local mensaje="🔹 $1. Presiona [ENTER] para continuar..."
  else
    local mensaje="✅ Comando ejecutado. Presiona [ENTER] para continuar..."
  fi

  # Muestra el mensaje en gris y espera la entrada del usuario
  echo -en "${Gray}"
  read -p "$mensaje"
  echo -en "${Color_Off}"
}





# ----------------------------------------
# Function: detect_system
# Detects the operating system distribution.
# Returns:
#   - "termux"  -> If running in Termux
#   - "wsl"     -> If running on Windows Subsystem for Linux
#   - "ubuntu"  -> If running on Ubuntu/Debian-based distributions
#   - "redhat"  -> If running on Red Hat, Fedora, CentOS, Rocky, or AlmaLinux
#   - "gitbash" -> If running on Git Bash
#   - "unknown" -> If the system is not recognized
#
# Example usage:
#   system=$(detect_system)
#   echo "Detected system: $system"
# ----------------------------------------
detect_system() {
    if [ -f /data/data/com.termux/files/usr/bin/pkg ]; then
        echo "termux"
    elif grep -q Microsoft /proc/version; then
        echo "wsl"
    elif [ -f /etc/os-release ]; then
        # Lee el ID de /etc/os-release
        source /etc/os-release
        case $ID in
            ubuntu|debian)
                echo "ubuntu"
                ;;
            rhel|centos|fedora|rocky|almalinux)
                echo "redhat"
                ;;
            *)
                echo "unknown"
                ;;
        esac
    elif [ -n "$MSYSTEM" ]; then
        echo "gitbash"
    else
        echo "unknown"
    fi
}

# ----------------------------------------------------------------------
# 🖥️ detect_os
# ----------------------------------------------------------------------
# Descripción:
#   Detecta el sistema operativo actual (windows, linux, macos, unknown).
#
# Retorna: "windows" | "linux" | "macos" | "unknown"
#
# Ejemplos:
#   OS=$(detect_os)
#
#   if [[ "$OS" == "windows" ]]; then
#       ping google.com -n 4
#   else
#       ping -c 4 google.com
#   fi
# ----------------------------------------------------------------------
detect_os() {
    if [[ "$OSTYPE" == "msys" || "$OSTYPE" == "win32" || "$OSTYPE" == "cygwin" ]]; then
        echo "windows"
    elif [[ "$OSTYPE" == "linux-gnu"* ]]; then
        echo "linux"
    elif [[ "$OSTYPE" == "darwin"* ]]; then
        echo "macos"
    else
        echo "unknown"
    fi
}



# =============================================================================
# ✅ Función: check_paths
# =============================================================================
# ✅ Descripción:
#   Verifica si las rutas dadas existen como archivos o directorios.
#   Soporta rutas en formato Unix y rutas estilo Windows, convirtiéndolas automáticamente.
#
# 🔧 Parámetros:
#   @param ...paths: Lista de rutas a verificar (una o más rutas).
#
# 📤 Retorna:
#   0 si todas las rutas existen, 1 si alguna falla.
#
# 🚨 Excepciones:
#   @throws: Ninguna excepción explícita, pero imprime errores en stderr.
#
# 💡 Ejemplos de uso:
#   @example: check_paths "/etc/passwd" "/var/log" → 0 (si existen)
#   @example: check_paths "C:\Users\user\documento.txt" → 0 (si existe)
#   @example: check_paths "/no/existe" → 1 (imprime error)
#   @example: check_paths "/etc/passwd" "/no/existe" "/var/log" → 1 (imprime error)
#
# 🎯 Casos de uso:
#   - Validación de rutas antes de operaciones críticas
#   - Verificación de archivos de configuración
#   - Comprobación de directorios de trabajo
#   - Validación de rutas de backup
#
# 🔧 Dependencias:
#   - Variables de color: BRed, Color_Off
#   - basename: Para extraer nombres de archivos
#   - sed: Para conversión de rutas Windows a Unix
# =============================================================================

check_paths() {
    local error=0
    for ruta in "$@"; do
        # Convertir rutas de Windows a formato Unix si es necesario
        ruta_unix=$(echo "$ruta" | sed 's/\\/\//g' | sed 's/^\([A-Za-z]\):/\/mnt\/\L\1/')

        if [ -d "$ruta_unix" ] || [ -d "$ruta" ]; then
            continue
        elif [ -f "$ruta_unix" ] || [ -f "$ruta" ]; then
            continue
        else
            nombre=$(basename "$ruta")
            if [[ "$nombre" == *.* ]]; then
                echo -en "${BRed}"
                echo  "Error: El archivo '$ruta' no existe" >&2
                echo -en "${Color_Off}"
            else
              echo -en "${BRed}"
                echo "Error: El directorio '$ruta' no existe" >&2
                echo -en "${Color_Off}"
            fi
            error=1
        fi
    done
    return $error
}

# ----------------------------------------------------------------------
# 📋 view_vars_config
# ----------------------------------------------------------------------
# Descripción:
#   Muestra todas las variables de configuración actuales en formato legible.
#
# Uso:
#   view_vars_config
# =============================================================================
# 📋 Función: view_vars_config
# =============================================================================
# ✅ Descripción:
#   Muestra todas las variables de configuración actuales en formato tabular y legible.
#   Incluye información del sistema, rutas, configuración de base de datos y WordPress.
#
# 🔧 Parámetros:
#   Ninguno - La función no requiere parámetros de entrada.
#
# 📤 Retorna:
#   No retorna valor específico, imprime la configuración en stdout.
#
# 🚨 Excepciones:
#   @throws: Ninguna excepción explícita.
#
# 💡 Ejemplos de uso:
#   @example: view_vars_config → Muestra toda la configuración actual
#   @example: view_vars_config → Formato tabular con colores
#
# 🎯 Casos de uso:
#   - Debugging de configuración
#   - Verificación de variables de entorno
#   - Diagnóstico de problemas
#   - Documentación de configuración
#
# 🔧 Dependencias:
#   - Variables de color: Color_Off, BYellow, BBlue
#   - Variables de configuración: DATE_HOUR, CURRENT_USER, PATH_DOMAIN, etc.
# =============================================================================
view_vars_config() {


  echo -e "${Color_Off}"
  echo -e "╔════════════════════════════════════════════════"
  echo -e "║         ${BYellow}🛠️  CONFIGURACIÓN ACTUAL 🛠️${Color_Off}"
  echo -e "║"
  echo -e "║ ${BBlue}📅 DATE_HOUR:${Color_Off}"
  echo -e "║    ${DATE_HOUR}"
  echo -e "║ ${BBlue}👤 CURRENT_USER:${Color_Off}"
  echo -e "║    ${CURRENT_USER}"
  echo -e "║ ${BBlue}🖥️ CURRENT_PC_NAME:${Color_Off}"
  echo -e "║    ${CURRENT_PC_NAME}"
  echo -e "║ ${BBlue}ℹ️ MY_INFO:${Color_Off}"
  echo -e "║    ${MY_INFO}"
  echo -e "║ ${BBlue}📁 CURRENT_USER_HOME:${Color_Off}"
  echo -e "║    ${CURRENT_USER_HOME}"
  echo -e "║ ${BBlue}📄 PATH_SCRIPT:${Color_Off}"
  echo -e "║    ${PATH_SCRIPT}"
  echo -e "║ ${BBlue}📜 SCRIPT_NAME:${Color_Off}"
  echo -e "║    ${SCRIPT_NAME}"
  echo -e "║ ${BBlue}📁 CURRENT_DIR:${Color_Off}"
  echo -e "║    ${CURRENT_DIR}"
  echo -e "║ ${BBlue}🗂️ NAME_DIR:${Color_Off}"
  echo -e "║    ${NAME_DIR}"
  echo -e "║ ${BBlue}🗃️ TEMP_PATH_SCRIPT:${Color_Off}"
  echo -e "║    ${TEMP_PATH_SCRIPT}"
  echo -e "║ ${BBlue}📂 TEMP_PATH_SCRIPT_SYSTEM:${Color_Off}"
  echo -e "║    ${TEMP_PATH_SCRIPT_SYSTEM}"
  echo -e "║═══════════════════════════════════════════════════════════"
  echo -e "║ 📂 DOWNLOAD_URL_WORDPRESS:  ${DOWNLOAD_URL_WORDPRESS}"
  echo -e "║ 📂 DOMAIN:  ${SITE}"
  echo -e "║ 📂 PATH_DOMAIN:  ${PATH_DOMAIN}"
  echo -e "║ 📂 PHP_BIN:  ${PHP_BIN}"
  echo -e "║ 📂 DB_HOST:  ${DB_HOST}"
  echo -e "║ 📂 DB_PORT:  ${DB_PORT}"
  echo -e "║ 📂 DB_USER:  ${DB_USER}"
  echo -e "║ 📂 DB_PASSWORD:  ${DB_PASSWORD}"
  echo -e "║ 📂 DB_NAME:  ${DB_NAME}"
  if [ -n "$ROOT_PATH" ]; then
    echo -e "║ ${BBlue}🏡 ROOT_PATH:${Color_Off}"
    echo -e "║    ${ROOT_PATH}"
  fi

  echo -e "╚════════════════════════════════════════════════"
  echo -e "${Color_Off}"
}

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



# =============================================================================
# 🔧 SECTION: Functions Utils
# =============================================================================

# =============================================================================
# 🔒 Función: check_verify_ssl_mode
# =============================================================================
# ✅ Descripción:
#   Verifica si el cliente MySQL soporta la opción "--ssl-mode=DISABLED".
#   Esta opción fue introducida en MySQL 5.7.11+ y no está disponible en MariaDB.
#
# 🔧 Parámetros:
#   Ninguno - La función no requiere parámetros de entrada.
#
# 📤 Retorna:
#   1 si el cliente soporta --ssl-mode=DISABLED, 0 si no lo soporta.
#
# 🚨 Excepciones:
#   @throws: Ninguna excepción explícita.
#
# 💡 Ejemplos de uso:
#   @example: check_verify_ssl_mode → 1 (MySQL 5.7.11+)
#   @example: check_verify_ssl_mode → 0 (MariaDB o MySQL antiguo)
#
# 🎯 Casos de uso:
#   - Configuración automática de opciones SSL para MySQL
#   - Adaptación de comandos según la versión de MySQL
#   - Configuración de conexiones de base de datos
#   - Validación de compatibilidad de cliente MySQL
#
# 🔧 Dependencias:
#   - Variable PATH_MYSQL: Ruta al cliente MySQL
#   - grep: Para buscar patrones en la salida de ayuda
# =============================================================================
check_verify_ssl_mode() {
    # Verificar si mysql está disponible
    if ! command -v "${PATH_MYSQL}" >/dev/null 2>&1; then
        echo 0
        return
    fi

    # Extraer todas las opciones que reconoce mysql
    "${PATH_MYSQL}" --help --verbose 2>/dev/null | grep -Eo '^ *--[a-z0-9\-]+' | grep -q '^ *--ssl-mode$'
    if [[ $? -eq 0 ]]; then
        echo 1
    else
        echo 0
    fi
}

# =============================================================================
# 📊 Función: fn_get_mysql_version
# =============================================================================
# ✅ Descripción:
#   Muestra la versión del cliente MySQL/MariaDB instalado en el sistema.
#   Utiliza colores para resaltar la información de versión.
#
# 🔧 Parámetros:
#   Ninguno - La función no requiere parámetros de entrada.
#
# 📤 Retorna:
#   No retorna valor específico, imprime la versión en stdout.
#
# 🚨 Excepciones:
#   @throws: Ninguna excepción explícita.
#
# 💡 Ejemplos de uso:
#   @example: fn_get_mysql_version → "mysql  Ver 8.0.30 for Win64 on x86_64"
#   @example: fn_get_mysql_version → "mysql  Ver 5.7.42-0ubuntu0.18.04.1"
#
# 🎯 Casos de uso:
#   - Verificación de versión de MySQL
#   - Diagnóstico de problemas de compatibilidad
#   - Configuración de scripts según versión
#   - Debugging de conexiones de base de datos
#
# 🔧 Dependencias:
#   - Variable PATH_MYSQL: Ruta al cliente MySQL
#   - Variables de color: Gray, Color_Off
# =============================================================================
function fn_get_mysql_version() {
  echo -en " ${Gray}"
  "${PATH_MYSQL}" --version
  echo -en " ${Color_Off}"
}

# =============================================================================
# 🔧 Función: check_install_wp_cli
# =============================================================================
# ✅ Descripción:
#   Verifica si WP-CLI está instalado y lo descarga automáticamente si no existe.
#   Utiliza curl para descargar la versión más reciente de WP-CLI.
#
# 🔧 Parámetros:
#   Ninguno - La función no requiere parámetros de entrada.
#
# 📤 Retorna:
#   No retorna valor específico.
#
# 🚨 Excepciones:
#   @throws: Ninguna excepción explícita.
#
# 💡 Ejemplos de uso:
#   @example: check_install_wp_cli → Descarga WP-CLI si no existe
#   @example: check_install_wp_cli → No hace nada si ya está instalado
#
# 🎯 Casos de uso:
#   - Instalación automática de WP-CLI
#   - Verificación de dependencias
#   - Setup inicial de WordPress
#   - Configuración de herramientas de desarrollo
#
# 🔧 Dependencias:
#   - curl: Para descargar WP-CLI
#   - Función: msg
#   - Variable pwd: Directorio actual
# =============================================================================
check_install_wp_cli() {
  # Verificamos si el archivo ya existe
  if [ ! -f "wp-cli.phar" ]; then
    msg " Descargando WP-CLI en ${PWD}..."
    curl -sSLO https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
  fi
}

# =============================================================================
# 📥 Función: fn_download_wp_cli
# =============================================================================
# ✅ Descripción:
#   Descarga e instala WP-CLI en el directorio de WordPress y muestra la versión.
#   Cambia al directorio de WordPress antes de realizar la instalación.
#
# 🔧 Parámetros:
#   @param PARAM_1: Parámetro opcional (no utilizado actualmente).
#
# 📤 Retorna:
#   No retorna valor específico.
#
# 🚨 Excepciones:
#   @throws: Ninguna excepción explícita.
#
# 💡 Ejemplos de uso:
#   @example: fn_download_wp_cli → Descarga e instala WP-CLI
#   @example: fn_download_wp_cli "param" → Descarga e instala WP-CLI
#
# 🎯 Casos de uso:
#   - Instalación de WP-CLI en WordPress
#   - Setup inicial de herramientas de WordPress
#   - Verificación de versión de WordPress
#   - Configuración de entorno de desarrollo
#
# 🔧 Dependencias:
#   - Variables: PATH_DOMAIN, WP_CLI
#   - Función: check_install_wp_cli
#   - Función: msg
#   - Función: pause_continue
#   - curl: Para descargar WP-CLI
# =============================================================================
function fn_download_wp_cli() {
  cd "$PATH_DOMAIN"
  check_install_wp_cli
  #----paranmetros
  PARAM_1=$1
  msg ""
  msg "Descargando WP-CLI ..."
  curl -O "https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar"
  msg ""
  msg "Version de Wordpress instalada:"
  $WP_CLI core version
  pause_continue 'Descargado wp-cli'
}



function fn_download_wordpress() {
  #----paranmetros
  PARAM_1=$1

  read -p "Seguro que deseas borrar $PATH_DOMAIN y reinstalar WordPress? (s/N): " confirm
  [[ "$confirm" != "s" && "$confirm" != "S" ]] && return

  # limpiamos la instalacion de wordpres anterior
  rm -rf "$PATH_DOMAIN"/*
  cd "$PATH_TEMP"

  msg "Limpiandos files de wordpress..."

  #  rm -rf ./wp-admin/*
  #  rm -rf ./wp-content.*
  #  rm -rf ./wp-includes.*
  rm -rf ./*.*
  sleep 3
  # ::: descargamos y ponemos la ultima version de wordpress
  msg "Descargando wordpress..."
  curl -O $DOWNLOAD_URL_WORDPRESS
  tar -xzvf *es_ES.tar.gz && cp -R ./wordpress/* "${PATH_DOMAIN}/"
  # ::: limpiar basuras
  rm -rf ./wordpress
  rm -rf ./*.tar.gz
  #  descargar wp-cli
  cd "$PATH_DOMAIN"
  check_install_wp_cli
  # generaremos el wp-config.php con los accesos a la  DB
  generate_config_wp

  fn_create_wordpress_db
  pause_continue 'Se instalo por completo Wordpress'
  msg "✅  Tarea Correcta " "SUCCESS"

}

function fn_backup_wordpress() {
  #----paranmetros
  PARAM_1=$1
  FECHA_TEMP=$(date -u -d "-5 hours" "+%Y-%m-%d_%H-%M")
  # ::: Backup
  msg "Backup files de wordpress..."

  PATH_BACKUP="${ROOT_PATH}/my_resource/backup_files/${FECHA_TEMP}_${APACHE_PUBLIC_ROOT}/"

  # :: creacion de dirs
  mkdir -p "${PATH_BACKUP}"

  # :: copiar files
  cd "$PATH_DOMAIN"

  cp -a "${PATH_DOMAIN}/." "${PATH_BACKUP}/"
  msg "============================================="
  msg "backup generado en: ${PATH_BACKUP}/"

  du -smh "${PATH_DOMAIN}" | awk '{print "Peso de nuestra web es: " $1}'
  du -smh "${PATH_BACKUP}" | awk '{print "Peso del backup es: " $1}'
  pause_continue 'Se realizo el backup de wordpress'

}


# =============================================================================
# 🔌 Función: fn_check_conexion_db
# =============================================================================
# ✅ Descripción:
#   Verifica la conectividad con la base de datos MySQL/MariaDB configurada.
#   Realiza una consulta de prueba para validar que la conexión funciona correctamente.
#
# 🔧 Parámetros:
#   Ninguno - La función no requiere parámetros de entrada.
#
# 📤 Retorna:
#   0 si la conexión es exitosa, 1 si falla la conexión.
#
# 🚨 Excepciones:
#   @throws: Ninguna excepción explícita, pero imprime errores detallados.
#
# 💡 Ejemplos de uso:
#   @example: fn_check_conexion_db → 0 (conexión exitosa)
#   @example: fn_check_conexion_db → 1 (error de conexión)
#
# 🎯 Casos de uso:
#   - Validación de configuración de base de datos
#   - Verificación antes de operaciones críticas
#   - Diagnóstico de problemas de conectividad
#   - Testing de configuración de WordPress
#
# 🔧 Dependencias:
#   - Variables: PATH_MYSQL, PATH_CONFIG_MYSQL, SSL_OPTION, DB_NAME
#   - Variables: DB_HOST, DB_PORT, DB_USER, DB_PASSWORD
#   - Función: fn_get_mysql_version
#   - Función: msg
# =============================================================================
function fn_check_conexion_db() {
  msg "Verificando conexión a la base de datos..."
  # ::::: Version de mysql
  fn_get_mysql_version

  # Intenta conectar a la base de datos
  if "$PATH_MYSQL" --defaults-file="$PATH_CONFIG_MYSQL" $SSL_OPTION "$DB_NAME" -e "SELECT 1" >/dev/null 2>&1; then
    msg "Conexión a la base de datos exitosa."  "SUCCESS"
    return 0
  else
    msg "Error: No se pudo conectar a la base de datos." "ERROR"
    msg "${Yellow}\"$PATH_MYSQL\" --defaults-file=\"$PATH_CONFIG_MYSQL\" $SSL_OPTION  \"$DB_NAME\" -e \"SELECT 1\" " "DEBUG"
    msg "DB_HOST: ${DB_HOST}" "INFO"
    msg "DB_PORT: ${DB_PORT}" "INFO"
    msg "DB_USER: ${DB_USER}" "INFO"
    msg "DB_PASSWORD: ${DB_PASSWORD}" "INFO"
    msg "DB_NAME: ${DB_NAME}" "INFO"
    return 1
  fi
}

# =============================================================================
# 🔧 Función: fn_set_collate_db
# =============================================================================
# ✅ Descripción:
#   Normaliza las collations en archivos SQL para evitar problemas de compatibilidad
#   entre diferentes versiones de MySQL/MariaDB durante la importación.
#
# 🔧 Parámetros:
#   @param PATH_FILE_SQL: Ruta al archivo SQL a procesar (no nula).
#
# 📤 Retorna:
#   No retorna valor, modifica el archivo SQL directamente.
#
# 🚨 Excepciones:
#   @throws: Ninguna excepción explícita.
#
# 💡 Ejemplos de uso:
#   @example: fn_set_collate_db "/backup/database.sql" → Modifica el archivo
#   @example: fn_set_collate_db "backup_${DB_NAME}.sql" → Normaliza collations
#
# 🎯 Casos de uso:
#   - Preparación de backups para importación
#   - Normalización de archivos SQL
#   - Compatibilidad entre versiones de MySQL
#   - Migración de bases de datos
#
# 🔧 Dependencias:
#   - sed: Para realizar reemplazos en el archivo
# =============================================================================
function fn_set_collate_db() {
  #----paranmetros
  PATH_FILE_SQL=$1

  sed -i 's/utf8mb4_unicode_520_ci/utf8mb4_unicode_ci/g' "$PATH_FILE_SQL"
  sed -i 's/utf8mb4_0900_ai_ci/utf8mb4_general_ci/g' "$PATH_FILE_SQL"
}



# =============================================================================
# 💾 Función: fn_backup_wordpress_db
# =============================================================================
# ✅ Descripción:
#   Crea un backup completo de la base de datos de WordPress incluyendo rutinas,
#   triggers y estructura completa. El backup se optimiza para compatibilidad.
#
# 🔧 Parámetros:
#   Ninguno - La función no requiere parámetros de entrada.
#
# 📤 Retorna:
#   0 si el backup se realiza exitosamente, 1 si falla.
#
# 🚨 Excepciones:
#   @throws: Error si no se puede conectar a la base de datos.
#
# 💡 Ejemplos de uso:
#   @example: fn_backup_wordpress_db → 0 (backup exitoso)
#   @example: fn_backup_wordpress_db → 1 (error de conexión)
#
# 🎯 Casos de uso:
#   - Backup regular de bases de datos WordPress
#   - Preparación para migración
#   - Respaldo antes de actualizaciones
#   - Backup automatizado de sitios web
#
# 🔧 Dependencias:
#   - Variables: PATH_MYSQL_DUMP, PATH_CONFIG_MYSQL, SSL_OPTION, DB_NAME
#   - Variables: PATH_MYSQL_DUMP_PARAMETERS, ROOT_PATH
#   - Función: fn_check_conexion_db
#   - Función: fn_get_mysql_version
#   - Función: fn_set_collate_db
#   - Función: msg
# =============================================================================
function fn_backup_wordpress_db() {
  # Verificar la conexión a la base de datos
  if ! fn_check_conexion_db; then
    msg "Error: No se puede realizar el backup debido a un problema de conexión." "ERROR"
    return 1
  fi
  # obtenemos la version de
  fn_get_mysql_version

  PATH_FILE_SQL="${ROOT_PATH}/my_resource/backup_db/backup_${DB_NAME}.sql"

  "$PATH_MYSQL_DUMP" --defaults-file="$PATH_CONFIG_MYSQL" \
    $SSL_OPTION \
    --databases "$DB_NAME" \
    --routines --triggers --add-drop-database ${PATH_MYSQL_DUMP_PARAMETERS} \
    --single-transaction --skip-lock-tables \
    --default-character-set=utf8mb4 \
    --skip-set-charset \
    --result-file="$PATH_FILE_SQL"

  # ::: reemplazar la collation para  evitar problemas de importacion
  fn_set_collate_db "$PATH_FILE_SQL"

  msg "============================================="
  msg "Exportación finalizada correctamente: ${PATH_FILE_SQL}"
  msg "✅  Tarea Correcta " "SUCCESS"
  pause_continue 'Se realizo backup de la DB'

}




# =============================================================================
# ⏪ Función: fn_restore_wordpress_db
# =============================================================================
# ✅ Descripción:
#   Restaura la base de datos de WordPress desde un archivo de backup SQL.
#   Elimina la base de datos existente y crea una nueva con el contenido del backup.
#
# 🔧 Parámetros:
#   Ninguno - La función no requiere parámetros de entrada.
#
# 📤 Retorna:
#   No retorna valor específico, pero puede fallar si el archivo no existe.
#
# 🚨 Excepciones:
#   @throws: Error si el archivo de backup no existe.
#
# 💡 Ejemplos de uso:
#   @example: fn_restore_wordpress_db → Restaura desde backup_${DB_NAME}.sql
#   @example: fn_restore_wordpress_db → Error si no existe el archivo
#
# 🎯 Casos de uso:
#   - Restauración de backups de WordPress
#   - Migración de sitios web
#   - Recuperación de datos perdidos
#   - Testing de configuraciones
#
# 🔧 Dependencias:
#   - Variables: ROOT_PATH, DB_NAME, PATH_MYSQL, PATH_CONFIG_MYSQL, SSL_OPTION
#   - Función: fn_get_mysql_version
#   - Función: msg
#   - sed: Para normalizar collations
# =============================================================================
function fn_restore_wordpress_db() {

  PATH_FILE_SQL="${ROOT_PATH}/my_resource/backup_db/backup_${DB_NAME}.sql"

  msg "============================================="
  msg "Fichero SQL Backup: ${PATH_FILE_SQL}"

  if [ ! -f "${PATH_FILE_SQL}" ]; then
    msg -e "El archivo NO existe" "ERROR"
    return
  fi
  # ::::: Version de mysql
    fn_get_mysql_version


  # Reemplazar collation si es necesario
  sed -i 's/utf8mb4_0900_ai_ci/utf8mb4_general_ci/g' "$PATH_FILE_SQL"

  # Eliminar y recrear la base de datos
  "$PATH_MYSQL" --defaults-file="$PATH_CONFIG_MYSQL" $SSL_OPTION \
    -e "DROP DATABASE IF EXISTS ${DB_NAME}; CREATE DATABASE ${DB_NAME};"

  "$PATH_MYSQL" --defaults-file="$PATH_CONFIG_MYSQL" $SSL_OPTION \
    "$DB_NAME" --default-character-set=utf8 --comments <"$PATH_FILE_SQL"
  msg "✅  Tarea Correcta " "SUCCESS"
  pause_continue 'Se restauro la DB'
}

# =============================================================================
# ℹ️ Función: fn_info_php
# =============================================================================
# ✅ Descripción:
#   Muestra información detallada de la configuración PHP relevante para WordPress.
#   Filtra y muestra solo las configuraciones más importantes para el rendimiento.
#
# 🔧 Parámetros:
#   Ninguno - La función no requiere parámetros de entrada.
#
# 📤 Retorna:
#   No retorna valor específico, imprime la información PHP en stdout.
#
# 🚨 Excepciones:
#   @throws: Ninguna excepción explícita.
#
# 💡 Ejemplos de uso:
#   @example: fn_info_php → Muestra configuración PHP filtrada
#   @example: fn_info_php → memory_limit, post_max_size, upload_max_filesize, etc.
#
# 🎯 Casos de uso:
#   - Verificación de configuración PHP
#   - Diagnóstico de problemas de rendimiento
#   - Optimización de WordPress
#   - Validación de límites de memoria y archivos
#
# 🔧 Dependencias:
#   - Variable PHP_BIN: Ruta al binario PHP
#   - Variables de color: Gray
#   - Función: msg
#   - Función: pause_continue
#   - grep: Para filtrar configuraciones específicas
# =============================================================================
function fn_info_php() {

  $PHP_BIN -i | grep -E 'memory_limit|post_max_size|upload_max_filesize|max_input_vars|max_execution_time|max_input_time'
  msg -e "${Gray}"
  pause_continue 'Informacion de PHP'

}

function fn_check_mysql_permissions() {
  msg "Verificando permisos de usuario MySQL..." "INFO"

  # Verificar conexión primero
  if ! fn_check_conexion_db; then
    msg "Error: No se puede verificar permisos debido a un problema de conexión." "ERROR"
    return 1
  fi
  # ::::: Version de mysql
  fn_get_mysql_version


  msg "=== PERMISOS DEL USUARIO ACTUAL ===" "INFO"
  "$PATH_MYSQL" --defaults-file="$PATH_CONFIG_MYSQL" $SSL_OPTION -e "SHOW GRANTS;"

  msg "=== USUARIOS EN EL SISTEMA ===" "INFO"
  "$PATH_MYSQL" --defaults-file="$PATH_CONFIG_MYSQL" $SSL_OPTION -e "SELECT User, Host FROM mysql.user;"

  msg "=== PERMISOS ESPECÍFICOS PARA LA BASE DE DATOS ===" "INFO"
  "$PATH_MYSQL" --defaults-file="$PATH_CONFIG_MYSQL" $SSL_OPTION -e "SHOW GRANTS FOR CURRENT_USER() ON \`$DB_NAME\`.*;"

  pause_continue 'Se verificaron los permisos de MySQL'
}

# =============================================================================
# 🔧 Función: fn_fix_action_scheduler
# =============================================================================
# ✅ Descripción:
#   Repara las tablas de Action Scheduler (WooCommerce) cuando hay errores
#   de clave primaria duplicada. Resetea el auto-increment y limpia registros.
#
# 🔧 Parámetros:
#   Ninguno - La función no requiere parámetros de entrada.
#
# 📤 Retorna:
#   0 si la reparación es exitosa, 1 si falla.
#
# 🚨 Excepciones:
#   @throws: Error si no se puede conectar a la base de datos.
#
# 💡 Ejemplos de uso:
#   @example: fn_fix_action_scheduler → Repara Action Scheduler
#
# 🎯 Casos de uso:
#   - Error: Duplicate entry '0' for key 'PRIMARY'
#   - Problemas con WooCommerce Action Scheduler
#   - Reparación de tablas corruptas
#
# 🔧 Dependencias:
#   - Variables: PATH_MYSQL, PATH_CONFIG_MYSQL, SSL_OPTION, DB_NAME
#   - Función: fn_check_conexion_db, fn_get_mysql_version, msg
# =============================================================================
function fn_fix_action_scheduler() {
  msg "=========================================" "INFO"
  msg "🔧 REPARANDO ACTION SCHEDULER" "INFO"
  msg "=========================================" "INFO"
  echo ""

  if ! fn_check_conexion_db; then
    msg "Error: No se puede reparar debido a problemas de conexión." "ERROR"
    return 1
  fi

  fn_get_mysql_version
  echo ""

  msg "📊 Analizando tablas..." "INFO"

  local tables_exist=$("$PATH_MYSQL" --defaults-file="$PATH_CONFIG_MYSQL" $SSL_OPTION "$DB_NAME" \
    -e "SHOW TABLES LIKE 'wp_actionscheduler%';" 2>/dev/null | wc -l)

  if [ "$tables_exist" -eq 0 ]; then
    msg "No se encontraron tablas de Action Scheduler" "WARNING"
    pause_continue
    return 0
  fi

  msg "Tablas encontradas: $tables_exist" "INFO"
  echo ""

  msg "📦 Creando backup..." "INFO"
  local backup_file="${ROOT_PATH}/my_resource/backup_db/action_scheduler_$(date +%Y%m%d_%H%M%S).sql"
  mkdir -p "${ROOT_PATH}/my_resource/backup_db"

  "$PATH_MYSQL_DUMP" --defaults-file="$PATH_CONFIG_MYSQL" $SSL_OPTION \
    "$DB_NAME" wp_actionscheduler_actions wp_actionscheduler_claims \
    wp_actionscheduler_groups wp_actionscheduler_logs \
    --result-file="$backup_file" 2>/dev/null

  [ -f "$backup_file" ] && msg "Backup: $backup_file" "SUCCESS"
  echo ""

  msg "Método de reparación:" "INFO"
  msg "1. Ligera (resetear auto-increment)"
  msg "2. Completa (limpiar y resetear)"
  msg "3. Profunda (recrear tablas)"
  msg "4. Cancelar"
  echo ""
  read -rp "Opción [1-4]: " repair_option

  case $repair_option in
    1)
      msg "🔧 Reparación ligera..." "INFO"
      "$PATH_MYSQL" --defaults-file="$PATH_CONFIG_MYSQL" $SSL_OPTION "$DB_NAME" -e "
SET @max_id = (SELECT IFNULL(MAX(action_id), 0) FROM wp_actionscheduler_actions);
SET @sql = CONCAT('ALTER TABLE wp_actionscheduler_actions AUTO_INCREMENT = ', @max_id + 1);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
REPAIR TABLE wp_actionscheduler_actions; OPTIMIZE TABLE wp_actionscheduler_actions;"
      msg "Completada" "SUCCESS"
      ;;
    2)
      msg "🔧 Reparación completa..." "INFO"
      "$PATH_MYSQL" --defaults-file="$PATH_CONFIG_MYSQL" $SSL_OPTION "$DB_NAME" -e "
DELETE FROM wp_actionscheduler_actions WHERE status IN ('complete','failed','canceled') AND scheduled_date_gmt < DATE_SUB(NOW(), INTERVAL 30 DAY);
DELETE FROM wp_actionscheduler_logs WHERE action_id NOT IN (SELECT action_id FROM wp_actionscheduler_actions);
SET @max_id = (SELECT IFNULL(MAX(action_id), 0) FROM wp_actionscheduler_actions);
SET @sql = CONCAT('ALTER TABLE wp_actionscheduler_actions AUTO_INCREMENT = ', @max_id + 1);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
REPAIR TABLE wp_actionscheduler_actions, wp_actionscheduler_claims, wp_actionscheduler_groups, wp_actionscheduler_logs;
OPTIMIZE TABLE wp_actionscheduler_actions, wp_actionscheduler_claims, wp_actionscheduler_groups, wp_actionscheduler_logs;"
      msg "Completada" "SUCCESS"
      ;;
    3)
      read -rp "⚠️  ELIMINAR TODAS las tareas? (s/N): " confirm
      if [[ "$confirm" == "s" || "$confirm" == "S" ]]; then
        msg "🔧 Reparación profunda..." "INFO"
        "$PATH_MYSQL" --defaults-file="$PATH_CONFIG_MYSQL" $SSL_OPTION "$DB_NAME" -e "
TRUNCATE TABLE wp_actionscheduler_actions; TRUNCATE TABLE wp_actionscheduler_claims;
TRUNCATE TABLE wp_actionscheduler_groups; TRUNCATE TABLE wp_actionscheduler_logs;
ALTER TABLE wp_actionscheduler_actions AUTO_INCREMENT = 1;
OPTIMIZE TABLE wp_actionscheduler_actions, wp_actionscheduler_claims, wp_actionscheduler_groups, wp_actionscheduler_logs;"
        msg "Completada - WooCommerce recreará tareas automáticamente" "SUCCESS"
      else
        msg "Cancelada" "INFO"
      fi
      ;;
    *) msg "Cancelada" "INFO" ;;
  esac

  echo ""
  msg "📊 Estado final:" "INFO"
  "$PATH_MYSQL" --defaults-file="$PATH_CONFIG_MYSQL" $SSL_OPTION "$DB_NAME" -e "
SELECT 'Total' as Estado, COUNT(*) as Cantidad FROM wp_actionscheduler_actions
UNION ALL SELECT 'Pendientes', COUNT(*) FROM wp_actionscheduler_actions WHERE status='pending'
UNION ALL SELECT 'Completadas', COUNT(*) FROM wp_actionscheduler_actions WHERE status='complete'
UNION ALL SELECT 'Fallidas', COUNT(*) FROM wp_actionscheduler_actions WHERE status='failed';"

  msg "✅ Reparación finalizada" "SUCCESS"
  pause_continue 'Reparación completada'
}

function fn_create_wordpress_db() {
# ::::: Version de mysql
  fn_get_mysql_version

  # Eliminar y recrear la base de datos
  "$PATH_MYSQL" --defaults-file="$PATH_CONFIG_MYSQL" $SSL_OPTION \
    -e "DROP DATABASE IF EXISTS ${DB_NAME}; CREATE DATABASE ${DB_NAME};"
  msg "✅  Tarea Correcta " "SUCCESS"
  pause_continue 'Se creo la DB'
}

function fn_backup_completo_wordpress() {

  #----Proceso backup files
  PARAM_1=$1
  FECHA_TEMP=$(date -u -d "-5 hours" "+%Y-%m-%d_%H-%M")
  msg "Backup files de wordpress..."
  PATH_BACKUP_COMPLETE="${ROOT_PATH}/my_resource/completo/${FECHA_TEMP}_${APACHE_PUBLIC_ROOT}"
  mkdir -p "${PATH_BACKUP_COMPLETE}"
  # :: copiar files
  cd "$PATH_DOMAIN"

  cp -a "${PATH_DOMAIN}/." "${PATH_BACKUP_COMPLETE}/"
  #    cp -R ./wp-admin/* "${ROOT_PATH}/my_resource/completo/${FECHA_TEMP}_${APACHE_PUBLIC_ROOT}/wp-admin/"
  #    cp -R ./wp-content/* "${ROOT_PATH}/my_resource/completo/${FECHA_TEMP}_${APACHE_PUBLIC_ROOT}/wp-content/"
  #    cp -R ./wp-includes/* "${ROOT_PATH}/my_resource/completo/${FECHA_TEMP}_${APACHE_PUBLIC_ROOT}/wp-includes/"
  #    cp -R ./soluciones-tools/* "${ROOT_PATH}/my_resource/completo/${FECHA_TEMP}_${APACHE_PUBLIC_ROOT}/soluciones-tools/"
  #    cp -R  *.* "${ROOT_PATH}/my_resource/completo/${FECHA_TEMP}_${APACHE_PUBLIC_ROOT}/"

  #----Proceso backup DB
  msg "Backup files de DB..."

  PATH_FILE_SQL="${PATH_BACKUP_COMPLETE}/backup_${DB_NAME}.sql"
  "$PATH_MYSQL_DUMP" --defaults-file="$PATH_CONFIG_MYSQL" \
    $SSL_OPTION \
    --databases "$DB_NAME" \
    --routines --triggers --add-drop-database ${PATH_MYSQL_DUMP_PARAMETERS} \
    --single-transaction --skip-lock-tables \
    --default-character-set=utf8mb4 \
    --skip-set-charset \
    --result-file="$PATH_FILE_SQL"

  sed -i 's/utf8mb4_unicode_520_ci/utf8mb4_unicode_ci/g' "$PATH_FILE_SQL"

  msg "==============================================================="
  msg "backup generado en: ${PATH_BACKUP_COMPLETE}"
  msg "backup sql en: ${PATH_FILE_SQL}"
  msg "✅  Tarea Correcta " "SUCCESS"
  pause_continue 'Se realizo el backup de wordpress y DB'
  #  "${PATH_MYSQL_DUMP}" --defaults-file="${PATH_CONFIG_MYSQL}" --ssl-mode=DISABLED -h $DB_HOST -u $DB_USER $DB_NAME  --routines --triggers --skip-opt --lock-tables --set-gtid-purged=OFF  --result-file="${PATH_FILE_SQL}"
}

function fn_update_wordpres_version() {
  cd "$PATH_DOMAIN"
  pwd
  check_install_wp_cli
  $WP_CLI core update
  msg "✅  Tarea Correcta " "SUCCESS"
  pause_continue 'Se actualizo la version de wordpress'
}

function generate_config_wp() {
  path_file="${PATH_DOMAIN}/wp-config-sample.php"
  path_file_new="${PATH_DOMAIN}/wp-config.php"
  cp "$path_file" "$path_file_new"

  sed -i "s/database_name_here/${DB_NAME}/g" "$path_file_new"
  sed -i "s/username_here/${DB_USER}/g" "$path_file_new"
  sed -i "s/password_here/${DB_PASSWORD}/g" "$path_file_new"
  # Aqui se pone el servicio de dockercompose de mysql
  #  sed -i "s/localhost/db_mysql/g" "$path_file_new"
  sed -i "s/localhost/${DB_HOST}/g" "$path_file_new"
  msg "✅  Tarea Correcta " "SUCCESS"
  pause_continue "Se creo el archivo wp-config.php"
}

# =============================================================================
# 🔌 Función: fn_install_plugin
# =============================================================================
# ✅ Descripción:
#   Instala y activa plugins de WordPress de forma interactiva usando WP-CLI.
#   Ofrece una lista de plugins recomendados y permite instalación manual.
#
# 🔧 Parámetros:
#   Ninguno - La función no requiere parámetros de entrada.
#
# 📤 Retorna:
#   0 si la instalación es exitosa, 1 si falla.
#
# 🚨 Excepciones:
#   @throws: Error si no se puede acceder al directorio de WordPress.
#   @throws: Error si WP-CLI no está disponible.
#
# 💡 Ejemplos de uso:
#   @example: fn_install_plugin → Instala plugins interactivamente
#   @example: fn_install_plugin → Muestra lista de plugins recomendados
#
# 🎯 Casos de uso:
#   - Instalación masiva de plugins
#   - Configuración inicial de WordPress
#   - Instalación de plugins recomendados
#   - Automatización de setup de sitios web
#
# 🔧 Dependencias:
#   - Variables: PATH_DOMAIN, WP_CLI
#   - Función: check_install_wp_cli
#   - Función: msg
#   - Función: pause_continue
#   - WP-CLI: Para instalación de plugins
# =============================================================================
function fn_install_plugin() {

  cd "$PATH_DOMAIN" || {
    msg "No se pudo acceder al directorio $PATH_DOMAIN" "ERROR"
    return 1
  }

  # Verificar WP CLI
  check_install_wp_cli || return 1

  # Lista de plugins recomendados
  local recommended_plugins=(
    "advanced-database-cleaner-pro"
    "contact-form-7"
    "wp-reset"
    "woocommerce-customizer"
    "elementor"
    "wpforms-lite"
    "elementskit-lite"
    "litespeed-cache"
    "bdthemes-prime-slider-lite"
    "pro-elements"
    "header-footer-elementor"
    "woocommerce"
    "perfect-woocommerce-brands"
    "woocommerce-direct-checkout"
    "wordfence"
    "wps-hide-login"
    "limit-login-attempts-reloaded"
    "loco-translate"
  )

  # Mostrar men煤 interactivo
  msg "=========== PLUGINS RECOMENDADOS ==========" "INFO"
  for i in "${!recommended_plugins[@]}"; do
    msg "$((i + 1)). ${recommended_plugins[$i]}"
  done
  msg "0. Ingresar manualmente otro plugin"
  msg "==========================================" "INFO"

  # Obtener selecci贸n del usuario
  local plugin_input
  read -rp "Seleccione un número, nombre del plugin o múltiples plugins separados por comas: " user_input

  # Verificar si el usuario seleccion贸 un n煤mero
  if [[ "$user_input" =~ ^[0-9]+$ ]]; then
    if ((user_input == 0)); then
      read -rp "Ingrese el nombre del plugin o múltiples plugins separados por comas: " plugin_input
    elif ((user_input <= ${#recommended_plugins[@]})); then
      plugin_input="${recommended_plugins[$((user_input - 1))]}"
    else
      msg "Número inválido" "ERROR"
      return 1
    fi
  else
    plugin_input="$user_input"
  fi

  # Validar que se ingresé un nombre
  if [ -z "$plugin_input" ]; then
    msg "No se ingresé ningún nombre de plugin" "ERROR"
    return 1
  fi

  # Convertir la entrada en un array separado por comas
  IFS=',' read -ra plugin_list <<<"$plugin_input"

  # Instalar cada plugin
  local success_count=0
  local total_plugins=${#plugin_list[@]}

  for plugin_name in "${plugin_list[@]}"; do
    # Limpiar espacios en blanco
    plugin_name=$(echo "$plugin_name" | xargs)

    if [ -n "$plugin_name" ]; then
      msg "Instalando plugin: $plugin_name..."
      if $WP_CLI plugin install "$plugin_name" --activate; then
        msg "Plugin $plugin_name instalado y activado correctamente"
        ((success_count++))
      else
        msg "❌ Error al instalar el plugin $plugin_name" "ERROR"
      fi
    fi
  done

  # Resumen final
  msg "=========================================" "INFO"
  msg "   Resumen de instalación:" "INFO"
  msg "   Total de plugins: $total_plugins" "INFO"
  msg "   Instalados exitosamente: $success_count" "INFO"
  msg "   Fallos: $((total_plugins - success_count))" "INFO"
  msg "=========================================" "INFO"

  pause_continue
}

# =============================================================================
# 🔌 Función: fn_deactivate_plugin
# =============================================================================
# ✅ Descripción:
#   Desactiva plugins de WordPress de forma interactiva usando WP-CLI.
#   Permite desactivar plugins activos o múltiples plugins separados por comas.
#
# 🔧 Parámetros:
#   Ninguno - La función no requiere parámetros de entrada.
#
# 📤 Retorna:
#   0 si la desactivación es exitosa, 1 si falla.
#
# 🚨 Excepciones:
#   @throws: Error si no se puede acceder al directorio de WordPress.
#   @throws: Error si WP-CLI no está disponible.
#
# 💡 Ejemplos de uso:
#   @example: fn_deactivate_plugin → Desactiva plugins interactivamente
#   @example: fn_deactivate_plugin → Muestra lista de plugins activos
#
# 🎯 Casos de uso:
#   - Desactivación de plugins problemáticos
#   - Debugging de conflictos entre plugins
#   - Mantenimiento de WordPress
#   - Desactivación masiva de plugins
#
# 🔧 Dependencias:
#   - Variables: PATH_DOMAIN, WP_CLI
#   - Función: check_install_wp_cli
#   - Función: msg
#   - Función: pause_continue
#   - WP-CLI: Para desactivación de plugins
# =============================================================================
function fn_deactivate_plugin() {

  cd "$PATH_DOMAIN" || {
    msg "No se pudo acceder al directorio $PATH_DOMAIN" "ERROR"
    return 1
  }

  # Verificar WP CLI
  check_install_wp_cli || return 1

  # Obtener lista de plugins activos
  msg "=========== PLUGINS ACTIVOS ==========" "INFO"

  # Guardar la lista de plugins activos en un array
  mapfile -t active_plugins < <($WP_CLI plugin list --status=active --field=name 2>/dev/null)

  if [ ${#active_plugins[@]} -eq 0 ]; then
    msg "No hay plugins activos para desactivar" "WARNING"
    pause_continue
    return 0
  fi

  # Mostrar plugins activos con números
  for i in "${!active_plugins[@]}"; do
    msg "$((i + 1)). ${active_plugins[$i]}"
  done
  msg "0. Desactivar TODOS los plugins activos"
  msg "==========================================" "INFO"

  # Obtener selección del usuario
  local plugin_input
  read -rp "Seleccione un número, nombre del plugin o múltiples plugins separados por comas: " user_input

  # Verificar si el usuario seleccionó un número
  if [[ "$user_input" =~ ^[0-9]+$ ]]; then
    if ((user_input == 0)); then
      # Desactivar todos los plugins
      msg "¿Está seguro de desactivar TODOS los plugins activos? (s/N): " "WARNING"
      read -rp "" confirm
      if [[ "$confirm" == "s" || "$confirm" == "S" ]]; then
        msg "Desactivando todos los plugins..."
        if $WP_CLI plugin deactivate --all; then
          msg "Todos los plugins han sido desactivados correctamente" "SUCCESS"
        else
          msg "Error al desactivar los plugins" "ERROR"
          return 1
        fi
      else
        msg "Operación cancelada" "INFO"
        return 0
      fi
    elif ((user_input <= ${#active_plugins[@]})); then
      plugin_input="${active_plugins[$((user_input - 1))]}"
    else
      msg "Número inválido" "ERROR"
      return 1
    fi
  else
    plugin_input="$user_input"
  fi

  # Validar que se ingresó un nombre
  if [ -z "$plugin_input" ]; then
    msg "No se ingresó ningún nombre de plugin" "ERROR"
    return 1
  fi

  # Convertir la entrada en un array separado por comas
  IFS=',' read -ra plugin_list <<<"$plugin_input"

  # Desactivar cada plugin
  local success_count=0
  local total_plugins=${#plugin_list[@]}

  for plugin_name in "${plugin_list[@]}"; do
    # Limpiar espacios en blanco
    plugin_name=$(echo "$plugin_name" | xargs)

    if [ -n "$plugin_name" ]; then
      msg "Desactivando plugin: $plugin_name..."
      if $WP_CLI plugin deactivate "$plugin_name"; then
        msg "Plugin $plugin_name desactivado correctamente" "SUCCESS"
        ((success_count++))
      else
        msg "❌ Error al desactivar el plugin $plugin_name" "ERROR"
      fi
    fi
  done

  # Resumen final
  msg "=========================================" "INFO"
  msg "   Resumen de desactivación:" "INFO"
  msg "   Total de plugins: $total_plugins" "INFO"
  msg "   Desactivados exitosamente: $success_count" "INFO"
  msg "   Fallos: $((total_plugins - success_count))" "INFO"
  msg "=========================================" "INFO"

  pause_continue
}

# =============================================================================
# 📋 Función: fn_list_plugins
# =============================================================================
# ✅ Descripción:
#   Lista todos los plugins instalados en WordPress con información detallada
#   incluyendo estado (activo/inactivo), versión y actualizaciones disponibles.
#
# 🔧 Parámetros:
#   Ninguno - La función no requiere parámetros de entrada.
#
# 📤 Retorna:
#   0 si la operación es exitosa, 1 si falla.
#
# 🚨 Excepciones:
#   @throws: Error si no se puede acceder al directorio de WordPress.
#   @throws: Error si WP-CLI no está disponible.
#
# 💡 Ejemplos de uso:
#   @example: fn_list_plugins → Lista todos los plugins
#   @example: fn_list_plugins → Muestra plugins activos e inactivos
#
# 🎯 Casos de uso:
#   - Ver todos los plugins instalados
#   - Verificar versiones de plugins
#   - Identificar plugins que necesitan actualización
#   - Auditoría de plugins instalados
#
# 🔧 Dependencias:
#   - Variables: PATH_DOMAIN, WP_CLI
#   - Función: check_install_wp_cli
#   - Función: msg
#   - Función: pause_continue
#   - WP-CLI: Para listado de plugins
# =============================================================================
function fn_list_plugins() {

  cd "$PATH_DOMAIN" || {
    msg "No se pudo acceder al directorio $PATH_DOMAIN" "ERROR"
    return 1
  }

  # Verificar WP CLI
  check_install_wp_cli || return 1

  msg "=========================================" "INFO"
  msg "       📋 LISTADO DE PLUGINS" "INFO"
  msg "=========================================" "INFO"
  echo ""

  # Menú de opciones de listado
  msg "Seleccione el tipo de listado:" "INFO"
  msg "1. Todos los plugins"
  msg "2. Solo plugins activos"
  msg "3. Solo plugins inactivos"
  msg "4. Plugins con actualizaciones disponibles"
  msg "5. Plugins activos con detalles completos"
  echo ""
  read -rp "Ingrese su opción [1-5]: " list_option

  echo ""
  msg "=========================================" "INFO"

  case $list_option in
    1)
      msg "📦 TODOS LOS PLUGINS:" "INFO"
      msg "=========================================" "INFO"
      $WP_CLI plugin list
      ;;
    2)
      msg "✅ PLUGINS ACTIVOS:" "SUCCESS"
      msg "=========================================" "INFO"
      $WP_CLI plugin list --status=active

      # Contar plugins activos
      local count=$($WP_CLI plugin list --status=active --format=count 2>/dev/null)
      echo ""
      msg "Total de plugins activos: $count" "INFO"
      ;;
    3)
      msg "❌ PLUGINS INACTIVOS:" "WARNING"
      msg "=========================================" "INFO"
      $WP_CLI plugin list --status=inactive

      # Contar plugins inactivos
      local count=$($WP_CLI plugin list --status=inactive --format=count 2>/dev/null)
      echo ""
      msg "Total de plugins inactivos: $count" "INFO"
      ;;
    4)
      msg "🔄 PLUGINS CON ACTUALIZACIONES DISPONIBLES:" "WARNING"
      msg "=========================================" "INFO"
      $WP_CLI plugin list --update=available

      # Contar plugins con actualizaciones
      local count=$($WP_CLI plugin list --update=available --format=count 2>/dev/null)
      echo ""
      if [ "$count" -gt 0 ]; then
        msg "Total de plugins con actualizaciones: $count" "WARNING"
        msg "Puede actualizarlos con la opción 12 del menú principal" "INFO"
      else
        msg "No hay plugins con actualizaciones disponibles" "SUCCESS"
      fi
      ;;
    5)
      msg "📊 PLUGINS ACTIVOS - INFORMACIÓN DETALLADA:" "SUCCESS"
      msg "=========================================" "INFO"

      # Lista detallada de plugins activos
      mapfile -t active_plugins < <($WP_CLI plugin list --status=active --field=name 2>/dev/null)

      if [ ${#active_plugins[@]} -eq 0 ]; then
        msg "No hay plugins activos" "WARNING"
      else
        for plugin in "${active_plugins[@]}"; do
          echo ""
          msg "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" "INFO"
          msg "Plugin: $plugin" "SUCCESS"
          $WP_CLI plugin get "$plugin" --fields=name,status,version,update_version,title 2>/dev/null
        done
        echo ""
        msg "Total de plugins activos: ${#active_plugins[@]}" "INFO"
      fi
      ;;
    *)
      msg "Opción inválida. Mostrando todos los plugins..." "WARNING"
      echo ""
      $WP_CLI plugin list
      ;;
  esac

  echo ""
  msg "=========================================" "INFO"
  pause_continue 'Se listaron los plugins'
}

function fn_check_site_status() {
  cd "$PATH_DOMAIN"
  check_install_wp_cli
  $WP_CLI core is-installed && echo "WordPress está instalado" || echo "WordPress NO esta instalado"
  pause_continue 'Se verifico el estado del sitio'
}

function fn_update_all() {
  cd "$PATH_DOMAIN"
  check_install_wp_cli
  $WP_CLI plugin update --all
  $WP_CLI theme update --all
  pause_continue "Se actualizo todos los plugins y temas"
}

# =============================================================================
# 🗑️ Función: fn_flush_cache
# =============================================================================
# ✅ Descripción:
#   Vacía todas las cachés de WordPress incluyendo caché nativa y plugins populares
#   como LiteSpeed Cache, W3 Total Cache, WP Rocket, WP Super Cache y Cache Enabler.
#
# 🔧 Parámetros:
#   Ninguno - La función no requiere parámetros de entrada.
#
# 📤 Retorna:
#   No retorna valor específico.
#
# 🚨 Excepciones:
#   @throws: Ninguna excepción explícita.
#
# 💡 Ejemplos de uso:
#   @example: fn_flush_cache → Vacía todas las cachés detectadas
#   @example: fn_flush_cache → Limpia caché nativa y plugins activos
#
# 🎯 Casos de uso:
#   - Limpieza de caché después de actualizaciones
#   - Resolución de problemas de caché
#   - Optimización de rendimiento
#   - Mantenimiento regular de WordPress
#
# 🔧 Dependencias:
#   - Variables: PATH_DOMAIN, WP_CLI
#   - Función: check_install_wp_cli
#   - Función: msg
#   - Función: pause_continue
#   - WP-CLI: Para comandos de caché
# =============================================================================
function fn_flush_cache() {
    cd "$PATH_DOMAIN"
    check_install_wp_cli

    msg "Vaciando caché de WordPress..."

    # Cache básico de WordPress
    $WP_CLI cache flush

    # Detectar y limpiar plugins de caché populares
    if $WP_CLI plugin is-active litespeed-cache; then
        msg "Limpiando LiteSpeed Cache..."
        $WP_CLI litespeed-cache purge all
    fi

    if $WP_CLI plugin is-active w3-total-cache; then
        msg "Limpiando W3 Total Cache..."
        $WP_CLI w3-total-cache flush all
    fi

    if $WP_CLI plugin is-active wp-rocket; then
        msg "Limpiando WP Rocket..."
        $WP_CLI wp-rocket clean --confirm
    fi

    if $WP_CLI plugin is-active wp-super-cache; then
        msg "Limpiando WP Super Cache..."
        $WP_CLI super-cache flush
    fi

    if $WP_CLI plugin is-active cache-enabler; then
        msg "Limpiando Cache Enabler..."
        $WP_CLI cache-enabler clear
    fi

    msg "✅ Cache limpiado correctamente" "SUCCESS"
    pause_continue 'Se vació la caché'
}

function fn_manteniment_wp_cli() {
  cd "$PATH_DOMAIN"
  msg "Mantenimento WP-cli"
  curl -sSL -o wp-maintenance.sh https://raw.githubusercontent.com/cesar23/utils_dev/refs/heads/master/scripts/wordpress/mantenimiento-wp-cli.sh &&
    chmod +x wp-maintenance.sh &&
    ./wp-maintenance.sh &&
    rm -rf wp-maintenance.sh

}

# =============================================================================
# 🧪 Función: test_script
# =============================================================================
# ✅ Descripción:
#   Ejecuta una serie de pruebas para verificar el funcionamiento correcto del script.
#   Incluye verificación de archivos, conexión a base de datos y configuración PHP.
#
# 🔧 Parámetros:
#   Ninguno - La función no requiere parámetros de entrada.
#
# 📤 Retorna:
#   No retorna valor específico.
#
# 🚨 Excepciones:
#   @throws: Ninguna excepción explícita.
#
# 💡 Ejemplos de uso:
#   @example: test_script → Ejecuta todas las pruebas de verificación
#   @example: test_script → Verifica archivos, DB y PHP
#
# 🎯 Casos de uso:
#   - Testing del script de WordPress
#   - Verificación de configuración
#   - Diagnóstico de problemas
#   - Validación de instalación
#
# 🔧 Dependencias:
#   - Variables: PATH_DOMAIN, ROOT_PATH, DB_NAME
#   - Función: fn_check_conexion_db
#   - Función: fn_info_php
#   - Función: msg
#   - Función: pause_continue
# =============================================================================
test_script(){
  msg "==============================================="
  msg " 1. verificando [$PATH_DOMAIN]"
  msg "==============================================="
  echo ""
  local PATH_WP_LOGIN="${PATH_DOMAIN}/wp-config.php"
  if [ -f "$PATH_WP_LOGIN" ]; then
    msg "El archivo de backup demo existe: ${PATH_WP_LOGIN}"
    msg "Contenido del fichero: ${BBlue}${PATH_WP_LOGIN}${Color_Off}"
    echo ""
    cat "$PATH_WP_LOGIN"
    msg "✅  Tarea Correcta " "SUCCESS"
  else
    msg "El archivo [${PATH_WP_LOGIN}] de backup NO existe: " "ERROR"
  fi
  pause_continue


  msg "==============================================="
  msg " 2. verificando conexion DB"
  msg "==============================================="
  echo ""
  fn_check_conexion_db
  msg "✅  Tarea Correcta " "SUCCESS"
  pause_continue

  msg "==============================================="
  msg " 3. Verificando generacion de  ficheros"
  msg "==============================================="
  echo ""
  local PATH_BACKUP="${ROOT_PATH}/my_resource/backup_db"
  mkdir -p "${PATH_BACKUP}"
  local PATH_FILE_SQL="${PATH_BACKUP}/backup_${DB_NAME}_test.sql"

  echo "">"$PATH_FILE_SQL"
  if [ -f "$PATH_FILE_SQL" ]; then
    msg "El archivo de backup demo existe: ${PATH_FILE_SQL}"
    msg "✅  Tarea Correcta " "SUCCESS"
  else
    msg "El archivo de backup NO existe: ${PATH_FILE_SQL}" "ERROR"
  fi
  pause_continue

  msg "==============================================="
  msg " 4. Verificando  info PHP"
  msg "==============================================="
  echo ""
  fn_info_php
  msg "✅  Tarea Correcta " "SUCCESS"
  pause_continue

}

# =============================================================================
# 🔍 Función: get_wp_var
# =============================================================================
# ✅ Descripción:
#   Extrae el valor de cualquier variable definida en el archivo wp-config.php de WordPress.
#   Soporta tanto variables con valores de cadena (entre comillas) como valores booleanos.
#
# 🔧 Parámetros:
#   @param config_file: Ruta completa al archivo wp-config.php (no nula).
#   @param var_name: Nombre de la variable a extraer (ej: 'WP_DEBUG', 'AUTH_KEY', 'DB_NAME').
#
# 📤 Retorna:
#   El valor de la variable encontrada, o cadena vacía si no se encuentra.
#
# 🚨 Excepciones:
#   @throws: Error si el archivo wp-config.php no existe.
#
# 💡 Ejemplos de uso:
#   @example: get_wp_var "/var/www/html/wp-config.php" "WP_DEBUG" → "true"
#   @example: get_wp_var "/var/www/html/wp-config.php" "DB_NAME" → "wordpress_db"
#   @example: get_wp_var "/var/www/html/wp-config.php" "AUTH_KEY" → "tu_clave_secreta_aqui"
#   @example: get_wp_var "/var/www/html/wp-config.php" "WP_MEMORY_LIMIT" → "256M"
#
# 🎯 Casos de uso:
#   - Verificar configuración de debug de WordPress
#   - Obtener credenciales de base de datos
#   - Leer claves de seguridad
#   - Validar configuraciones personalizadas
#
# 🔧 Dependencias:
#   - grep: Para buscar patrones en el archivo
#   - sed: Para extraer el valor de la variable
#   - Variables de color: Red, Color_Off
# =============================================================================
get_wp_var() {
    local config_file="$1"
    local var_name="$2"

    if [[ ! -f "$config_file" ]]; then
        echo -e "${Red}[ERROR]${Color_Off} El archivo $config_file no existe"
        return 1
    fi

    # Buscar la variable con comillas
    local value=$(grep -E "define\('$var_name',\s*'[^']*'" "$config_file" | sed -E "s/.*define\('$var_name',\s*'([^']*)'.*/\1/")

    # Si no se encontró, buscar sin comillas (para valores booleanos)
    if [[ -z "$value" ]]; then
        value=$(grep -E "define\('$var_name',\s*[^)]*" "$config_file" | sed -E "s/.*define\('$var_name',\s*([^)]*).*/\1/")
    fi

    echo "$value"
}



# =============================================================================
# 🔍 Función: search_php_bin
# =============================================================================
# ✅ Descripción:
#   Busca la ruta ejecutable de PHP en el sistema, priorizando rutas específicas
#   de servidores web y luego el PATH del sistema.
#
# 🔧 Parámetros:
#   Ninguno - La función no requiere parámetros de entrada.
#
# 📤 Retorna:
#   La ruta absoluta del binario PHP encontrado, o cadena vacía si no se encuentra.
#   Código de salida: 0 si se encuentra, 1 si no se encuentra ningún binario PHP.
#
# 🚨 Excepciones:
#   @throws: Ninguna excepción explícita.
#
# 💡 Ejemplos de uso:
#   @example: search_php_bin → "/usr/bin/php" (código 0)
#   @example: search_php_bin → "/opt/alt/php83/usr/bin/php" (código 0)
#   @example: search_php_bin → "" (código 1, PHP no encontrado)
#
# 🎯 Casos de uso:
#   - Configuración automática de rutas PHP
#   - Validación de instalación de PHP
#   - Configuración de scripts de WordPress
#   - Detección de versiones específicas de PHP
#
# 🔧 Dependencias:
#   - command -v: Para buscar comandos en el PATH
#   - Rutas específicas de servidores web (LiteSpeed, cPanel, etc.)
# =============================================================================
search_php_bin() {
  # Lista de rutas comunes donde puede estar el binario PHP
  local possible_paths=(
    "/opt/alt/php83/usr/bin/php"
    "/opt/alt/php82/usr/bin/php"
    "/usr/local/lsws/lsphp83/bin/php"
    "/usr/local/lsws/lsphp82/bin/php"
    "/usr/local/lsws/lsphp81/bin/php"
    "php"
  )

  # Busca en las rutas predefinidas
  for path in "${possible_paths[@]}"; do
    if [ -x "$path" ]; then
      echo "$path"
      return 0
    fi
  done

  # Si no se encuentra en rutas predefinidas, busca en el PATH del sistema
  local default_php
  default_php=$(command -v php)
  if [ -n "$default_php" ]; then
    echo "$default_php"
    return 0
  fi

  # Si no se encuentra ningún binario PHP, retorna error
  return 1
}

favorites() {
  clear
  # Muestra un menú de favoritos
  echo -e "${BBlue}Favoritos:${Color_Off}"
  echo -e "${Gray}========================================================================${Color_Off}"
  echo -e "${Yellow} 1. WooCommerce versión 9.9.5${Color_Off}"
  echo ""
  echo -e "${Purple} - Para instalar la version  estable${Color_Off}"
  echo -e "cd ${PATH_DOMAIN} &&  "
  echo -e "${PHP_BIN}  wp-cli.phar plugin install woocommerce --version=9.9.5 --force && "
  echo -e "${PHP_BIN}  wp-cli.phar core update-db &&  "
  echo -e "${PHP_BIN}  wp-cli.phar wc update &&  "
  echo -e "cd ${CURRENT_DIR}"
  echo ""
  echo -e "${Gray}========================================================================${Color_Off}"
  echo -e "${Yellow}2. Instalar Wordpress 6.8.1 (estable)${Color_Off}"
  echo ""
  echo -e "${Purple} - Ver version Actual${Color_Off}"
  echo -e "cd ${PATH_DOMAIN} &&  "
  echo -e "${PHP_BIN}  wp-cli.phar core version --extra && "
  echo -e "cd ${CURRENT_DIR}"
  echo ""
  echo -e "${Purple} - Actualizar tu wordpress${Color_Off}"
  echo -e "cd ${PATH_DOMAIN} &&  "
  echo -e "${PHP_BIN}  wp-cli.phar core update --version=6.8.1 --force &&  "
  echo -e "${PHP_BIN}  wp-cli.phar core update-db &&  "
  echo -e "cd ${CURRENT_DIR}"
  echo ""
  echo -e "${Purple} - Instalar directamente esa versión aunque ya tengas WordPress instalado${Color_Off}"
  echo -e "cd ${PATH_DOMAIN} &&  "
  echo -e "cd ${PATH_DOMAIN} &&   wp-cli.phar core download --version=6.8.1 --force &&  "
  echo -e "${PHP_BIN}  wp-cli.phar core update-db &&  "
  echo -e "cd ${CURRENT_DIR}"
  echo ""
  echo -e "${Gray}========================================================================${Color_Off}"
  echo -e "${Yellow}3. Plugins ${Color_Off}"
  echo ""
  echo -e "${Purple} - Para ver solo los plugins activos con WP-CLI${Color_Off}"
  echo -e "cd ${PATH_DOMAIN} &&  "
  echo -e "${PHP_BIN}  wp-cli.phar  plugin list --status=active && \ "
  echo -e "cd ${CURRENT_DIR}"
  echo ""
  echo -e "${Purple} - Para desactivar todos los plugins activos${Color_Off}"
  echo -e "cd ${PATH_DOMAIN} &&  "
  echo -e "${PHP_BIN}  wp-cli.phar  plugin deactivate --all && \ "
  echo -e "cd ${CURRENT_DIR}"
  echo ""
  echo -e "${Purple} - Si quieres desactivar todos menos uno específico, puedes hacer${Color_Off}"
  echo -e "cd ${PATH_DOMAIN} &&  "
  echo -e "${PHP_BIN}  wp-cli.phar  plugin deactivate --all --exclude=woocommerce && \ "
  echo -e "cd ${CURRENT_DIR}"
  echo ""

  pause_continue

}


# =============================================================================
# 🔥 SECTION: Main Code
# =============================================================================

# Detectar sistema operativo
SO_SYSTEM=$(detect_system)

# :::::::::::::::::::::::::::::: carga de variables

PATH_FUNCTION_LIBS_SHELL="$(dirname "$0")/libs_shell"


if [ -f "${PATH_FUNCTION_LIBS_SHELL}/read_env.sh" ]; then
  source "${PATH_FUNCTION_LIBS_SHELL}/read_env.sh"
  else
    msg "No se encontro el archivo ${PATH_FUNCTION_LIBS_SHELL}/read_env.sh" "ERROR"
    exit 1
fi










# ::::::::::::::::::::::::::: Carga de Enviroment ::::::::::::::::::::::::::
#PATH_ENV="$(dirname "$0")/.env.development.adcomputers.com.pe"
#PATH_ENV="$(dirname "$0")/.env.development.ferrechincha.com"

PATH_ENV="$(dirname "$0")/.env.navdyelstore.it"

# ::::::::::::::::::::::::::: Configuraciones mysql ::::::::::::::::::::::::::

SITE=$(find_env 'APACHE_DOMAIN_0' $PATH_ENV)
# Directorio dodne sta la web
APACHE_PUBLIC_ROOT=$(find_env 'APACHE_PUBLIC_ROOT' $PATH_ENV)

# ::::::::::::::::::::::::::::::::: Configuraciones::::::::::::::::::::::::::::::

regex="s/\/${NAME_DIR}//"
ROOT_PATH=$(echo $CURRENT_DIR | sed -e $regex) # D:/repos/curso_plugin_theme_wordpress
#PATH_DOMAIN="${ROOT_PATH}/www/${SITE}"
PATH_DOMAIN="${ROOT_PATH}/${APACHE_PUBLIC_ROOT}"
PATH_TEMP="${ROOT_PATH}/tmp/"
mkdir -p "$PATH_TEMP"


# DB_HOST=$(find_env 'MYSQL_HOST' $PATH_ENV)
# DB_PORT=$(find_env 'MYSQL_PORT' $PATH_ENV)
# DB_USER=$(find_env 'MYSQL_USER_ROOT' $PATH_ENV)
# DB_PASSWORD=$(find_env 'MYSQL_ROOT_PASSWORD_WINDOWS' $PATH_ENV)
# DB_NAME=$(find_env 'MYSQL_DATABASE' $PATH_ENV)

config_file="${PATH_DOMAIN}/wp-config.php"

echo -e "${Blue}=================================================${Color_Off}"
echo -e "${Cyan} Las variables de basde de datos las quiere conectar desde el fichero:${Color_Off}"
echo -e "${Cyan} 1. [${PATH_ENV}] ${Color_Off}"
echo -e "${Cyan} 2. [${config_file}] ${Color_Off}"
read -r opt
case $opt in
  "1")
    DB_HOST=$(find_env 'MYSQL_HOST' $PATH_ENV)
    DB_PORT=$(find_env 'MYSQL_PORT' $PATH_ENV)
    DB_USER=$(find_env 'MYSQL_USER_ROOT' $PATH_ENV)
    DB_PASSWORD=$(find_env 'MYSQL_ROOT_PASSWORD_WINDOWS' $PATH_ENV)
    DB_NAME=$(find_env 'MYSQL_DATABASE' $PATH_ENV)
    ;;
  "2")
    DB_HOST=$(get_wp_var "$config_file" "DB_HOST")
    DB_PORT=3306
    DB_USER=$(get_wp_var "$config_file" "DB_USER")
    DB_PASSWORD=$(get_wp_var "$config_file" "DB_PASSWORD")
    if [ -z "$DB_PASSWORD" ] || [ "$DB_PASSWORD" = "''" ]; then
      DB_PASSWORD=""
    fi
    DB_NAME=$(get_wp_var "$config_file" "DB_NAME")
    ;;
  *)
    echo -e "${Red}=================================================${Color_Off}"
    echo -e "${Red} Opcion no valida ${Color_Off}"
    echo -e "${Red}=================================================${Color_Off}"
    exit 1
    ;;
esac




# definir la ruta del PHP bin
msg "Detectando PHP en el sistema..." "INFO"
PHP_BIN=$(search_php_bin)
if [ $? -eq 0 ]; then
    echo -e "PHP encontrado en: $PHP_BIN" && sleep 2
    #"$PHP_BIN" -v
else
    msg "No se encontró PHP instalado. verificar la ruta con phpinfo();" "ERROR"
    exit 1
fi

# Alias r谩pido
WP_CLI="${PHP_BIN} wp-cli.phar"





DOWNLOAD_URL_WORDPRESS='https://es.wordpress.org/wordpress-6.7.1-es_ES.tar.gz'
DOWNLOAD_URL_WORDPRESS='https://es.wordpress.org/wordpress-6.8.1-es_ES.tar.gz'


PATH_CONFIG_MYSQL="${CURRENT_DIR}/config_mysql.cnf"
PATH_FILE_SQL="${CURRENT_DIR}/backup.sql"

# si estamos en so de termux, cambiar la ruta de descarga
if [ -n "$SO_SYSTEM" ] && { [ "$SO_SYSTEM" = "ubuntu" ] || [ "$SO_SYSTEM" = "debian" ] || [ "$SO_SYSTEM" = "redhat" ]; }; then
    PATH_MYSQL="mysql"
    PATH_MYSQL_DUMP="mysqldump"
else
  PATH_MYSQL="C:/laragon/bin/mysql/mysql-8.0.30-winx64/bin/mysql.exe"
  PATH_MYSQL_DUMP="C:/laragon/bin/mysql/mysql-8.0.30-winx64/bin/mysqldump.exe"
fi

PATH_MYSQL_DUMP_PARAMETERS=""

if [ "$DB_USER" = "root" ] || [ "$SO_SYSTEM" = "gitbash" ]; then
  PATH_MYSQL_DUMP_PARAMETERS="  --set-gtid-purged=AUTO "
fi

echo -e "${Gray}============================================================${Color_Off}"
echo -e "${Gray} 1. Verificando rutas ${Color_Off}"
echo -e "${Gray}============================================================${Color_Off}"
echo "verica so: ${SO_SYSTEM}"
echo "" && sleep 2
if [ -n "$SO_SYSTEM" ] && { [ "$SO_SYSTEM" = "ubuntu" ] || [ "$SO_SYSTEM" = "debian" ] || [ "$SO_SYSTEM" = "redhat" ]; }; then
    echo -e "${Gray} Verificando rutas Linux.. ${Color_Off}"
    echo ""
    if check_paths  "${PATH_ENV}" "${PATH_DOMAIN}" "${PATH_TEMP}"; then
        msg "Todas las rutas son válidas" "SUCCESS"
        sleep 1
    else
        msg " Ocurrió un error: Algunas rutas no existen" "ERROR"
        pause_continue
        exit 1
    fi
else
  echo -e "${Gray} Verificando rutas windows.. ${Color_Off}"
  echo ""
  if check_paths "${PATH_MYSQL}" "${PATH_MYSQL_DUMP}" "${PATH_ENV}" "${PATH_DOMAIN}" "${PATH_TEMP}"; then
      msg "Todas las rutas son válidas" "SUCCESS"
      sleep 1
  else
      msg " Ocurrió un error: Algunas rutas no existen" "ERROR"
      pause_continue
      exit 1
  fi
fi






echo -e "${Gray}============================================================${Color_Off}"
echo -e "${Gray} 2. Mysql - ssl-mode ${Color_Off}"
echo -e "${Gray}============================================================${Color_Off}"
echo "" && sleep 1
# ::::: verificando si el cliente mysql soporta --ssl-mode=DISABLED
is_ssl_mode=$(check_verify_ssl_mode)

if [[ "$is_ssl_mode" -eq 1 ]]; then
    echo "✔ Soporta --ssl-mode=DISABLED"
    SSL_OPTION="--ssl-mode=DISABLED"
else
    echo "✘ No soporta --ssl-mode=DISABLED (probablemente MariaDB)"
    SSL_OPTION="--ssl=0"
fi
msg "Verificacion" "SUCCESS"

echo -e "${Gray}============================================================${Color_Off}"
echo -e "${Gray} 3. Creando configuracion de Mysql ${Color_Off}"
echo -e "${Gray}============================================================${Color_Off}"
echo ""
# Crear archivo config
{
  echo "[client]"
  echo "user=\"$DB_USER\""
  [ -n "$DB_PASSWORD" ] && echo "password=\"$DB_PASSWORD\""
  echo "host=\"$DB_HOST\""
  echo "port=$DB_PORT"
} >"$PATH_CONFIG_MYSQL"
msg "Archivo de configuración creado: $PATH_CONFIG_MYSQL" "SUCCESS"
sleep 4

FECHA_HORA=$(date -u -d "-5 hours" "+%Y-%m-%d_%H:%M:%S")
ANIO=$(date +'%Y')
MES=$(date +'%m')
DIA=$(date +'%d')






# :::::::::::::::::::::::::::::: Menu Mejorado ::::::::::::::::::::::::::::::
show_menu() {

  while true; do
    clear
    echo -e "${Blue}============================================================${Color_Off}"
    echo -e "${Blue} ADMINISTRACIÓN WORDPRESS v3.1.0 ${Color_Off}"
    echo -e "${Blue} -----------------------------------------------------------${Color_Off}"
    echo -e "${Blue} ?? SO: ${SO_SYSTEM}${Color_Off}"
    echo -e "${Blue} 🗂️  CURRENT_USER: ${CURRENT_USER}${Color_Off}"
    echo -e "${Blue} 🗂️  CURRENT_DIR: ${CURRENT_DIR}${Color_Off}"
    echo -e "${Blue} 🗂️  ROOT_PATH: ${ROOT_PATH}${Color_Off}"
    echo -e "${Blue} 🗂️  PATH_DOMAIN: ${PATH_DOMAIN}${Color_Off}"
    echo -e "${Blue}============================================================${Color_Off}"

  print_menu_option() {
    local idx="$1"
    local text="$2"
    if [ "$idx" -eq 17 ]; then
      echo -e "${Yellow}●${Color_Off} ${Green}${idx})${Color_Off}${Purple} ${text} ${Yellow}●${Color_Off}"
    else
      printf "${Yellow}●${Color_Off} ${Green}%2d)${Color_Off} %s ${Yellow}●${Color_Off}\n" "$idx" "$text"
    fi
    
  }

  print_menu_option 1  "🧪 Test Ficheros"
  print_menu_option 2  "📥 Nueva instalación de WordPress"
  print_menu_option 3  "🔧 Descargar WP-CLI"
  print_menu_option 4  "🗂️ Backup de archivos de WordPress"
  print_menu_option 5  "🗂️ Backup Completo Web, Files y DB"
  print_menu_option 6  "🗄️ Backup de base de datos"
  print_menu_option 7  "⏪ Restaurar backup de base de datos"
  print_menu_option 8  "(WP-CLI) 🛠️ Mantenimiento WordPress"
  print_menu_option 9  "(WP-CLI) 🔌 Instalar plugin"
  print_menu_option 10 "(WP-CLI) 🔴 Desactivar plugin"
  print_menu_option 11 "(WP-CLI) 📋 Listar plugins instalados"
  print_menu_option 12 "(WP-CLI) 🔍 Verificar instalación de WordPress"
  print_menu_option 13 "(WP-CLI) 🔄 Actualizar plugins y temas"
  print_menu_option 14 "🛠️ Crear (o recrear) base de datos WordPress"
  print_menu_option 15 "🔧 Reparar Action Scheduler (WooCommerce)"
  print_menu_option 16 "📋 Ver Variables de configuración"
  print_menu_option 17 "⚙️ Ver php.ini configuración"
  print_menu_option 18 "🔐 Ver permisos de usuario MySQL"
  print_menu_option 19 "(WP-CLI) 🗑️ Vaciar caché de WordPress"
  print_menu_option 20 "✨ [===== Comandos Favoritos - WP_CLI =====]"
  print_menu_option 21 "🚪 Salir"

    echo -e "${Blue}=================================================${Color_Off}"
      printf "${Cyan}➡️  Seleccione una opción [1-${#options[@]} | x para salir]: ${Color_Off}"

    read -r opt
    case $opt in
    1)
      clear
        echo -e "${Green}⚙️ Test de ficheros...${Color_Off}"
      test_script
      ;;
    2)
      clear
        echo -e "${Green}🚀 Instalando WordPress...${Color_Off}"
      fn_download_wordpress
      ;;
    3)
      clear
        echo -e "${Green}🔧 Descargando WP-CLI...${Color_Off}"
      fn_download_wp_cli
      ;;
    4)
      clear
        echo -e "${Green}🗂️ Creando backup de archivos...${Color_Off}"
      fn_backup_wordpress
      ;;
    5)
      clear
        echo -e "${Green}🗂️ Creando backup completo...${Color_Off}"
      fn_backup_completo_wordpress
      ;;
    6)
      clear
        echo -e "${Green}🗄️ Creando backup de base de datos...${Color_Off}"
      fn_backup_wordpress_db
      ;;
    7)
      clear
        echo -e "${Green}⏪ Restaurando base de datos...${Color_Off}"
      fn_restore_wordpress_db
      ;;
    8)
      clear
        echo -e "${Green}🛠️ Mantenimiento WordPress WP-CLI...${Color_Off}"
      fn_manteniment_wp_cli
      ;;
    9)
      clear
        echo -e "${Green}🔌 Instalando plugin...${Color_Off}"
      fn_install_plugin
      ;;
    10)
      clear
        echo -e "${Green}🔴 Desactivando plugin...${Color_Off}"
      fn_deactivate_plugin
      ;;
    11)
      clear
        echo -e "${Green}📋 Listando plugins...${Color_Off}"
      fn_list_plugins
      ;;
    12)
      clear
        echo -e "${Green}🔍 Verificando instalación de WordPress...${Color_Off}"
      fn_check_site_status
      ;;
    13)
      clear
        echo -e "${Green}🔄 Actualizando plugins y temas...${Color_Off}"
      fn_update_all
      ;;
    14)
      clear
        echo -e "${Green}🛠️ Creando base de datos WordPress...${Color_Off}"
      fn_create_wordpress_db
      ;;
    15)
      clear
        echo -e "${Green}🔧 Reparando Action Scheduler...${Color_Off}"
      fn_fix_action_scheduler
      ;;
    16)
      clear
        echo -e "${Green}📋 Variables de configuración...${Color_Off}"
      view_vars_config && pause_continue
      ;;
    17)
      clear
        echo -e "${Green}⚙️ Configuración de PHP...${Color_Off}"
      fn_info_php
      ;;
    18)
      clear
        echo -e "${Green}🔐 Permisos de usuario MySQL...${Color_Off}"
      fn_check_mysql_permissions
      ;;
    19)
      clear
        echo -e "${Green}🗑️ Vaciando caché de WordPress...${Color_Off}"
      fn_flush_cache
      ;;
    20)
          clear
            echo -e "${Green}⚙️ Favoritos...${Color_Off}"
          favorites
          ;;
    21 | x | X)
      clear
        echo -e "${Red}🚪 Saliendo del programa...${Color_Off}"
      exit 0
      ;;
    *)
        echo -e "${Red}❌ Opción inválida. Intente nuevamente.${Color_Off}"
      sleep 1
      ;;
    esac
  done
}

# :::::::::::::::::::::::::::::: Ejecuci贸n ::::::::::::::::::::::::::::::
clear
show_menu
