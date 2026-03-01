#!/usr/bin/env bash

set -euo pipefail  # Detener script al primer error, variables no definidas y errores en pipes

# =============================================================================
# 🏆 SECTION: Configuración Inicial
# =============================================================================

# Configurar locale UTF-8 de forma segura
export LC_ALL="C.UTF-8" 2>/dev/null || export LC_ALL="C"

# Ruta completa del script actual
PATH_SCRIPT=$(readlink -f "${BASH_SOURCE:-$0}" 2>/dev/null || realpath "${BASH_SOURCE:-$0}" 2>/dev/null || echo "$0")
SCRIPT_NAME=$(basename "$PATH_SCRIPT")
CURRENT_DIR=$(dirname "$PATH_SCRIPT")

# Variables de configuración iniciales
DATE_HOUR=$(date -u -d "-5 hours" "+%Y-%m-%d_%H:%M:%S" 2>/dev/null || TZ="America/Lima" date "+%Y-%m-%d_%H:%M:%S" 2>/dev/null || date "+%Y-%m-%d_%H:%M:%S")
DATE_SIMPLE="$(date +%Y%m%d_%H%M%S)"
CURRENT_USER=$(id -un)
CURRENT_PC_NAME=$(hostname)
MY_INFO="${CURRENT_USER}@${CURRENT_PC_NAME}"
NAME_DIR=$(basename "$CURRENT_DIR")
TEMP_PATH_SCRIPT=$(echo "$PATH_SCRIPT" | sed 's/.sh/.tmp/g')
TEMP_PATH_SCRIPT_SYSTEM=$(echo "${TMP}/${SCRIPT_NAME}" | sed 's/.sh/.tmp/g')
ROOT_PATH=$(realpath -m "${CURRENT_DIR}/..")

# Variables de control
DEBUG_MODE=false
FORCE_MODE=false
LOG_FILE="${CURRENT_DIR}/logs/${SCRIPT_NAME%.sh}_${DATE_SIMPLE}.log"

# =============================================================================
# 🎨 SECTION: Colores para su uso
# =============================================================================

# Colores Regulares
Color_Off='\033[0m'
Black='\033[0;30m'
Red='\033[0;31m'
Green='\033[0;32m'
Yellow='\033[0;33m'
Blue='\033[0;34m'
Purple='\033[0;35m'
Cyan='\033[0;36m'
White='\033[0;37m'
Gray='\033[0;90m'

# Colores en Negrita
BBlack='\033[1;30m'
BRed='\033[1;31m'
BGreen='\033[1;32m'
BYellow='\033[1;33m'
BBlue='\033[1;34m'
BPurple='\033[1;35m'
BCyan='\033[1;36m'
BWhite='\033[1;37m'
BGray='\033[1;90m'

# =============================================================================
# ⚙️ SECTION: Core Functions
# =============================================================================

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

# ==============================================================================
# 📝 Función: pause_continue
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Pausa la ejecución y espera que el usuario presione ENTER
#
# 🔧 Parámetros:
#   $1 - Mensaje personalizado (opcional)
# ==============================================================================
pause_continue() {
  local input_msg="${1:-}"

  if [ -n "$input_msg" ]; then
    local mensaje="🔹 $input_msg. Presiona [ENTER] para continuar..."
  else
    local mensaje="✅ Comando ejecutado. Presiona [ENTER] para continuar..."
  fi

  echo -en "${Gray}"
  read -p "$mensaje"
  echo -en "${Color_Off}"
}

# ==============================================================================
# 📝 Función: my_banner
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Muestra el banner del script
# ==============================================================================
my_banner(){
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
  echo ""
}

# ==============================================================================
# 📝 Función: log_to_file
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Registra mensajes en el archivo de log con timestamp
#
# 🔧 Parámetros:
#   $1 - Nivel del log (INFO | WARNING | ERROR | SUCCESS | DEBUG)
#   $2 - Mensaje a registrar
# ==============================================================================
log_to_file() {
  local level="${1:-INFO}"
  local message="$2"
  local timestamp=$(date -u -d "-5 hours" "+%Y-%m-%d %H:%M:%S" 2>/dev/null || TZ="America/Lima" date "+%Y-%m-%d %H:%M:%S" 2>/dev/null || date "+%Y-%m-%d %H:%M:%S")

  mkdir -p "$(dirname "$LOG_FILE")" 2>/dev/null || true
  echo "[${timestamp}] [${level}] ${message}" >> "$LOG_FILE" 2>/dev/null || true
}

# ==============================================================================
# 📝 Función: check_error (FUNCIÓN ORIGINAL MANTENIDA)
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Verifica el código de salida del último comando ejecutado y muestra un
#   mensaje de error personalizado si ocurrió una falla.
#
# 🔧 Parámetros:
#   $1 - Mensaje de error personalizado
#
# 💡 Uso:
#   comando || check_error "Falló el comando"
# ==============================================================================
check_error() {
  local exit_code=$?
  local error_message="${1:-Error desconocido}"

  if [ $exit_code -ne 0 ]; then
    msg "❌ Error: ${error_message}" "ERROR"
    log_to_file "ERROR" "${error_message} (código: ${exit_code})"
    exit $exit_code
  fi
}

# ==============================================================================
# 📝 Función: view_vars_config (FUNCIÓN ORIGINAL MANTENIDA)
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Muestra la configuración actual del script
# ==============================================================================
view_vars_config() {
  echo -e "${Gray}"
  echo -e "╔═══════════════════════════════════════════════════════════════╗"
  echo -e "║             🛠️  CONFIGURACIÓN ACTUAL 🛠️"
  echo -e "║"
  echo -e "║ 📅 DATE_HOUR:                ${DATE_HOUR}"
  echo -e "║ 👤 CURRENT_USER:             ${CURRENT_USER}"
  echo -e "║ 🖥️ CURRENT_PC_NAME:          ${CURRENT_PC_NAME}"
  echo -e "║ ℹ️ MY_INFO:                   ${MY_INFO}"
  echo -e "║ 📄 PATH_SCRIPT:              ${PATH_SCRIPT}"
  echo -e "║ 📜 SCRIPT_NAME:              ${SCRIPT_NAME}"
  echo -e "║ 📂 CURRENT_DIR:              ${CURRENT_DIR}"
  echo -e "║ 🗂️ NAME_DIR:                  ${NAME_DIR}"
  echo -e "║ 🗃️ TEMP_PATH_SCRIPT:         ${TEMP_PATH_SCRIPT}"
  echo -e "║ 🔐 DB_HOST:                  ${DB_HOST:-N/A}"
  echo -e "║ 🔌 DB_PORT:                  ${DB_PORT:-N/A}"
  echo -e "║ 👤 DB_USER:                  ${DB_USER:-N/A}"
  echo -e "║ 🔑 DB_PASSWORD:              ${DB_PASSWORD:+***oculta***}"
  echo -e "║ 💾 DB_NAME:                  ${DB_NAME:-N/A}"
  echo -e "║ 🌐 DOMAIN:                   ${DOMAIN:-N/A}"
  echo -e "║ 📁 RUTA_WEB:                 ${RUTA_WEB:-N/A}"
  echo -e "║ 📄 ARCHIVO_VHOST:            ${ARCHIVO_RUTA:-N/A}"
  echo -e "║ 📝 LOG_FILE:                 ${LOG_FILE}"
  echo -e "╚═══════════════════════════════════════════════════════════════╝"
  echo -e "${Color_Off}"
}

# ==============================================================================
# 📝 Función: validate_command
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Verifica si un comando está disponible en el sistema
# ==============================================================================
validate_command() {
  local command_name="$1"
  local install_hint="${2:-}"

  if [[ "$DEBUG_MODE" == true ]]; then
    msg "Verificando disponibilidad de: ${command_name}" "DEBUG"
  fi

  if ! command -v "$command_name" &> /dev/null; then
    msg "❌ Comando requerido no encontrado: ${command_name}" "ERROR"

    if [[ -n "$install_hint" ]]; then
      msg "💡 Sugerencia: ${install_hint}" "INFO"
    fi

    return 1
  fi

  if [[ "$DEBUG_MODE" == true ]]; then
    msg "✓ ${command_name} disponible" "DEBUG"
  fi

  return 0
}

# ==============================================================================
# 📝 Función: validate_file
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Valida que un archivo exista
# ==============================================================================
validate_file() {
  local file_path="$1"
  local is_required="${2:-true}"

  if [[ "$DEBUG_MODE" == true ]]; then
    msg "Verificando archivo: ${file_path}" "DEBUG"
  fi

  if [[ ! -f "$file_path" ]]; then
    if [[ "$is_required" == true ]]; then
      msg "❌ Archivo requerido no encontrado: ${file_path}" "ERROR"
      return 1
    else
      msg "⚠️  Archivo no encontrado: ${file_path}" "WARNING"
      return 1
    fi
  fi

  if [[ "$DEBUG_MODE" == true ]]; then
    msg "✓ Archivo existe: ${file_path}" "DEBUG"
  fi

  return 0
}

# ==============================================================================
# 📝 Función: validate_directory
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Valida que un directorio exista y sea accesible, opcionalmente lo crea
# ==============================================================================
validate_directory() {
  local dir_path="$1"
  local create_if_missing="${2:-false}"

  if [[ "$DEBUG_MODE" == true ]]; then
    msg "Verificando directorio: ${dir_path}" "DEBUG"
  fi

  if [[ ! -d "$dir_path" ]]; then
    if [[ "$create_if_missing" == true ]]; then
      msg "📁 Creando directorio: ${dir_path}" "INFO"
      mkdir -p "$dir_path"
      check_error "No se pudo crear el directorio: ${dir_path}"
      msg "✅ Directorio creado exitosamente" "SUCCESS"
    else
      msg "❌ Directorio no encontrado: ${dir_path}" "ERROR"
      return 1
    fi
  fi

  if [[ ! -r "$dir_path" ]]; then
    msg "❌ Sin permisos de lectura en: ${dir_path}" "ERROR"
    return 1
  fi

  return 0
}

# ==============================================================================
# 📝 Función: validate_domain
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Valida el formato de un nombre de dominio
# ==============================================================================
validate_domain() {
  local domain="$1"

  local domain_regex='^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$|^[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.local$'

  if [[ ! "$domain" =~ $domain_regex ]]; then
    msg "❌ Formato de dominio inválido: ${domain}" "ERROR"
    return 1
  fi

  if [[ "$DEBUG_MODE" == true ]]; then
    msg "✓ Dominio válido: ${domain}" "DEBUG"
  fi

  return 0
}

# ==============================================================================
# 📝 Función: show_help
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Muestra la ayuda del script
# ==============================================================================
show_help() {
  cat << EOF
${BBlue}
╔══════════════════════════════════════════════════════════════╗
║  🌐 Generador de VirtualHost para Laragon
╚══════════════════════════════════════════════════════════════╝
${Color_Off}

${BWhite}USO:${Color_Off}
    $SCRIPT_NAME [OPCIONES]

${BWhite}OPCIONES:${Color_Off}
    --debug         Activa el modo debug con información detallada
    --force         Sobrescribe archivos existentes sin confirmación
    --help, -h      Muestra esta ayuda

${BWhite}DESCRIPCIÓN:${Color_Off}
    Genera archivos de configuración VirtualHost para Apache en Laragon.
    Lee la configuración desde archivos .env y crea:

    • VirtualHost HTTP (puerto 80)
    • VirtualHost HTTPS (puerto 443) con SSL
    • Configuración de DocumentRoot
    • Configuración de ServerName y ServerAlias

${BWhite}EJEMPLOS:${Color_Off}
    # Ejecución normal
    ./$SCRIPT_NAME

    # Modo debug
    ./$SCRIPT_NAME --debug

    # Forzar sobrescritura
    ./$SCRIPT_NAME --force

${Gray}Desarrollado por: ${MY_INFO}${Color_Off}

EOF
}

# ==============================================================================
# 📝 Función: parse_args
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Procesa los argumentos de línea de comandos
# ==============================================================================
parse_args() {
  while [[ $# -gt 0 ]]; do
    case "$1" in
      --debug)
        DEBUG_MODE=true
        msg "🐛 Modo debug activado" "INFO"
        shift
        ;;
      --force)
        FORCE_MODE=true
        msg "⚡ Modo forzado activado" "INFO"
        shift
        ;;
      --help|-h)
        show_help
        exit 0
        ;;
      *)
        msg "❌ Opción desconocida: $1" "ERROR"
        echo "Usa --help para ver las opciones disponibles"
        exit 1
        ;;
    esac
  done
}

# ==============================================================================
# 📝 Función: validate_dependencies
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Valida que todas las dependencias necesarias estén instaladas
# ==============================================================================
validate_dependencies() {
  msg "╔══════════════════════════════════════════════════════════════╗" "INFO"
  msg "║  🔍 VALIDACIÓN DE DEPENDENCIAS" "INFO"
  msg "╚══════════════════════════════════════════════════════════════╝" "INFO"
  echo ""

  local all_ok=true

  # Verificar bash version
  if [[ "${BASH_VERSINFO[0]}" -ge 4 ]]; then
    msg "✅ Bash version: ${BASH_VERSION}" "SUCCESS"
    log_to_file "INFO" "Bash version: ${BASH_VERSION}"
  else
    msg "❌ Se requiere Bash 4.0+ (actual: ${BASH_VERSION})" "ERROR"
    all_ok=false
  fi

  # Verificar librería de lectura de .env.development
  local lib_env_path="${ROOT_PATH}/libs_shell/read_env.sh"
  if validate_file "$lib_env_path" true; then
    msg "✅ Librería read_env.sh encontrada" "SUCCESS"
    log_to_file "INFO" "Librería read_env.sh: ${lib_env_path}"
  else
    all_ok=false
  fi

  echo ""

  if [[ "$all_ok" == false ]]; then
    msg "❌ Faltan dependencias críticas" "ERROR"
    return 1
  fi

  msg "✅ Todas las dependencias satisfechas" "SUCCESS"
  return 0
}



#===============================================================================
# FUNCIÓN: path_to_laragon_format
#===============================================================================
# DESCRIPCIÓN:
#   Convierte una ruta de archivo a formato compatible con Laragon Virtual Hosts.
#   Normaliza diferentes formatos de ruta (Windows, Git Bash, WSL) a un formato
#   estándar con comillas dobles, listo para usar en configuraciones de Apache/Nginx.
#
#   Formato de salida: "D:/ruta/completa/al/proyecto"
#
# PARÁMETROS:
#   $1 - Ruta de entrada (cualquier formato soportado)
#
# FORMATOS DE ENTRADA SOPORTADOS:
#   1. Windows nativo:     D:\repos\proyecto\www
#   2. Git Bash/MobaXterm: /d/repos/proyecto/www
#   3. WSL:                /mnt/d/repos/proyecto/www
#
# RETORNA:
#   - Éxito: Imprime la ruta formateada con comillas dobles
#   - Error: Imprime mensaje de error a stderr y retorna 1
#
# EJEMPLOS DE USO:
#   path_to_laragon_format "D:\repos\mi_proyecto\www"
#   path_to_laragon_format "/d/repos/mi_proyecto/www"
#   path_to_laragon_format "/mnt/d/repos/mi_proyecto/www"
#
# SALIDA ESPERADA:
#   "D:/repos/mi_proyecto/www"
#
# CASOS DE USO TÍPICOS:
#   - Generar automáticamente DocumentRoot en virtual hosts
#   - Crear configuraciones de Apache/Nginx para Laragon
#   - Scripts de automatización de proyectos
#
# DEPENDENCIAS:
#   - bash
#   - sed (con soporte para expresiones regulares extendidas -r)
#
# AUTOR: [Tu nombre]
# VERSIÓN: 1.0
#===============================================================================

function path_to_laragon_format() {
    local PATH_INPUT="$1"
    local result

    # Validar que se proporcionó un argumento
    if [[ -z "$PATH_INPUT" ]]; then
        echo "Error: Debes proporcionar una ruta." >&2
        echo "Uso: path_to_laragon_format <ruta>" >&2
        return 1
    fi

    # Detectar el formato de entrada y normalizar
    if [[ "$PATH_INPUT" =~ ^[A-Za-z]:\\ ]]; then
        # Caso 1: Entrada Windows (ej: D:\repos\...)
        # - Convierte letra de unidad a mayúscula
        # - Reemplaza backslashes (\) por forward slashes (/)
        result=$(echo "$PATH_INPUT" | sed -r -e 's@^([A-Za-z]):@\U\1:@' -e 's@\\@/@g')

    elif [[ "$PATH_INPUT" =~ ^/[A-Za-z]/ ]]; then
        # Caso 2: Entrada Shell/Git Bash (ej: /d/repos/...)
        # - Convierte /d/ a D:/
        # - Mantiene forward slashes (/)
        result=$(echo "$PATH_INPUT" | sed -r -e 's@^/([a-zA-Z])@\U\1:@' -e 's@/@/@g')

    elif [[ "$PATH_INPUT" =~ ^/mnt/[A-Za-z]/ ]]; then
        # Caso 3: Entrada WSL (ej: /mnt/d/repos/...)
        # - Convierte /mnt/d/ a D:/
        # - Mantiene forward slashes (/)
        result=$(echo "$PATH_INPUT" | sed -r -e 's@^/mnt/([a-zA-Z])@\U\1:@' -e 's@/@/@g')

    else
        echo "Error: Formato de ruta no reconocido." >&2
        echo "Formatos soportados:" >&2
        echo "  - Windows: D:\\ruta\\archivo" >&2
        echo "  - Git Bash: /d/ruta/archivo" >&2
        echo "  - WSL: /mnt/d/ruta/archivo" >&2
        return 1
    fi

    # Añadir comillas dobles al resultado (requerido para configuraciones Apache/Nginx)
    echo "${result}"
}


# ==============================================================================
# 📝 Función: load_env_config
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Carga la configuración desde el archivo .env.development
# ==============================================================================
load_env_config() {
  msg "╔══════════════════════════════════════════════════════════════╗" "INFO"
  msg "║  📥 CARGA DE CONFIGURACIÓN" "INFO"
  msg "╚══════════════════════════════════════════════════════════════╝" "INFO"
  echo ""

  # Cargar librería de lectura de .env.development
  local lib_env_path="${ROOT_PATH}/libs_shell/read_env.sh"

  msg "📚 Cargando librería: ${lib_env_path}" "INFO"
  source "$lib_env_path"
  check_error "No se pudo cargar read_env.sh"

  # Definir ruta del archivo .env.development
  local path_env="${ROOT_PATH}/.env.woocomerce-api.local"

  msg "📄 Archivo .env: ${path_env}" "INFO"
  validate_file "$path_env" true
  check_error "Archivo .env no encontrado"

  # Cargar variables desde .env.development
  msg "📖 Leyendo variables de entorno..." "INFO"

  DB_HOST=$(find_env 'MYSQL_HOST_LOCAL' "$path_env")
  check_error "No se pudo leer MYSQL_HOST_LOCAL"

  DB_PORT=$(find_env 'MYSQL_PORT_LOCAL' "$path_env")
  check_error "No se pudo leer MYSQL_PORT_LOCAL"

  DB_USER=$(find_env 'MYSQL_USER_ROOT' "$path_env")
  check_error "No se pudo leer MYSQL_USER_ROOT"

  DB_PASSWORD=$(find_env 'MYSQL_ROOT_PASSWORD' "$path_env")
  check_error "No se pudo leer MYSQL_ROOT_PASSWORD"

  DB_NAME=$(find_env 'MYSQL_DATABASE' "$path_env")
  check_error "No se pudo leer MYSQL_DATABASE"

  DOMAIN=$(find_env 'APACHE_DOMAIN_0' "$path_env")
  check_error "No se pudo leer APACHE_DOMAIN_0"

  msg "✅ Variables cargadas correctamente" "SUCCESS"
  log_to_file "SUCCESS" "Variables de entorno cargadas"

  # Validar dominio
  validate_domain "$DOMAIN"
  check_error "Dominio inválido: ${DOMAIN}"

  echo ""
  return 0
}

# ==============================================================================
# 📝 Función: configure_paths
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Configura las rutas necesarias para el VirtualHost
# ==============================================================================
configure_paths() {
  msg "╔══════════════════════════════════════════════════════════════╗" "INFO"
  msg "║  📁 CONFIGURACIÓN DE RUTAS" "INFO"
  msg "╚══════════════════════════════════════════════════════════════╝" "INFO"
  echo ""

  # Definir archivo de configuración del VirtualHost
  ARCHIVO_RUTA="C:/laragon/etc/apache2/sites-enabled/${DOMAIN}.conf"
  msg "📄 Archivo VirtualHost: ${ARCHIVO_RUTA}" "INFO"
  log_to_file "INFO" "Archivo VirtualHost: ${ARCHIVO_RUTA}"

  # Definir ruta del proyecto
  ROOT_PATH_PROJECT=$(realpath -m "${CURRENT_DIR}/../..")
  msg "📂 Ruta del proyecto: ${ROOT_PATH_PROJECT}" "INFO"
  log_to_file "INFO" "Ruta del proyecto: ${ROOT_PATH_PROJECT}"

  # Definir ruta web (DocumentRoot)
  RUTA_WEB="${ROOT_PATH_PROJECT}/public"
  RUTA_WEB_LARAGON=$(path_to_laragon_format "${RUTA_WEB}")
  msg "🌐 DocumentRoot: ${RUTA_WEB_LARAGON}" "INFO"
  log_to_file "INFO" "DocumentRoot: ${RUTA_WEB_LARAGON}"

  # Validar que existe el directorio público
  if [[ ! -d "$RUTA_WEB" ]]; then
    msg "⚠️  El directorio público no existe: ${RUTA_WEB}" "WARNING"
    msg "📁 Creando directorio público..." "INFO"
    mkdir -p "$RUTA_WEB"
    check_error "No se pudo crear el directorio: ${RUTA_WEB}"
    msg "✅ Directorio público creado" "SUCCESS"
  else
    msg "✅ Directorio público existe" "SUCCESS"
  fi

  echo ""
  return 0
}

# ==============================================================================
# 📝 Función: generate_vhost_config
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Genera el contenido del archivo VirtualHost
# ==============================================================================
generate_vhost_config() {
  msg "╔══════════════════════════════════════════════════════════════╗" "INFO"
  msg "║  📝 GENERACIÓN DE CONFIGURACIÓN VIRTUALHOST" "INFO"
  msg "╚══════════════════════════════════════════════════════════════╝" "INFO"
  echo ""

  # Verificar si ya existe el archivo
  if [[ -f "$ARCHIVO_RUTA" ]] && [[ "$FORCE_MODE" == false ]]; then
    msg "⚠️  Ya existe un VirtualHost para: ${DOMAIN}" "WARNING"

    echo -e "${BYellow}"
    read -p "¿Desea sobrescribirlo? (s/n): " -r confirm
    echo -e "${Color_Off}"

    if [[ ! "$confirm" =~ ^[sS]$ ]]; then
      msg "ℹ️  Operación cancelada por el usuario" "INFO"
      return 0
    fi
  fi

  msg "📝 Generando contenido del VirtualHost..." "INFO"

  # Contenido del archivo VirtualHost
  CONTENIDO="define ROOT \"$RUTA_WEB_LARAGON\"
define SITE \"$DOMAIN\"

<VirtualHost *:80>
    DocumentRoot \"\${ROOT}\"
    ServerName \${SITE}
    ServerAlias *.\${SITE}
    <Directory \"\${ROOT}\">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:443>
    DocumentRoot \"\${ROOT}\"
    ServerName \${SITE}
    ServerAlias *.\${SITE}
    <Directory \"\${ROOT}\">
        AllowOverride All
        Require all granted
    </Directory>

    SSLEngine on
    SSLCertificateFile       C:/laragon/etc/ssl/\${SITE}/server.crt
    SSLCertificateKeyFile   C:/laragon/etc/ssl/\${SITE}/server.key
</VirtualHost>
"

  # Crear directorio si no existe
  local dir_vhost=$(dirname "$ARCHIVO_RUTA")
  validate_directory "$dir_vhost" true

  # Crear el archivo
  msg "💾 Escribiendo archivo: ${ARCHIVO_RUTA}" "INFO"
  echo "$CONTENIDO" > "$ARCHIVO_RUTA"
  check_error "No se pudo crear el archivo VirtualHost"

  msg "✅ Archivo VirtualHost creado exitosamente" "SUCCESS"
  log_to_file "SUCCESS" "VirtualHost creado: ${ARCHIVO_RUTA}"

  # Mostrar resumen
  echo ""
  msg "📊 RESUMEN DE CONFIGURACIÓN" "INFO"
  echo -e "${Cyan}"
  echo "  • Dominio:       ${DOMAIN}"
  echo "  • DocumentRoot:  ${RUTA_WEB}"
  echo "  • Archivo:       ${ARCHIVO_RUTA}"
  echo "  • HTTP:          http://${DOMAIN}"
  echo "  • HTTPS:         https://${DOMAIN}"
  echo -e "${Color_Off}"

  echo ""
  return 0
}

# ==============================================================================
# 📝 Función: show_next_steps
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Muestra los siguientes pasos para el usuario
# ==============================================================================
show_next_steps() {
  echo ""
  echo -e "${BCyan}┌─────────────────────────────────────────────┐${Color_Off}"
  echo -e "${BCyan}│ 📋 PRÓXIMOS PASOS                           │${Color_Off}"
  echo -e "${BCyan}└─────────────────────────────────────────────┘${Color_Off}"
  echo -e "${Cyan}"
  echo "  1. Asegúrate de tener el certificado SSL generado:"
  echo "     C:/laragon/etc/ssl/${DOMAIN}/server.crt"
  echo "     C:/laragon/etc/ssl/${DOMAIN}/server.key"
  echo ""
  echo "  2. Reinicia Apache en Laragon:"
  echo "     - Click derecho en Laragon"
  echo "     - Apache → Reload"
  echo ""
  echo "  3. Verifica que el dominio esté en tu archivo hosts:"
  echo "     C:/Windows/System32/drivers/etc/hosts"
  echo "     127.0.0.1    ${DOMAIN}"
  echo ""
  echo "  4. Accede a tu sitio:"
  echo "     http://${DOMAIN}"
  echo "     https://${DOMAIN}"
  echo -e "${Color_Off}"
}

# ==============================================================================
# 📝 Función: handle_error
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Captura cualquier error no manejado y muestra información detallada
# ==============================================================================
handle_error() {
  local exit_code=$1
  local line_number=$2

  msg "=================================================" "ERROR"
  msg "💥 ERROR CRÍTICO NO MANEJADO" "ERROR"
  msg "=================================================" "ERROR"
  msg "Código de salida: ${exit_code}" "ERROR"
  msg "Línea del error: ${line_number}" "ERROR"
  msg "Comando: ${BASH_COMMAND:-N/A}" "ERROR"
  msg "Script: ${PATH_SCRIPT}" "ERROR"
  msg "Función: ${FUNCNAME[1]:-main}" "ERROR"
  msg "Usuario: ${USER:-$(id -un 2>/dev/null || echo 'N/A')}" "ERROR"
  msg "Directorio: ${CURRENT_DIR:-N/A}" "ERROR"

  # Información adicional si está disponible
  if [[ -n "${DOMAIN:-}" ]]; then
    msg "Dominio: ${DOMAIN}" "ERROR"
  fi
  if [[ -n "${ARCHIVO_RUTA:-}" ]]; then
    msg "Archivo VirtualHost: ${ARCHIVO_RUTA}" "ERROR"
  fi

  msg "=================================================" "ERROR"

  log_to_file "ERROR" "Error crítico en línea ${line_number} con código ${exit_code}"

  exit "$exit_code"
}

# ==============================================================================
# 📝 Función: cleanup_on_exit
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Limpia archivos temporales al salir del script
# ==============================================================================
cleanup_on_exit() {
  local exit_code=$?

  # Limpiar archivos temporales si existen
  if [[ -n "${TEMP_PATH_SCRIPT:-}" ]] && [[ -f "${TEMP_PATH_SCRIPT}" ]]; then
    rm -f "${TEMP_PATH_SCRIPT}" 2>/dev/null || true
  fi

  if [[ -n "${TEMP_PATH_SCRIPT_SYSTEM:-}" ]] && [[ -f "${TEMP_PATH_SCRIPT_SYSTEM}" ]]; then
    rm -f "${TEMP_PATH_SCRIPT_SYSTEM}" 2>/dev/null || true
  fi
}

# Configurar traps para manejo de errores
trap 'handle_error $? $LINENO' ERR
trap 'cleanup_on_exit' EXIT

# =============================================================================
# 🔥 SECTION: Main Code
# =============================================================================

my_banner

# Cambiar al directorio del script
cd "${CURRENT_DIR}" || {
  msg "❌ Error: No se pudo cambiar al directorio: ${CURRENT_DIR}" "ERROR"
  exit 1
}

# Inicio del log
log_to_file "INFO" "========== INICIO DE EJECUCIÓN =========="
log_to_file "INFO" "Script: ${SCRIPT_NAME}"
log_to_file "INFO" "Usuario: ${MY_INFO}"
log_to_file "INFO" "Directorio: ${CURRENT_DIR}"

# Banner inicial
clear
msg "╔══════════════════════════════════════════════════════════════╗" "INFO"
msg "║  🌐 GENERADOR DE VIRTUALHOST PARA LARAGON" "INFO"
msg "╚══════════════════════════════════════════════════════════════╝" "INFO"
echo -e "${Gray}Iniciado: ${DATE_HOUR}${Color_Off}"
echo -e "${Gray}Usuario:  ${MY_INFO}${Color_Off}"
echo -e "${Gray}Log:      ${LOG_FILE}${Color_Off}"
echo ""

# Parsear argumentos
parse_args "$@"

# Validar dependencias
validate_dependencies || {
  msg "❌ Validación de dependencias falló" "ERROR"
  exit 1
}

# Cargar configuración desde .env.development
load_env_config || {
  msg "❌ Carga de configuración falló" "ERROR"
  exit 1
}

# Configurar rutas
configure_paths || {
  msg "❌ Configuración de rutas falló" "ERROR"
  exit 1
}

# Mostrar configuración
view_vars_config

# Pausa antes de continuar
pause_continue "Revisado la configuración"

# Generar VirtualHost
generate_vhost_config || {
  msg "❌ Generación de VirtualHost falló" "ERROR"
  exit 1
}

# Mostrar siguientes pasos
show_next_steps

# Finalización
msg "╔══════════════════════════════════════════════════════════════╗" "SUCCESS"
msg "║  ✅ PROCESO COMPLETADO EXITOSAMENTE" "SUCCESS"
msg "╚══════════════════════════════════════════════════════════════╝" "SUCCESS"
msg "🌐 VirtualHost creado para: ${DOMAIN}" "SUCCESS"
msg "📝 Revisa el log en: ${LOG_FILE}" "INFO"

echo -e "${Gray} Terminado de procesar...${Color_Off}"
sleep 2

log_to_file "INFO" "========== FIN DE EJECUCIÓN =========="