#!/usr/bin/env bash

set -euo pipefail  # Detener script al primer error, variables no definidas y errores en pipes

# =============================================================================
# 🏆 SECTION: Configuración Inicial
# =============================================================================

# Configurar locale de forma robusta
if locale -a 2>/dev/null | grep -qi "en_US.utf8\|en_US.UTF-8"; then
    export LC_ALL="${LC_ALL:-en_US.UTF-8}"
elif locale -a 2>/dev/null | grep -qi "C.UTF-8\|C.utf8"; then
    export LC_ALL="${LC_ALL:-C.UTF-8}"
else
    export LC_ALL="${LC_ALL:-C}"
fi

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

# Variables de certificado
CERT_VALIDITY_DAYS=365
CERT_KEY_SIZE=2048

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

pause_continue() {
  # Usa ${1:-} para evitar error con 'set -u' si no hay argumento
  local input_msg="${1:-}"

  # Determina el mensaje a mostrar según si se recibe argumento
  if [ -n "$input_msg" ]; then
    local mensaje="🔹 $input_msg. Presiona [ENTER] para continuar..."
  else
    local mensaje="✅ Comando ejecutado. Presiona [ENTER] para continuar..."
  fi

  # Muestra el mensaje en gris y espera la entrada del usuario
  echo -en "${Gray}"
  read -p "$mensaje"
  echo -en "${Color_Off}"
}


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


  # Crear directorio de logs si no existe
  mkdir -p "$(dirname "$LOG_FILE")" 2>/dev/null || true

  # Escribir en archivo de log
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
  echo -e "║ 📝 LOG_FILE:                 ${LOG_FILE}"
  echo -e "║ 🔐 CERT_VALIDITY_DAYS:       ${CERT_VALIDITY_DAYS}"
  echo -e "║ 🔑 CERT_KEY_SIZE:            ${CERT_KEY_SIZE}"
  echo -e "╚═══════════════════════════════════════════════════════════════╝"
  echo -e "${Color_Off}"
}

# ==============================================================================
# 📝 Función: validate_command
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Verifica si un comando está disponible en el sistema
#
# 🔧 Parámetros:
#   $1 - Nombre del comando a verificar
#   $2 - Mensaje de ayuda de instalación (opcional)
#
# 💡 Retorna:
#   0: Comando disponible
#   1: Comando no encontrado
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
#
# 🔧 Parámetros:
#   $1 - Ruta del archivo a validar
#   $2 - Es requerido (true/false) [opcional, por defecto: true]
#
# 💡 Retorna:
#   0: Archivo válido
#   1: Archivo no existe
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
#
# 🔧 Parámetros:
#   $1 - Ruta del directorio a validar
#   $2 - Crear si no existe (true/false) [opcional, por defecto: false]
#
# 💡 Retorna:
#   0: Directorio válido
#   1: Directorio inválido
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
#
# 🔧 Parámetros:
#   $1 - Dominio a validar
#
# 💡 Retorna:
#   0: Dominio válido
#   1: Dominio inválido
# ==============================================================================
validate_domain() {
  local domain="$1"

  # Patrón regex para validar nombres de dominio
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
║  🔐 Generador de Certificados SSL/TLS para Desarrollo
╚══════════════════════════════════════════════════════════════╝
${Color_Off}

${BWhite}USO:${Color_Off}
    $SCRIPT_NAME [OPCIONES]

${BWhite}OPCIONES:${Color_Off}
    --debug         Activa el modo debug con información detallada
    --force         Sobrescribe certificados existentes sin confirmación
    --help, -h      Muestra esta ayuda

${BWhite}DESCRIPCIÓN:${Color_Off}
    Genera certificados SSL autofirmados para desarrollo local.
    Lee la configuración desde archivos .env y crea:

    • Archivo de configuración OpenSSL (cert.conf)
    • Certificado SSL (.crt)
    • Clave privada (.key)

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
#
# 🔧 Parámetros:
#   $@ - Todos los argumentos pasados al script
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
#
# 💡 Retorna:
#   0: Todas las dependencias satisfechas
#   1: Faltan dependencias
# ==============================================================================
validate_dependencies() {
  msg "╔══════════════════════════════════════════════════════════════╗" "INFO"
  msg "║  🔍 VALIDACIÓN DE DEPENDENCIAS" "INFO"
  msg "╚══════════════════════════════════════════════════════════════╝" "INFO"
  echo ""

  local all_ok=true

  # Verificar OpenSSL
  if validate_command "openssl" "Instalar: apt install openssl (Linux) / choco install openssl (Windows)"; then
    local openssl_version
    openssl_version=$(openssl version 2>/dev/null | awk '{print $2}')
    msg "✅ OpenSSL instalado: ${openssl_version}" "SUCCESS"
    log_to_file "INFO" "OpenSSL version: ${openssl_version}"
  else
    all_ok=false
  fi

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

# ==============================================================================
# 📝 Función: load_env_config
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Carga la configuración desde el archivo .env.development
#
# 💡 Retorna:
#   0: Configuración cargada exitosamente
#   1: Error al cargar configuración
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
  local path_env="${ROOT_PATH}/.env.rog.local"

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
# 📝 Función: create_cert_config
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Genera el archivo de configuración de OpenSSL
#
# 🔧 Parámetros:
#   $1 - Ruta del archivo de configuración
#   $2 - Dominio para el certificado
# ==============================================================================
create_cert_config() {
  local config_path="$1"
  local domain="$2"

  msg "📝 Generando archivo de configuración: ${config_path}" "INFO"

  cat > "$config_path" <<EOF
[ req ]

default_bits        = ${CERT_KEY_SIZE}
default_keyfile     = server-key.pem
distinguished_name  = subject
req_extensions      = req_ext
x509_extensions     = x509_ext
string_mask         = utf8only

[ subject ]

countryName                 = Country Name (2 letter code)
countryName_default         = PE

stateOrProvinceName         = State or Province Name (full name)
stateOrProvinceName_default = Lima

localityName                = Locality Name (eg, city)
localityName_default        = Lima

organizationName            = Organization Name (eg, company)
organizationName_default    = Soluciones System, PE

commonName                  = Common Name (e.g. server FQDN or YOUR name)
commonName_default          = ${domain}

emailAddress                = Email Address
emailAddress_default        = admin@${domain}

[ dn ]
C  = PE
ST = Lima
L  = Lima
O  = Soluciones System
OU = Development Team
CN = ${domain}

[ x509_ext ]

subjectKeyIdentifier   = hash
authorityKeyIdentifier = keyid,issuer

basicConstraints       = CA:FALSE
keyUsage               = digitalSignature, keyEncipherment
subjectAltName         = @alternate_names
nsComment              = "OpenSSL Generated Certificate for Development"

[ req_ext ]

subjectKeyIdentifier = hash

basicConstraints     = CA:FALSE
keyUsage             = digitalSignature, keyEncipherment
subjectAltName       = @alternate_names
nsComment            = "OpenSSL Generated Certificate for Development"

[ alternate_names ]

DNS.1 = ${domain}
DNS.2 = www.${domain}
DNS.3 = *.${domain}

EOF

  check_error "No se pudo crear el archivo de configuración"
  msg "✅ Archivo de configuración creado exitosamente" "SUCCESS"
  log_to_file "SUCCESS" "Archivo de configuración creado: ${config_path}"
}

# ==============================================================================
# 📝 Función: generate_certificate
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Genera el certificado SSL y la clave privada
#
# 💡 Retorna:
#   0: Certificado generado exitosamente
#   1: Error en la generación
# ==============================================================================
generate_certificate() {
  msg "╔══════════════════════════════════════════════════════════════╗" "INFO"
  msg "║  🔐 GENERACIÓN DE CERTIFICADO SSL" "INFO"
  msg "╚══════════════════════════════════════════════════════════════╝" "INFO"
  echo ""

  local config_path="${CURRENT_DIR}/cert.conf"
  local output_dir="${CURRENT_DIR}/${DOMAIN}"
  local cert_file="${output_dir}/server.crt"
  local key_file="${output_dir}/server.key"

  # Verificar si ya existen certificados
  if [[ -f "$cert_file" ]] && [[ "$FORCE_MODE" == false ]]; then
    msg "⚠️  Ya existe un certificado para: ${DOMAIN}" "WARNING"

    echo -e "${BYellow}"
    read -p "¿Desea sobrescribirlo? (s/n): " -r confirm
    echo -e "${Color_Off}"

    if [[ ! "$confirm" =~ ^[sS]$ ]]; then
      msg "ℹ️  Operación cancelada por el usuario" "INFO"
      return 0
    fi
  fi

  # Crear directorio de salida
  validate_directory "$output_dir" true

  # Generar archivo de configuración
  create_cert_config "$config_path" "$DOMAIN"

  # Generar certificado
  msg "🔐 Generando certificado SSL..." "INFO"
  msg "   • Algoritmo: RSA ${CERT_KEY_SIZE} bits" "INFO"
  msg "   • Validez: ${CERT_VALIDITY_DAYS} días" "INFO"
  msg "   • Dominio: ${DOMAIN}" "INFO"
  echo ""

  openssl.exe req -config "${config_path}" \
                  -new \
                  -sha256 \
                  -newkey "rsa:${CERT_KEY_SIZE}" \
                  -nodes \
                  -keyout "${key_file}" \
                  -x509 \
                  -days "${CERT_VALIDITY_DAYS}" \
                  -out "${cert_file}" \
                  -batch 2>> "$LOG_FILE"

  check_error "Falló la generación del certificado"

  msg "✅ Certificado generado exitosamente" "SUCCESS"
  msg "   📄 Certificado: ${cert_file}" "SUCCESS"
  msg "   🔑 Clave:       ${key_file}" "SUCCESS"

  log_to_file "SUCCESS" "Certificado generado: ${cert_file}"
  log_to_file "SUCCESS" "Clave generada: ${key_file}"

  # Verificar certificado
  msg "" "INFO"
  msg "🔍 Verificando certificado generado..." "INFO"
  openssl x509 -text -noout -in "${cert_file}" | head -20

  echo ""
  return 0
}

# ==============================================================================
# 📝 Función: install_to_laragon
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Instala el certificado generado en Laragon
#
# 💡 Retorna:
#   0: Instalación exitosa
#   1: Error en la instalación
# ==============================================================================
install_to_laragon() {
  msg "╔══════════════════════════════════════════════════════════════╗" "INFO"
  msg "║  💾 INSTALACIÓN EN LARAGON" "INFO"
  msg "╚══════════════════════════════════════════════════════════════╝" "INFO"
  echo ""

  local source_dir="${CURRENT_DIR}/${DOMAIN}"
  local laragon_ssl_path="/C/laragon/etc/ssl/${DOMAIN}"

  # Verificar directorio fuente
  validate_directory "$source_dir" false
  check_error "No se encontró el directorio con certificados"

  # Solicitar confirmación
  if [[ "$FORCE_MODE" == false ]]; then
    echo -e "${BBlue}┌─────────────────────────────────────────────┐${Color_Off}"
    echo -e "${BBlue}│ ¿Instalar certificado en Laragon?          │${Color_Off}"
    echo -e "${BBlue}└─────────────────────────────────────────────┘${Color_Off}"
    echo -e "  ${Gray}Origen:  ${source_dir}${Color_Off}"
    echo -e "  ${Gray}Destino: ${laragon_ssl_path}${Color_Off}"
    echo ""

    echo -e "${Blue}"
    read -p "¿Desea guardar CERTIFICADO en: ${laragon_ssl_path}? (s/n): " confirmacion
    echo -e "${Color_Off}"

    if [[ ! "$confirmacion" =~ ^[sS]$ ]]; then
      msg "ℹ️  Saliendo del script..." "INFO"
      exit 0
    fi
  fi

  # Crear directorio destino
  msg "📁 Preparando directorio de destino..." "INFO"
  mkdir -p "$laragon_ssl_path" 2>/dev/null || true

  # Copiar certificados
  msg "📋 Copiando certificados..." "INFO"
  echo -e "${Gray}"
  echo "  Carpeta copiada: ${source_dir}"
  echo "  Destino: ${laragon_ssl_path}"
  echo -e "${Color_Off}"

  cp -r "${source_dir}"/* "${laragon_ssl_path}/"
  check_error "No se pudo guardar en: ${laragon_ssl_path}"

  msg "✅ Certificados instalados en Laragon" "SUCCESS"
  log_to_file "SUCCESS" "Certificados copiados a: ${laragon_ssl_path}"

  # Instrucciones
  echo ""
  echo -e "${BCyan}┌─────────────────────────────────────────────┐${Color_Off}"
  echo -e "${BCyan}│ 📋 PRÓXIMOS PASOS                           │${Color_Off}"
  echo -e "${BCyan}└─────────────────────────────────────────────┘${Color_Off}"
  echo -e "${Cyan}"
  echo "  1. Abre Laragon"
  echo "  2. Click derecho → Apache → SSL → Enabled"
  echo "  3. Reinicia Apache"
  echo "  4. Accede a: https://${DOMAIN}"
  echo -e "${Color_Off}"

  return 0
}

# ==============================================================================
# 📝 Función: handle_error
# ------------------------------------------------------------------------------
# ✅ Descripción:
#   Captura cualquier error no manejado y muestra información detallada
#
# 🔧 Parámetros:
#   $1 - Código de salida del comando que falló
#   $2 - Número de línea donde ocurrió el error
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
  if [[ -n "${config_path:-}" ]]; then
    msg "Config path: ${config_path}" "ERROR"
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

  # Si el script falló y hay archivos de configuración temporales, limpiarlos
  if [[ $exit_code -ne 0 ]]; then
    if [[ -n "${config_path:-}" ]] && [[ -f "${config_path}" ]]; then
      msg "⚠️  Limpiando archivo de configuración temporal..." "WARNING"
      # Solo limpiar si queremos, por ahora lo dejamos para debugging
    fi
  fi
}

# Configurar traps para manejo de errores
# Captura cualquier error no manejado y lo procesa
trap 'handle_error $? $LINENO' ERR

# Capturar EXIT para limpiar archivos temporales
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
msg "║  🔐 GENERADOR DE CERTIFICADOS SSL/TLS" "INFO"
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

# Mostrar configuración
view_vars_config
# pausar para confirmar
pause_continue

# Generar certificado
generate_certificate || {
  msg "❌ Generación de certificado falló" "ERROR"
  exit 1
}

# Instalar en Laragon
install_to_laragon || {
  msg "⚠️  Instalación en Laragon falló o fue cancelada" "WARNING"
}

# Finalización
msg "╔══════════════════════════════════════════════════════════════╗" "SUCCESS"
msg "║  ✅ PROCESO COMPLETADO EXITOSAMENTE" "SUCCESS"
msg "╚══════════════════════════════════════════════════════════════╝" "SUCCESS"
msg "🔐 Certificado SSL generado para: ${DOMAIN}" "SUCCESS"
msg "📝 Revisa el log en: ${LOG_FILE}" "INFO"

echo -e "${Gray} Terminado de procesar...${Color_Off}"
sleep 2

log_to_file "INFO" "========== FIN DE EJECUCIÓN =========="