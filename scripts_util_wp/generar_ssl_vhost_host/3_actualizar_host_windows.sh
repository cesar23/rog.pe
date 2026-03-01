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

# Variables de archivo hosts
HOSTS_FILE="/c/Windows/System32/drivers/etc/hosts"
BACKUP_SUFFIX=".backup_${DATE_SIMPLE}"

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
  echo -e "║ 📁 HOSTS_FILE:               ${HOSTS_FILE}"
  echo -e "║ 📝 LOG_FILE:                 ${LOG_FILE}"
  echo -e "╚═══════════════════════════════════════════════════════════════╝"
  echo -e "${Color_Off}"
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
║  📝 Actualizador de Archivo Hosts de Windows
╚══════════════════════════════════════════════════════════════╝
${Color_Off}

${BWhite}USO:${Color_Off}
    $SCRIPT_NAME [OPCIONES]

${BWhite}OPCIONES:${Color_Off}
    --debug         Activa el modo debug con información detallada
    --force         Fuerza la actualización sin confirmación
    --help, -h      Muestra esta ayuda

${BWhite}DESCRIPCIÓN:${Color_Off}
    Actualiza el archivo hosts de Windows (C:/Windows/System32/drivers/etc/hosts)
    agregando entradas para dominios locales.

    Lee la configuración desde archivos .env y agrega:
    • Entrada IPv4: 127.0.0.1 <dominio>
    • Entrada IPv6: ::1 <dominio>

    Crea respaldo automático antes de modificar.

${BWhite}REQUISITOS:${Color_Off}
    • Ejecutar como Administrador
    • Git Bash o terminal compatible
    • Archivo .env configurado

${BWhite}EJEMPLOS:${Color_Off}
    # Ejecución normal
    ./$SCRIPT_NAME

    # Modo debug
    ./$SCRIPT_NAME --debug

    # Forzar actualización
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

  # Verificar archivo hosts
  if validate_file "$HOSTS_FILE" true; then
    msg "✅ Archivo hosts encontrado: ${HOSTS_FILE}" "SUCCESS"
    log_to_file "INFO" "Archivo hosts: ${HOSTS_FILE}"
  else
    msg "❌ Archivo hosts no encontrado" "ERROR"
    all_ok=false
  fi

  # Verificar permisos de escritura en hosts
  if [[ -w "$HOSTS_FILE" ]]; then
    msg "✅ Permisos de escritura en archivo hosts" "SUCCESS"
  else
    msg "⚠️  Sin permisos de escritura en hosts (requiere administrador)" "WARNING"
  fi

  echo ""

  if [[ "$all_ok" == false ]]; then
    msg "❌ Faltan dependencias críticas" "ERROR"
    return 1
  fi

  msg "✅ Todas las dependencias satisfechas" "SUCCESS"
  return 0
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
# 📝 Función: backup_hosts_file
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Crea un respaldo del archivo hosts antes de modificarlo
# ==============================================================================
backup_hosts_file() {
  local backup_file="${HOSTS_FILE}${BACKUP_SUFFIX}"

  msg "💾 Creando respaldo del archivo hosts..." "INFO"
  msg "   Backup: ${backup_file}" "INFO"

  cp "$HOSTS_FILE" "$backup_file"
  check_error "No se pudo crear el respaldo del archivo hosts"

  msg "✅ Respaldo creado exitosamente" "SUCCESS"
  log_to_file "SUCCESS" "Respaldo creado: ${backup_file}"

  return 0
}

# ==============================================================================
# 📝 Función: check_domain_exists
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Verifica si el dominio ya existe en el archivo hosts
# ==============================================================================
check_domain_exists() {
  local domain="$1"

  if grep -q "$domain" "$HOSTS_FILE"; then
    return 0  # Existe
  else
    return 1  # No existe
  fi
}

# ==============================================================================
# 📝 Función: add_domain_to_hosts
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Agrega el dominio al archivo hosts
# ==============================================================================
add_domain_to_hosts() {
  msg "╔══════════════════════════════════════════════════════════════╗" "INFO"
  msg "║  📝 ACTUALIZACIÓN DEL ARCHIVO HOSTS" "INFO"
  msg "╚══════════════════════════════════════════════════════════════╝" "INFO"
  echo ""

  local domain="$DOMAIN"

  # Verificar si el dominio ya existe
  if check_domain_exists "$domain"; then
    msg "ℹ️  El dominio '${domain}' ya existe en el archivo hosts" "INFO"
    log_to_file "INFO" "Dominio ya existe: ${domain}"

    if [[ "$FORCE_MODE" == false ]]; then
      echo ""
      echo -e "${BYellow}"
      read -p "¿Desea actualizar la entrada existente? (s/n): " -r confirm
      echo -e "${Color_Off}"

      if [[ ! "$confirm" =~ ^[sS]$ ]]; then
        msg "ℹ️  Operación cancelada por el usuario" "INFO"
        return 0
      fi

      # Remover entradas existentes del dominio
      msg "🗑️  Removiendo entradas existentes del dominio..." "INFO"
      sed -i.tmp "/$domain/d" "$HOSTS_FILE"
      check_error "No se pudo remover entradas existentes"
    else
      msg "⚡ Modo forzado: removiendo entradas existentes..." "INFO"
      sed -i.tmp "/$domain/d" "$HOSTS_FILE"
      check_error "No se pudo remover entradas existentes"
    fi
  fi

  # Crear respaldo antes de modificar
  backup_hosts_file

  msg "📝 Agregando dominio al archivo hosts..." "INFO"

  # Verificar si existe la sección "Host virtuales"
  if grep -q "# --------- Host virtuales" "$HOSTS_FILE"; then
    msg "✓ Sección 'Host virtuales' encontrada" "DEBUG"

    # Agregar después de la sección
    awk -v ipv4="127.0.0.1       $domain" -v ipv6="::1             $domain" '
    BEGIN {inserted = 0}
    /# --------- Host virtuales/ {
        print
        if (!inserted) {
            print ipv4
            print ipv6
            inserted = 1
        }
        next
    }
    {print}
    ' "$HOSTS_FILE" > "${HOSTS_FILE}.new" && mv "${HOSTS_FILE}.new" "$HOSTS_FILE"
    check_error "No se pudo agregar el dominio al archivo hosts"

  else
    msg "⚠️  Sección 'Host virtuales' no encontrada" "WARNING"
    msg "📝 Agregando sección y dominio..." "INFO"

    # Agregar sección y dominio al final del archivo
    {
      echo ""
      echo "# --------- Host virtuales"
      echo "127.0.0.1       $domain"
      echo "::1             $domain"
    } >> "$HOSTS_FILE"
    check_error "No se pudo agregar el dominio al archivo hosts"
  fi

  msg "✅ Dominio agregado correctamente al archivo hosts" "SUCCESS"
  log_to_file "SUCCESS" "Dominio agregado: ${domain}"

  # Mostrar las líneas agregadas
  echo ""
  msg "📋 Entradas agregadas:" "INFO"
  echo -e "${Green}"
  echo "  127.0.0.1       $domain"
  echo "  ::1             $domain"
  echo -e "${Color_Off}"

  echo ""
  return 0
}

# ==============================================================================
# 📝 Función: verify_hosts_entry
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Verifica que el dominio se haya agregado correctamente
# ==============================================================================
verify_hosts_entry() {
  msg "╔══════════════════════════════════════════════════════════════╗" "INFO"
  msg "║  🔍 VERIFICACIÓN FINAL" "INFO"
  msg "╚══════════════════════════════════════════════════════════════╝" "INFO"
  echo ""

  if check_domain_exists "$DOMAIN"; then
    msg "✅ Verificación exitosa: dominio encontrado en hosts" "SUCCESS"
    log_to_file "SUCCESS" "Verificación exitosa"

    # Mostrar las líneas del dominio
    msg "📄 Entradas actuales del dominio:" "INFO"
    echo -e "${Cyan}"
    grep "$DOMAIN" "$HOSTS_FILE" | sed 's/^/  /'
    echo -e "${Color_Off}"

    return 0
  else
    msg "❌ Verificación falló: dominio no encontrado en hosts" "ERROR"
    log_to_file "ERROR" "Verificación falló"
    return 1
  fi
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
  echo "  1. Verifica que Apache/Laragon esté ejecutándose"
  echo ""
  echo "  2. Limpia la caché DNS de Windows:"
  echo "     ipconfig /flushdns"
  echo ""
  echo "  3. Prueba el dominio en tu navegador:"
  echo "     http://${DOMAIN}"
  echo "     https://${DOMAIN}"
  echo ""
  echo "  4. Si hay problemas, verifica el archivo hosts:"
  echo "     notepad C:\\Windows\\System32\\drivers\\etc\\hosts"
  echo ""
  echo "  5. Respaldo creado en:"
  echo "     ${HOSTS_FILE}${BACKUP_SUFFIX}"
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

  if [[ -n "${DOMAIN:-}" ]]; then
    msg "Dominio: ${DOMAIN}" "ERROR"
  fi
  if [[ -n "${HOSTS_FILE:-}" ]]; then
    msg "Archivo hosts: ${HOSTS_FILE}" "ERROR"
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

  # Limpiar archivos .tmp del sed
  rm -f "${HOSTS_FILE}.tmp" 2>/dev/null || true
  rm -f "${HOSTS_FILE}.new" 2>/dev/null || true
}

# Configurar traps para manejo de errores
trap 'handle_error $? $LINENO' ERR
trap 'cleanup_on_exit' EXIT

# =============================================================================
# 🔥 SECTION: Main Code
# =============================================================================


  if ! net session > /dev/null 2>&1; then
      msg "╔══════════════════════════════════════════════════════════════╗" "INFO"
      msg "║  🔐 Reiniciando el script con permisos de administrador..." "INFO"
      msg "╚══════════════════════════════════════════════════════════════╝" "INFO"
       sleep 2
      powershell -Command "Start-Process 'C:\\Program Files\\Git\\git-bash.exe' -ArgumentList '\"$0\"' -Verb RunAs"
      exit 0
  fi

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
msg "║  📝 ACTUALIZADOR DE ARCHIVO HOSTS DE WINDOWS" "INFO"
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
  pause_continue "Presiona ENTER para salir"
  exit 1
}

# Cargar configuración desde .env.development
load_env_config || {
  msg "❌ Carga de configuración falló" "ERROR"
  pause_continue "Presiona ENTER para salir"
  exit 1
}

# Mostrar configuración
view_vars_config

# Pausa antes de continuar
pause_continue "Revisado la configuración"

# Agregar dominio al archivo hosts
add_domain_to_hosts || {
  msg "❌ Actualización del archivo hosts falló" "ERROR"
  pause_continue "Presiona ENTER para salir"
  exit 1
}

# Verificar que se agregó correctamente
verify_hosts_entry || {
  msg "⚠️  La verificación falló" "WARNING"
}

# Mostrar siguientes pasos
show_next_steps

# Finalización
msg "╔══════════════════════════════════════════════════════════════╗" "SUCCESS"
msg "║  ✅ PROCESO COMPLETADO EXITOSAMENTE" "SUCCESS"
msg "╚══════════════════════════════════════════════════════════════╝" "SUCCESS"
msg "📝 Archivo hosts actualizado para: ${DOMAIN}" "SUCCESS"
msg "💾 Respaldo disponible en: ${HOSTS_FILE}${BACKUP_SUFFIX}" "INFO"
msg "📝 Revisa el log en: ${LOG_FILE}" "INFO"

echo ""
pause_continue "Se terminó. Presiona ENTER para salir"

log_to_file "INFO" "========== FIN DE EJECUCIÓN =========="