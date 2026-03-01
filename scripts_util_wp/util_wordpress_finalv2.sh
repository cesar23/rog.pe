 #!/usr/bin/env bash

# =============================================================================
# Script: util_wordpress_final.sh
# Descripción: Utilidades profesionales para administración de WordPress
# Ubicación por defecto: /home/navdyelstore.it/scripts
# Versión: 3.2.0
# =============================================================================

set -euo pipefail

# Configurar locale UTF-8 de forma segura
export LC_ALL="C.UTF-8" 2>/dev/null || export LC_ALL="C"
# =============================================================================
# SECTION: Configuración Inicial
# =============================================================================


DATE_HOUR=$(date -u -d "-5 hours" "+%Y-%m-%d_%H:%M:%S" 2>/dev/null || TZ="America/Lima" date "+%Y-%m-%d_%H:%M:%S" 2>/dev/null || date "+%Y-%m-%d_%H:%M:%S")
CURRENT_USER=$(id -un)
CURRENT_USER_HOME="${HOME:-$USERPROFILE}"
CURRENT_PC_NAME=$(hostname)
MY_INFO="${CURRENT_USER}@${CURRENT_PC_NAME}"
PATH_SCRIPT=$(readlink -f "${BASH_SOURCE:-$0}" 2>/dev/null || realpath "${BASH_SOURCE:-$0}" 2>/dev/null || echo "$0")
SCRIPT_NAME=$(basename "$PATH_SCRIPT")
CURRENT_DIR=$(dirname "$PATH_SCRIPT")
NAME_DIR=$(basename "$CURRENT_DIR")
TEMP_PATH_SCRIPT=$(echo "$PATH_SCRIPT" | sed 's/.sh/.tmp/g')
TEMP_PATH_SCRIPT_SYSTEM=$(echo "${TMP:-/tmp}/${SCRIPT_NAME}" | sed 's/.sh/.tmp/g')
ROOT_PATH=$(realpath -m "${CURRENT_DIR}/..")

# Variables de control
DEBUG_MODE=false
DATE_SIMPLE="$(date +%Y%m%d_%H%M%S)"
LOG_FILE="${CURRENT_DIR}/logs/${SCRIPT_NAME%.sh}_${DATE_SIMPLE}.log"

# =============================================================================
# SECTION: Colores
# =============================================================================

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
# SECTION: Funciones Core de Mensajería
# =============================================================================

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

# =============================================================================
# SECTION: Funciones de Validación
# =============================================================================

validate_directory() {
  local dir="$1"
  local dir_name="${2:-directorio}"

  if [[ ! -d "$dir" ]]; then
    msg "Error: El ${dir_name} no existe: $dir" "ERROR"
    return 1
  fi

  if [[ ! -r "$dir" ]]; then
    msg "Error: Sin permisos de lectura en ${dir_name}: $dir" "ERROR"
    return 1
  fi

  return 0
}

validate_file() {
  local file="$1"
  local file_desc="${2:-archivo}"

  if [[ ! -f "$file" ]]; then
    msg "Error: El ${file_desc} no existe: $file" "ERROR"
    return 1
  fi

  if [[ ! -r "$file" ]]; then
    msg "Error: Sin permisos de lectura en ${file_desc}: $file" "ERROR"
    return 1
  fi

  return 0
}

check_paths() {
  local error=0

  for ruta in "$@"; do
    local ruta_unix=$(echo "$ruta" | sed 's/\\/\//g' | sed 's/^\([A-Za-z]\):/\/mnt\/\L\1/')

    if [[ -d "$ruta_unix" ]] || [[ -d "$ruta" ]]; then
      continue
    elif [[ -f "$ruta_unix" ]] || [[ -f "$ruta" ]]; then
      continue
    else
      local nombre=$(basename "$ruta")
      if [[ "$nombre" == *.* ]]; then
        msg "Error: El archivo '$ruta' no existe" "ERROR"
      else
        msg "Error: El directorio '$ruta' no existe" "ERROR"
      fi
      error=1
    fi
  done

  return $error
}

validate_command() {
  local cmd="$1"
  local cmd_name="${2:-$cmd}"

  if ! command -v "$cmd" >/dev/null 2>&1; then
    msg "Error: El comando '$cmd_name' no está disponible" "ERROR"
    return 1
  fi

  return 0
}

# =============================================================================
# SECTION: Manejador Global de Errores
# =============================================================================

handle_error() {
  local exit_code=$1
  local line_number=$2

  msg "=================================================" "ERROR"
  msg "ERROR CRÍTICO NO MANEJADO" "ERROR"
  msg "=================================================" "ERROR"
  msg "Código de salida: ${exit_code}" "ERROR"
  msg "Línea del error: ${line_number}" "ERROR"
  msg "Comando: ${BASH_COMMAND:-N/A}" "ERROR"
  msg "Script: ${PATH_SCRIPT}" "ERROR"
  msg "Función: ${FUNCNAME[1]:-main}" "ERROR"
  msg "Usuario: ${USER:-$(id -un 2>/dev/null || echo 'N/A')}" "ERROR"
  msg "Directorio: ${CURRENT_DIR:-N/A}" "ERROR"

  if [[ -n "${PATH_DOMAIN:-}" ]]; then
    msg "Directorio WordPress: ${PATH_DOMAIN}" "ERROR"
  fi

  if [[ -n "${DB_NAME:-}" ]]; then
    msg "Base de datos: ${DB_NAME}" "ERROR"
  fi

  msg "=================================================" "ERROR"

  cleanup_temp_files
    # ✅ AGREGAR ESTA LÍNEA:
    log_to_file "ERROR" "Error crítico en línea ${line_number} con código ${exit_code}: ${BASH_COMMAND:-N/A}"


  exit "${exit_code}"
}

cleanup_temp_files() {
  local temp_files=(
    "${TEMP_PATH_SCRIPT:-}"
    "${TEMP_PATH_SCRIPT_SYSTEM:-}"
    "${PATH_CONFIG_MYSQL:-}"
  )

  for temp_file in "${temp_files[@]}"; do
    if [[ -n "$temp_file" ]] && [[ -f "$temp_file" ]]; then
      rm -f "$temp_file" 2>/dev/null || true
    fi
  done
}

cleanup_on_exit() {
  local exit_code=$?

  cleanup_temp_files

  if [[ $exit_code -ne 0 ]]; then
    msg "Script finalizado con errores (código: ${exit_code})" "WARNING"
  fi
}

trap 'handle_error $? $LINENO' ERR
trap 'cleanup_on_exit' EXIT

# =============================================================================
# SECTION: Funciones de Utilidad General
# =============================================================================

pause_continue() {
  local mensaje

  if [[ -n "${1:-}" ]]; then
    mensaje="${1}. Presiona [ENTER] para continuar..."
  else
    mensaje="Comando ejecutado. Presiona [ENTER] para continuar..."
  fi

  echo -en "${Gray}"
  read -p "$mensaje"
  echo -en "${Color_Off}"
}

detect_system() {
  if [[ -f /data/data/com.termux/files/usr/bin/pkg ]]; then
    echo "termux"
  elif grep -q Microsoft /proc/version 2>/dev/null; then
    echo "wsl"
  elif [[ -f /etc/os-release ]]; then
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
  elif [[ -n "${MSYSTEM:-}" ]]; then
    echo "gitbash"
  else
    echo "unknown"
  fi
}

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

find_env() {
  local key="$1"
  local path_file="$2"

  if ! validate_file "$path_file" "archivo .env"; then
    return 1
  fi

  local value
  value=$(awk -F '=' -v k="$key" '
    $1 ~ "^"k"$" && $0 !~ /^#/ {
      $1=""; sub(/^ /, "", $0); print $0; exit
    }
  ' "$path_file" | tr -d '\r\n')

  echo -n "$value"
}

get_wp_var() {
  local config_file="$1"
  local var_name="$2"

  if ! validate_file "$config_file" "wp-config.php"; then
    return 1
  fi

  local value=$(grep -E "define\('$var_name',\s*'[^']*'" "$config_file" 2>/dev/null | sed -E "s/.*define\('$var_name',\s*'([^']*)'.*/\1/")

  if [[ -z "$value" ]]; then
    value=$(grep -E "define\('$var_name',\s*[^)]*" "$config_file" 2>/dev/null | sed -E "s/.*define\('$var_name',\s*([^)]*).*/\1/")
  fi

  echo "$value"
}

search_php_bin() {
  local possible_paths=(
    "/opt/alt/php83/usr/bin/php"
    "/opt/alt/php82/usr/bin/php"
    "/opt/alt/php81/usr/bin/php"
    "/usr/local/lsws/lsphp83/bin/php"
    "/usr/local/lsws/lsphp82/bin/php"
    "/usr/local/lsws/lsphp81/bin/php"
    "/usr/bin/php"
    "php"
  )

  for path in "${possible_paths[@]}"; do
    if [[ -x "$path" ]] 2>/dev/null; then
      echo "$path"
      return 0
    fi
  done

  local default_php
  default_php=$(command -v php 2>/dev/null)
  if [[ -n "$default_php" ]]; then
    echo "$default_php"
    return 0
  fi

  return 1
}

view_vars_config() {
  echo -e "${Color_Off}"
  echo -e "╔═══════════════════════════════════════════════╗"
  echo -e "║         ${BYellow}CONFIGURACIÓN ACTUAL${Color_Off}              ║"
  echo -e "║                                               ║"
  echo -e "║ ${BBlue}DATE_HOUR:${Color_Off}                                 ║"
  echo -e "║    ${DATE_HOUR}                              ║"
  echo -e "║ ${BBlue}CURRENT_USER:${Color_Off}                              ║"
  echo -e "║    ${CURRENT_USER}                           ║"
  echo -e "║ ${BBlue}CURRENT_PC_NAME:${Color_Off}                           ║"
  echo -e "║    ${CURRENT_PC_NAME}                        ║"
  echo -e "║ ${BBlue}PATH_DOMAIN:${Color_Off}                               ║"
  echo -e "║    ${PATH_DOMAIN}                            ║"
  echo -e "║ ${BBlue}PHP_BIN:${Color_Off}                                   ║"
  echo -e "║    ${PHP_BIN}                                ║"
  echo -e "║ ${BBlue}DB_HOST:${Color_Off}                                   ║"
  echo -e "║    ${DB_HOST}                                ║"
  echo -e "║ ${BBlue}DB_PORT:${Color_Off}                                   ║"
  echo -e "║    ${DB_PORT}                                ║"
  echo -e "║ ${BBlue}DB_USER:${Color_Off}                                   ║"
  echo -e "║    ${DB_USER}                                ║"
  echo -e "║ ${BBlue}DB_NAME:${Color_Off}                                   ║"
  echo -e "║    ${DB_NAME}                                ║"
  echo -e "╚═══════════════════════════════════════════════╝"
  echo -e "${Color_Off}"
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

# =============================================================================
# SECTION: Funciones MySQL
# =============================================================================

check_verify_ssl_mode() {
  if ! command -v "${PATH_MYSQL}" >/dev/null 2>&1; then
    echo 0
    return
  fi

  "${PATH_MYSQL}" --help --verbose 2>/dev/null | grep -Eo '^ *--[a-z0-9\-]+' | grep -q '^ *--ssl-mode$'
  if [[ $? -eq 0 ]]; then
    echo 1
  else
    echo 0
  fi
}

fn_get_mysql_version() {
  echo -en " ${Gray}"
  "${PATH_MYSQL}" --version 2>/dev/null || msg "Error: No se pudo obtener la versión de MySQL" "ERROR"
  echo -en " ${Color_Off}"
}

fn_check_conexion_db() {
  msg "Verificando conexión a la base de datos..." "INFO"

  if ! validate_file "$PATH_CONFIG_MYSQL" "archivo de configuración MySQL"; then
    return 1
  fi

  fn_get_mysql_version

  if "$PATH_MYSQL" --defaults-file="$PATH_CONFIG_MYSQL" $SSL_OPTION "$DB_NAME" -e "SELECT 1" >/dev/null 2>&1; then
    msg "Conexión a la base de datos exitosa" "SUCCESS"
    return 0
  else
    msg "Error: No se pudo conectar a la base de datos" "ERROR"
    msg "DB_HOST: ${DB_HOST}" "INFO"
    msg "DB_PORT: ${DB_PORT}" "INFO"
    msg "DB_USER: ${DB_USER}" "INFO"
    msg "DB_NAME: ${DB_NAME}" "INFO"
    return 1
  fi
}

fn_set_collate_db() {
  local path_file_sql="$1"

  if ! validate_file "$path_file_sql" "archivo SQL"; then
    return 1
  fi

  sed -i 's/utf8mb4_unicode_520_ci/utf8mb4_unicode_ci/g' "$path_file_sql"
  sed -i 's/utf8mb4_0900_ai_ci/utf8mb4_general_ci/g' "$path_file_sql"

  msg "Collations normalizadas" "SUCCESS"
}

fn_backup_wordpress_db() {
  log_to_file "INFO" "Iniciando backup de base de datos: ${DB_NAME}"
  if ! fn_check_conexion_db; then
    msg "Error: No se puede realizar el backup debido a un problema de conexión" "ERROR"
    return 1
  fi

  fn_get_mysql_version

  local backup_dir="${ROOT_PATH}/my_resource/backup_db"
  mkdir -p "$backup_dir" || {
    msg "Error: No se pudo crear el directorio de backup: $backup_dir" "ERROR"
    return 1
  }

  local path_file_sql="${backup_dir}/backup_${DB_NAME}.sql"

  msg "Iniciando backup de la base de datos..." "INFO"
  msg "Archivo de destino: $path_file_sql" "INFO"

  if "$PATH_MYSQL_DUMP" --defaults-file="$PATH_CONFIG_MYSQL" \
    $SSL_OPTION \
    --databases "$DB_NAME" \
    --routines --triggers --add-drop-database ${PATH_MYSQL_DUMP_PARAMETERS} \
    --single-transaction --skip-lock-tables \
    --default-character-set=utf8mb4 \
    --skip-set-charset \
    --result-file="$path_file_sql" 2>/dev/null; then

    fn_set_collate_db "$path_file_sql"

    msg "=============================================" "SUCCESS"
    msg "Exportación finalizada: ${path_file_sql}" "SUCCESS"
    pause_continue 'Se realizó backup de la DB'
    log_to_file "SUCCESS" "Backup completado: ${backup_file}"
    return 0
  else
    msg "Error: Falló la exportación de la base de datos" "ERROR"
    log_to_file "ERROR" "Backup No completado: ${backup_file}"
    return 1
  fi
}

fn_restore_wordpress_db() {
  log_to_file "INFO" "Iniciando restauración de BD desde: ${sql_file}"
  local path_file_sql="${ROOT_PATH}/my_resource/backup_db/backup_${DB_NAME}.sql"

  msg "=============================================" "INFO"
  msg "Fichero SQL Backup: ${path_file_sql}" "INFO"

  if ! validate_file "$path_file_sql" "archivo de backup SQL"; then
    return 1
  fi

  fn_get_mysql_version
  echo ""

  sed -i 's/utf8mb4_0900_ai_ci/utf8mb4_general_ci/g' "$path_file_sql"

  msg "Eliminando y recreando la base de datos..." "INFO"

  if "$PATH_MYSQL" --defaults-file="$PATH_CONFIG_MYSQL" $SSL_OPTION \
    -e "DROP DATABASE IF EXISTS ${DB_NAME}; CREATE DATABASE ${DB_NAME};" 2>/dev/null; then

    msg "Base de datos recreada exitosamente" "SUCCESS"
  else
    msg "Error: No se pudo recrear la base de datos" "ERROR"
    return 1
  fi

  msg "Importando datos del backup..." "INFO"

  if "$PATH_MYSQL" --defaults-file="$PATH_CONFIG_MYSQL" $SSL_OPTION \
    "$DB_NAME" --default-character-set=utf8 --comments <"$path_file_sql" 2>/dev/null; then

    log_to_file "SUCCESS" "Restauración completada exitosamente"


    msg "Tarea Correcta" "SUCCESS"
    pause_continue 'Se restauró la DB'
    return 0
  else
    msg "Error: Falló la importación del backup" "ERROR"
    return 1
  fi
}

fn_create_wordpress_db() {
  fn_get_mysql_version

  msg "Creando base de datos: $DB_NAME" "INFO"

  if "$PATH_MYSQL" --defaults-file="$PATH_CONFIG_MYSQL" $SSL_OPTION \
    -e "DROP DATABASE IF EXISTS ${DB_NAME}; CREATE DATABASE ${DB_NAME};" 2>/dev/null; then

    msg "Tarea Correcta" "SUCCESS"
    pause_continue 'Se creó la DB'
    return 0
  else
    msg "Error: No se pudo crear la base de datos" "ERROR"
    return 1
  fi
}

fn_check_mysql_permissions() {
  msg "Verificando permisos de usuario MySQL..." "INFO"

  if ! fn_check_conexion_db; then
    msg "Error: No se puede verificar permisos debido a un problema de conexión" "ERROR"
    return 1
  fi

  fn_get_mysql_version

  msg "=== PERMISOS DEL USUARIO ACTUAL ===" "INFO"
  "$PATH_MYSQL" --defaults-file="$PATH_CONFIG_MYSQL" $SSL_OPTION -e "SHOW GRANTS;" 2>/dev/null

  msg "=== USUARIOS EN EL SISTEMA ===" "INFO"
  "$PATH_MYSQL" --defaults-file="$PATH_CONFIG_MYSQL" $SSL_OPTION -e "SELECT User, Host FROM mysql.user;" 2>/dev/null

  pause_continue 'Se verificaron los permisos de MySQL'
}

fn_fix_action_scheduler() {
  msg "=========================================" "INFO"
  msg "REPARANDO ACTION SCHEDULER" "INFO"
  msg "=========================================" "INFO"
  echo ""

  if ! fn_check_conexion_db; then
    msg "Error: No se puede reparar debido a problemas de conexión" "ERROR"
    return 1
  fi

  fn_get_mysql_version
  echo ""

  msg "Analizando tablas..." "INFO"

  local tables_exist=$("$PATH_MYSQL" --defaults-file="$PATH_CONFIG_MYSQL" $SSL_OPTION "$DB_NAME" \
    -e "SHOW TABLES LIKE 'wp_actionscheduler%';" 2>/dev/null | wc -l)

  if [[ "$tables_exist" -eq 0 ]]; then
    msg "No se encontraron tablas de Action Scheduler" "WARNING"
    pause_continue
    return 0
  fi

  msg "Tablas encontradas: $tables_exist" "INFO"
  echo ""

  msg "Creando backup..." "INFO"
  local backup_file="${ROOT_PATH}/my_resource/backup_db/action_scheduler_$(date +%Y%m%d_%H%M%S).sql"
  mkdir -p "${ROOT_PATH}/my_resource/backup_db"

  "$PATH_MYSQL_DUMP" --defaults-file="$PATH_CONFIG_MYSQL" $SSL_OPTION \
    "$DB_NAME" wp_actionscheduler_actions wp_actionscheduler_claims \
    wp_actionscheduler_groups wp_actionscheduler_logs \
    --result-file="$backup_file" 2>/dev/null

  [[ -f "$backup_file" ]] && msg "Backup: $backup_file" "SUCCESS"
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
      msg "Reparación ligera..." "INFO"
      "$PATH_MYSQL" --defaults-file="$PATH_CONFIG_MYSQL" $SSL_OPTION "$DB_NAME" -e "
SET @max_id = (SELECT IFNULL(MAX(action_id), 0) FROM wp_actionscheduler_actions);
SET @sql = CONCAT('ALTER TABLE wp_actionscheduler_actions AUTO_INCREMENT = ', @max_id + 1);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
REPAIR TABLE wp_actionscheduler_actions; OPTIMIZE TABLE wp_actionscheduler_actions;" 2>/dev/null
      msg "Completada" "SUCCESS"
      ;;
    2)
      msg "Reparación completa..." "INFO"
      "$PATH_MYSQL" --defaults-file="$PATH_CONFIG_MYSQL" $SSL_OPTION "$DB_NAME" -e "
DELETE FROM wp_actionscheduler_actions WHERE status IN ('complete','failed','canceled') AND scheduled_date_gmt < DATE_SUB(NOW(), INTERVAL 30 DAY);
DELETE FROM wp_actionscheduler_logs WHERE action_id NOT IN (SELECT action_id FROM wp_actionscheduler_actions);
SET @max_id = (SELECT IFNULL(MAX(action_id), 0) FROM wp_actionscheduler_actions);
SET @sql = CONCAT('ALTER TABLE wp_actionscheduler_actions AUTO_INCREMENT = ', @max_id + 1);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
REPAIR TABLE wp_actionscheduler_actions, wp_actionscheduler_claims, wp_actionscheduler_groups, wp_actionscheduler_logs;
OPTIMIZE TABLE wp_actionscheduler_actions, wp_actionscheduler_claims, wp_actionscheduler_groups, wp_actionscheduler_logs;" 2>/dev/null
      msg "Completada" "SUCCESS"
      ;;
    3)
      read -rp "ELIMINAR TODAS las tareas? (s/N): " confirm
      if [[ "$confirm" == "s" || "$confirm" == "S" ]]; then
        msg "Reparación profunda..." "INFO"
        "$PATH_MYSQL" --defaults-file="$PATH_CONFIG_MYSQL" $SSL_OPTION "$DB_NAME" -e "
TRUNCATE TABLE wp_actionscheduler_actions; TRUNCATE TABLE wp_actionscheduler_claims;
TRUNCATE TABLE wp_actionscheduler_groups; TRUNCATE TABLE wp_actionscheduler_logs;
ALTER TABLE wp_actionscheduler_actions AUTO_INCREMENT = 1;
OPTIMIZE TABLE wp_actionscheduler_actions, wp_actionscheduler_claims, wp_actionscheduler_groups, wp_actionscheduler_logs;" 2>/dev/null
        msg "Completada - WooCommerce recreará tareas automáticamente" "SUCCESS"
      else
        msg "Cancelada" "INFO"
      fi
      ;;
    *) msg "Cancelada" "INFO" ;;
  esac

  echo ""
  msg "Estado final:" "INFO"
  "$PATH_MYSQL" --defaults-file="$PATH_CONFIG_MYSQL" $SSL_OPTION "$DB_NAME" -e "
SELECT 'Total' as Estado, COUNT(*) as Cantidad FROM wp_actionscheduler_actions
UNION ALL SELECT 'Pendientes', COUNT(*) FROM wp_actionscheduler_actions WHERE status='pending'
UNION ALL SELECT 'Completadas', COUNT(*) FROM wp_actionscheduler_actions WHERE status='complete'
UNION ALL SELECT 'Fallidas', COUNT(*) FROM wp_actionscheduler_actions WHERE status='failed';" 2>/dev/null

  msg "Reparación finalizada" "SUCCESS"
  pause_continue 'Reparación completada'
}

# =============================================================================
# SECTION: Funciones WordPress
# =============================================================================

check_install_wp_cli() {
  if [[ ! -f "wp-cli.phar" ]]; then
    msg "Descargando WP-CLI en ${PWD}..." "INFO"

    if curl -sSLO https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar 2>/dev/null; then
      msg "WP-CLI descargado exitosamente" "SUCCESS"
    else
      msg "Error: No se pudo descargar WP-CLI" "ERROR"
      return 1
    fi
  fi
  return 0
}

fn_download_wp_cli() {
  if ! validate_directory "$PATH_DOMAIN" "directorio de WordPress"; then
    return 1
  fi

  cd "$PATH_DOMAIN" || {
    msg "Error: No se pudo acceder al directorio: $PATH_DOMAIN" "ERROR"
    return 1
  }

  check_install_wp_cli || return 1

  msg "" "INFO"
  msg "Descargando WP-CLI..." "INFO"

  if curl -O "https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar" 2>/dev/null; then
    msg "WP-CLI descargado exitosamente" "SUCCESS"
    msg "Versión de WordPress instalada:" "INFO"
    $WP_CLI core version 2>/dev/null || msg "Error al obtener versión de WordPress" "ERROR"
    pause_continue 'Descargado wp-cli'
  else
    msg "Error: No se pudo descargar WP-CLI" "ERROR"
    return 1
  fi
}

fn_download_wordpress() {
  read -p "Seguro que deseas borrar $PATH_DOMAIN y reinstalar WordPress? (s/N): " confirm
  [[ "$confirm" != "s" && "$confirm" != "S" ]] && return

  rm -rf "$PATH_DOMAIN"/* || {
    msg "Error: No se pudo limpiar el directorio de WordPress" "ERROR"
    return 1
  }

  if ! validate_directory "$PATH_TEMP" "directorio temporal"; then
    mkdir -p "$PATH_TEMP" || {
      msg "Error: No se pudo crear el directorio temporal" "ERROR"
      return 1
    }
  fi

  cd "$PATH_TEMP" || {
    msg "Error: No se pudo acceder a: $PATH_TEMP" "ERROR"
    return 1
  }

  msg "Limpiando archivos de wordpress..." "INFO"
  rm -rf ./*.* 2>/dev/null
  sleep 3

  msg "Descargando wordpress..." "INFO"
  if curl -O "$DOWNLOAD_URL_WORDPRESS" 2>/dev/null; then
    msg "WordPress descargado exitosamente" "SUCCESS"
  else
    msg "Error: No se pudo descargar WordPress" "ERROR"
    return 1
  fi

  tar -xzvf *es_ES.tar.gz && cp -R ./wordpress/* "${PATH_DOMAIN}/" || {
    msg "Error: No se pudo extraer o copiar WordPress" "ERROR"
    return 1
  }

  rm -rf ./wordpress
  rm -rf ./*.tar.gz

  cd "$PATH_DOMAIN" || return 1
  check_install_wp_cli
  generate_config_wp
  fn_create_wordpress_db
  pause_continue 'Se instaló por completo WordPress'
  msg "Tarea Correcta" "SUCCESS"
}

fn_backup_wordpress() {
  local fecha_temp=$(date -u -d "-5 hours" "+%Y-%m-%d_%H-%M" 2>/dev/null || date "+%Y-%m-%d_%H-%M")
  msg "Backup files de wordpress..." "INFO"

  local path_backup="${ROOT_PATH}/my_resource/backup_files/${fecha_temp}_${APACHE_PUBLIC_ROOT}/"

  mkdir -p "${path_backup}" || {
    msg "Error: No se pudo crear directorio de backup: $path_backup" "ERROR"
    return 1
  }

  if ! validate_directory "$PATH_DOMAIN" "directorio de WordPress"; then
    return 1
  fi

  cd "$PATH_DOMAIN" || return 1

  if cp -a "${PATH_DOMAIN}/." "${path_backup}/"; then
    msg "=============================================" "SUCCESS"
    msg "Backup generado en: ${path_backup}/" "SUCCESS"

    du -smh "${PATH_DOMAIN}" 2>/dev/null | awk '{print "Peso de nuestra web es: " $1}'
    du -smh "${path_backup}" 2>/dev/null | awk '{print "Peso del backup es: " $1}'
    pause_continue 'Se realizó el backup de wordpress'
  else
    msg "Error: No se pudo realizar el backup" "ERROR"
    return 1
  fi
}

fn_backup_completo_wordpress() {
  local fecha_temp=$(date -u -d "-5 hours" "+%Y-%m-%d_%H-%M" 2>/dev/null || date "+%Y-%m-%d_%H-%M")
  msg "Backup completo de wordpress..." "INFO"

  local path_backup_complete="${ROOT_PATH}/my_resource/completo/${fecha_temp}_${APACHE_PUBLIC_ROOT}"
  mkdir -p "${path_backup_complete}" || {
    msg "Error: No se pudo crear directorio de backup" "ERROR"
    return 1
  }

  if ! validate_directory "$PATH_DOMAIN" "directorio de WordPress"; then
    return 1
  fi

  cd "$PATH_DOMAIN" || return 1

  if cp -a "${PATH_DOMAIN}/." "${path_backup_complete}/"; then
    msg "Archivos copiados exitosamente" "SUCCESS"
  else
    msg "Error: No se pudieron copiar los archivos" "ERROR"
    return 1
  fi

  msg "Backup de DB..." "INFO"

  local path_file_sql="${path_backup_complete}/backup_${DB_NAME}.sql"

  if "$PATH_MYSQL_DUMP" --defaults-file="$PATH_CONFIG_MYSQL" \
    $SSL_OPTION \
    --databases "$DB_NAME" \
    --routines --triggers --add-drop-database ${PATH_MYSQL_DUMP_PARAMETERS} \
    --single-transaction --skip-lock-tables \
    --default-character-set=utf8mb4 \
    --skip-set-charset \
    --result-file="$path_file_sql" 2>/dev/null; then

    sed -i 's/utf8mb4_unicode_520_ci/utf8mb4_unicode_ci/g' "$path_file_sql"

    msg "===============================================================" "SUCCESS"
    msg "Backup generado en: ${path_backup_complete}" "SUCCESS"
    msg "Backup sql en: ${path_file_sql}" "SUCCESS"
    msg "Tarea Correcta" "SUCCESS"
    pause_continue 'Se realizó el backup de wordpress y DB'
  else
    msg "Error: No se pudo realizar el backup de la base de datos" "ERROR"
    return 1
  fi
}

fn_update_wordpres_version() {
  if ! validate_directory "$PATH_DOMAIN" "directorio de WordPress"; then
    return 1
  fi

  cd "$PATH_DOMAIN" || return 1
  pwd
  check_install_wp_cli || return 1

  if $WP_CLI core update 2>/dev/null; then
    msg "Tarea Correcta" "SUCCESS"
    pause_continue 'Se actualizó la versión de wordpress'
  else
    msg "Error: No se pudo actualizar WordPress" "ERROR"
    return 1
  fi
}

generate_config_wp() {
  local path_file="${PATH_DOMAIN}/wp-config-sample.php"
  local path_file_new="${PATH_DOMAIN}/wp-config.php"

  if ! validate_file "$path_file" "wp-config-sample.php"; then
    return 1
  fi

  cp "$path_file" "$path_file_new" || {
    msg "Error: No se pudo copiar wp-config-sample.php" "ERROR"
    return 1
  }

  sed -i "s/database_name_here/${DB_NAME}/g" "$path_file_new"
  sed -i "s/username_here/${DB_USER}/g" "$path_file_new"
  sed -i "s/password_here/${DB_PASSWORD}/g" "$path_file_new"
  sed -i "s/localhost/${DB_HOST}/g" "$path_file_new"

  msg "Tarea Correcta" "SUCCESS"
  pause_continue "Se creó el archivo wp-config.php"
}

fn_install_plugin() {
  if ! validate_directory "$PATH_DOMAIN" "directorio de WordPress"; then
    return 1
  fi

  cd "$PATH_DOMAIN" || {
    msg "No se pudo acceder al directorio $PATH_DOMAIN" "ERROR"
    return 1
  }

  check_install_wp_cli || return 1

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

  msg "=========== PLUGINS RECOMENDADOS ==========" "INFO"
  for i in "${!recommended_plugins[@]}"; do
    msg "$((i + 1)). ${recommended_plugins[$i]}"
  done
  msg "0. Ingresar manualmente otro plugin"
  msg "==========================================" "INFO"

  local plugin_input
  read -rp "Seleccione un número, nombre del plugin o múltiples plugins separados por comas: " user_input

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

  if [[ -z "$plugin_input" ]]; then
    msg "No se ingresó ningún nombre de plugin" "ERROR"
    return 1
  fi

  IFS=',' read -ra plugin_list <<<"$plugin_input"

  local success_count=0
  local total_plugins=${#plugin_list[@]}

  for plugin_name in "${plugin_list[@]}"; do
    plugin_name=$(echo "$plugin_name" | xargs)

    if [[ -n "$plugin_name" ]]; then
      msg "Instalando plugin: $plugin_name..." "INFO"
      if $WP_CLI plugin install "$plugin_name" --activate 2>/dev/null; then
        msg "Plugin $plugin_name instalado y activado correctamente" "SUCCESS"
        ((success_count++))
      else
        msg "Error al instalar el plugin $plugin_name" "ERROR"
      fi
    fi
  done

  msg "=========================================" "INFO"
  msg "   Resumen de instalación:" "INFO"
  msg "   Total de plugins: $total_plugins" "INFO"
  msg "   Instalados exitosamente: $success_count" "INFO"
  msg "   Fallos: $((total_plugins - success_count))" "INFO"
  msg "=========================================" "INFO"

  pause_continue
}

fn_deactivate_plugin() {
  if ! validate_directory "$PATH_DOMAIN" "directorio de WordPress"; then
    return 1
  fi

  cd "$PATH_DOMAIN" || {
    msg "No se pudo acceder al directorio $PATH_DOMAIN" "ERROR"
    return 1
  }

  check_install_wp_cli || return 1

  msg "=========== PLUGINS ACTIVOS ==========" "INFO"

  mapfile -t active_plugins < <($WP_CLI plugin list --status=active --field=name 2>/dev/null)

  if [[ ${#active_plugins[@]} -eq 0 ]]; then
    msg "No hay plugins activos para desactivar" "WARNING"
    pause_continue
    return 0
  fi

  for i in "${!active_plugins[@]}"; do
    msg "$((i + 1)). ${active_plugins[$i]}"
  done
  msg "0. Desactivar TODOS los plugins activos"
  msg "==========================================" "INFO"

  local plugin_input
  read -rp "Seleccione un número, nombre del plugin o múltiples plugins separados por comas: " user_input

  if [[ "$user_input" =~ ^[0-9]+$ ]]; then
    if ((user_input == 0)); then
      msg "¿Está seguro de desactivar TODOS los plugins activos? (s/N): " "WARNING"
      read -rp "" confirm
      if [[ "$confirm" == "s" || "$confirm" == "S" ]]; then
        msg "Desactivando todos los plugins..." "INFO"
        if $WP_CLI plugin deactivate --all 2>/dev/null; then
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

  if [[ -z "$plugin_input" ]]; then
    msg "No se ingresó ningún nombre de plugin" "ERROR"
    return 1
  fi

  IFS=',' read -ra plugin_list <<<"$plugin_input"

  local success_count=0
  local total_plugins=${#plugin_list[@]}

  for plugin_name in "${plugin_list[@]}"; do
    plugin_name=$(echo "$plugin_name" | xargs)

    if [[ -n "$plugin_name" ]]; then
      msg "Desactivando plugin: $plugin_name..." "INFO"
      if $WP_CLI plugin deactivate "$plugin_name" 2>/dev/null; then
        msg "Plugin $plugin_name desactivado correctamente" "SUCCESS"
        ((success_count++))
      else
        msg "Error al desactivar el plugin $plugin_name" "ERROR"
      fi
    fi
  done

  msg "=========================================" "INFO"
  msg "   Resumen de desactivación:" "INFO"
  msg "   Total de plugins: $total_plugins" "INFO"
  msg "   Desactivados exitosamente: $success_count" "INFO"
  msg "   Fallos: $((total_plugins - success_count))" "INFO"
  msg "=========================================" "INFO"

  pause_continue
}

fn_list_plugins() {
  if ! validate_directory "$PATH_DOMAIN" "directorio de WordPress"; then
    return 1
  fi

  cd "$PATH_DOMAIN" || {
    msg "No se pudo acceder al directorio $PATH_DOMAIN" "ERROR"
    return 1
  }

  check_install_wp_cli || return 1

  msg "=========================================" "INFO"
  msg "       LISTADO DE PLUGINS" "INFO"
  msg "=========================================" "INFO"
  echo ""

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
      msg "TODOS LOS PLUGINS:" "INFO"
      msg "=========================================" "INFO"
      $WP_CLI plugin list 2>/dev/null
      ;;
    2)
      msg "PLUGINS ACTIVOS:" "SUCCESS"
      msg "=========================================" "INFO"
      $WP_CLI plugin list --status=active 2>/dev/null

      local count=$($WP_CLI plugin list --status=active --format=count 2>/dev/null)
      echo ""
      msg "Total de plugins activos: $count" "INFO"
      ;;
    3)
      msg "PLUGINS INACTIVOS:" "WARNING"
      msg "=========================================" "INFO"
      $WP_CLI plugin list --status=inactive 2>/dev/null

      local count=$($WP_CLI plugin list --status=inactive --format=count 2>/dev/null)
      echo ""
      msg "Total de plugins inactivos: $count" "INFO"
      ;;
    4)
      msg "PLUGINS CON ACTUALIZACIONES DISPONIBLES:" "WARNING"
      msg "=========================================" "INFO"
      $WP_CLI plugin list --update=available 2>/dev/null

      local count=$($WP_CLI plugin list --update=available --format=count 2>/dev/null)
      echo ""
      if [[ "$count" -gt 0 ]]; then
        msg "Total de plugins con actualizaciones: $count" "WARNING"
        msg "Puede actualizarlos con la opción 13 del menú principal" "INFO"
      else
        msg "No hay plugins con actualizaciones disponibles" "SUCCESS"
      fi
      ;;
    5)
      msg "PLUGINS ACTIVOS - INFORMACIÓN DETALLADA:" "SUCCESS"
      msg "=========================================" "INFO"

      mapfile -t active_plugins < <($WP_CLI plugin list --status=active --field=name 2>/dev/null)

      if [[ ${#active_plugins[@]} -eq 0 ]]; then
        msg "No hay plugins activos" "WARNING"
      else
        for plugin in "${active_plugins[@]}"; do
          echo ""
          msg "────────────────────────────────────" "INFO"
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
      $WP_CLI plugin list 2>/dev/null
      ;;
  esac

  echo ""
  msg "=========================================" "INFO"
  pause_continue 'Se listaron los plugins'
}

fn_check_site_status() {
  if ! validate_directory "$PATH_DOMAIN" "directorio de WordPress"; then
    return 1
  fi

  cd "$PATH_DOMAIN" || return 1
  check_install_wp_cli || return 1

  if $WP_CLI core is-installed 2>/dev/null; then
    msg "WordPress está instalado" "SUCCESS"
  else
    msg "WordPress NO está instalado" "WARNING"
  fi

  pause_continue 'Se verificó el estado del sitio'
}

fn_update_all() {
  if ! validate_directory "$PATH_DOMAIN" "directorio de WordPress"; then
    return 1
  fi

  cd "$PATH_DOMAIN" || return 1
  check_install_wp_cli || return 1

  msg "Actualizando plugins..." "INFO"
  $WP_CLI plugin update --all 2>/dev/null

  msg "Actualizando temas..." "INFO"
  $WP_CLI theme update --all 2>/dev/null

  pause_continue "Se actualizó todos los plugins y temas"
}

fn_flush_cache() {
  if ! validate_directory "$PATH_DOMAIN" "directorio de WordPress"; then
    return 1
  fi

  cd "$PATH_DOMAIN" || return 1
  check_install_wp_cli || return 1

  msg "Vaciando caché de WordPress..." "INFO"

  $WP_CLI cache flush 2>/dev/null

  if $WP_CLI plugin is-active litespeed-cache 2>/dev/null; then
    msg "Limpiando LiteSpeed Cache..." "INFO"
    $WP_CLI litespeed-cache purge all 2>/dev/null
  fi

  if $WP_CLI plugin is-active w3-total-cache 2>/dev/null; then
    msg "Limpiando W3 Total Cache..." "INFO"
    $WP_CLI w3-total-cache flush all 2>/dev/null
  fi

  if $WP_CLI plugin is-active wp-rocket 2>/dev/null; then
    msg "Limpiando WP Rocket..." "INFO"
    $WP_CLI wp-rocket clean --confirm 2>/dev/null
  fi

  if $WP_CLI plugin is-active wp-super-cache 2>/dev/null; then
    msg "Limpiando WP Super Cache..." "INFO"
    $WP_CLI super-cache flush 2>/dev/null
  fi

  if $WP_CLI plugin is-active cache-enabler 2>/dev/null; then
    msg "Limpiando Cache Enabler..." "INFO"
    $WP_CLI cache-enabler clear 2>/dev/null
  fi

  msg "Cache limpiado correctamente" "SUCCESS"
  pause_continue 'Se vació la caché'
}

fn_manteniment_wp_cli() {
  if ! validate_directory "$PATH_DOMAIN" "directorio de WordPress"; then
    return 1
  fi

  cd "$PATH_DOMAIN" || return 1
  msg "Mantenimiento WP-cli" "INFO"

  if curl -sSL -o wp-maintenance.sh https://raw.githubusercontent.com/cesar23/utils_dev/refs/heads/master/scripts/wordpress/mantenimiento-wp-cli.sh 2>/dev/null; then
    chmod +x wp-maintenance.sh &&
    ./wp-maintenance.sh &&
    rm -rf wp-maintenance.sh
    msg "Mantenimiento completado" "SUCCESS"
  else
    msg "Error: No se pudo descargar el script de mantenimiento" "ERROR"
    return 1
  fi
}

fn_info_php() {
  if ! validate_command "$PHP_BIN" "PHP"; then
    return 1
  fi

  $PHP_BIN -i 2>/dev/null | grep -E 'memory_limit|post_max_size|upload_max_filesize|max_input_vars|max_execution_time|max_input_time'
  pause_continue 'Información de PHP'
}

test_script() {
  msg "===============================================" "INFO"
  msg " 1. Verificando [$PATH_DOMAIN]" "INFO"
  msg "===============================================" "INFO"
  echo ""

  local path_wp_login="${PATH_DOMAIN}/wp-config.php"
  if validate_file "$path_wp_login" "wp-config.php"; then
    msg "El archivo existe: ${path_wp_login}" "SUCCESS"
    msg "Contenido del fichero: ${BBlue}${path_wp_login}${Color_Off}" "INFO"
    echo ""
    cat "$path_wp_login"
    msg "Tarea Correcta" "SUCCESS"
  else
    msg "El archivo [${path_wp_login}] NO existe" "ERROR"
  fi
  pause_continue

  msg "===============================================" "INFO"
  msg " 2. Verificando conexión DB" "INFO"
  msg "===============================================" "INFO"
  echo ""
  fn_check_conexion_db
  msg "Tarea Correcta" "SUCCESS"
  pause_continue

  msg "===============================================" "INFO"
  msg " 3. Verificando generación de ficheros" "INFO"
  msg "===============================================" "INFO"
  echo ""

  local path_backup="${ROOT_PATH}/my_resource/backup_db"
  mkdir -p "${path_backup}" || {
    msg "Error: No se pudo crear directorio de prueba" "ERROR"
    pause_continue
    return 1
  }

  local path_file_sql="${path_backup}/backup_${DB_NAME}_test.sql"

  echo "" >"$path_file_sql"
  if [[ -f "$path_file_sql" ]]; then
    msg "El archivo de backup demo existe: ${path_file_sql}" "SUCCESS"
    msg "Tarea Correcta" "SUCCESS"
  else
    msg "El archivo de backup NO existe: ${path_file_sql}" "ERROR"
  fi
  pause_continue

  msg "===============================================" "INFO"
  msg " 4. Verificando info PHP" "INFO"
  msg "===============================================" "INFO"
  echo ""
  fn_info_php
  msg "Tarea Correcta" "SUCCESS"
  pause_continue
}

favorites() {
  clear
  echo -e "${BBlue}Comandos Favoritos de WP-CLI:${Color_Off}"
  echo -e "${Gray}========================================================================${Color_Off}"
  echo -e "${Yellow} 1. WooCommerce versión 9.9.5${Color_Off}"
  echo ""
  echo -e "${Purple} - Para instalar la versión estable${Color_Off}"
  echo -e "cd ${PATH_DOMAIN} &&"
  echo -e "${PHP_BIN} wp-cli.phar plugin install woocommerce --version=9.9.5 --force &&"
  echo -e "${PHP_BIN} wp-cli.phar core update-db &&"
  echo -e "${PHP_BIN} wp-cli.phar wc update &&"
  echo -e "cd ${CURRENT_DIR}"
  echo ""
  echo -e "${Gray}========================================================================${Color_Off}"
  echo -e "${Yellow}2. Instalar WordPress 6.8.1 (estable)${Color_Off}"
  echo ""
  echo -e "${Purple} - Ver versión Actual${Color_Off}"
  echo -e "cd ${PATH_DOMAIN} &&"
  echo -e "${PHP_BIN} wp-cli.phar core version --extra &&"
  echo -e "cd ${CURRENT_DIR}"
  echo ""
  echo -e "${Purple} - Actualizar tu wordpress${Color_Off}"
  echo -e "cd ${PATH_DOMAIN} &&"
  echo -e "${PHP_BIN} wp-cli.phar core update --version=6.8.1 --force &&"
  echo -e "${PHP_BIN} wp-cli.phar core update-db &&"
  echo -e "cd ${CURRENT_DIR}"
  echo ""
  echo -e "${Purple} - Instalar directamente esa versión${Color_Off}"
  echo -e "cd ${PATH_DOMAIN} &&"
  echo -e "${PHP_BIN} wp-cli.phar core download --version=6.8.1 --force &&"
  echo -e "${PHP_BIN} wp-cli.phar core update-db &&"
  echo -e "cd ${CURRENT_DIR}"
  echo ""
  echo -e "${Gray}========================================================================${Color_Off}"
  echo -e "${Yellow}3. Plugins${Color_Off}"
  echo ""
  echo -e "${Purple} - Ver plugins activos${Color_Off}"
  echo -e "cd ${PATH_DOMAIN} &&"
  echo -e "${PHP_BIN} wp-cli.phar plugin list --status=active &&"
  echo -e "cd ${CURRENT_DIR}"
  echo ""
  echo -e "${Purple} - Desactivar todos los plugins${Color_Off}"
  echo -e "cd ${PATH_DOMAIN} &&"
  echo -e "${PHP_BIN} wp-cli.phar plugin deactivate --all &&"
  echo -e "cd ${CURRENT_DIR}"
  echo ""
  echo -e "${Purple} - Desactivar todos menos uno${Color_Off}"
  echo -e "cd ${PATH_DOMAIN} &&"
  echo -e "${PHP_BIN} wp-cli.phar plugin deactivate --all --exclude=woocommerce &&"
  echo -e "cd ${CURRENT_DIR}"
  echo ""

  pause_continue
}

# =============================================================================
# SECTION: Inicialización del Script
# =============================================================================

initialize_script() {
  msg "Inicializando script..." "INFO"
  VERSION_WP="6.8.1-es_ES"
  DOWNLOAD_URL_WORDPRESS='https://es.wordpress.org/wordpress-6.8.1-es_ES.tar.gz'

  # Detectar sistema operativo
  SO_SYSTEM=$(detect_system)
  msg "Sistema detectado: ${SO_SYSTEM}" "INFO"

  # Cargar librería de lectura de .env.development
  local path_function_libs_shell="${CURRENT_DIR}/libs_shell/read_env.sh"

  if [[ -f "$path_function_libs_shell" ]]; then
    source "$path_function_libs_shell" || {
      msg "Error: No se pudo cargar el archivo: $path_function_libs_shell" "ERROR"
      exit 1
    }
  else
    msg "Error: No se encontró el archivo: $path_function_libs_shell" "ERROR"
    exit 1
  fi

  # Configurar rutas principales
  local regex="s/\/${NAME_DIR}//"
  ROOT_PATH=$(echo $CURRENT_DIR | sed -e $regex)
  PATH_ENV="$(dirname "$0")/.env.woocomerce-api.local"

  # Verificar existencia de PATH_ENV (debe estar definido en libs_shell/read_env.sh)
  if [[ -z "${PATH_ENV:-}" ]]; then
    msg "Error: PATH_ENV no está definido" "ERROR"
    msg "Asegúrese de configurar PATH_ENV en libs_shell/read_env.sh" "ERROR"
    exit 1
  fi

  # Cargar variable APACHE_PUBLIC_ROOT del .env.development
  APACHE_PUBLIC_ROOT=$(find_env 'APACHE_PUBLIC_ROOT' "$PATH_ENV")

  if [[ -z "$APACHE_PUBLIC_ROOT" ]]; then
    msg "Error: APACHE_PUBLIC_ROOT no está definido en $PATH_ENV" "ERROR"
    exit 1
  fi

  PATH_DOMAIN="${ROOT_PATH}/${APACHE_PUBLIC_ROOT}"
  PATH_TEMP="${ROOT_PATH}/tmp/"

  # Crear directorio temporal si no existe
  mkdir -p "$PATH_TEMP" || {
    msg "Error: No se pudo crear el directorio temporal: $PATH_TEMP" "ERROR"
    exit 1
  }

  msg "Rutas configuradas exitosamente" "SUCCESS"
}

configure_database() {
  local config_file="${PATH_DOMAIN}/wp-config.php"

  echo -e "${Blue}=================================================${Color_Off}"
  echo -e "${Cyan} ¿Desde dónde desea cargar las variables de base de datos?${Color_Off}"
  echo -e "${Cyan} 1. Archivo .env: [${PATH_ENV}]${Color_Off}"
  echo -e "${Cyan} 2. Archivo wp-config.php: [${config_file}]${Color_Off}"
  echo -e "${Blue}=================================================${Color_Off}"
  read -r opt

  case $opt in
    "1")
      DB_HOST=$(find_env 'MYSQL_HOST' "$PATH_ENV")
      DB_PORT=$(find_env 'MYSQL_PORT' "$PATH_ENV")
      DB_USER=$(find_env 'MYSQL_USER_ROOT' "$PATH_ENV")
      DB_PASSWORD=$(find_env 'MYSQL_ROOT_PASSWORD_WINDOWS' "$PATH_ENV")
      DB_NAME=$(find_env 'MYSQL_DATABASE' "$PATH_ENV")

      if [[ -z "$DB_HOST" || -z "$DB_NAME" ]]; then
        msg "Error: No se pudieron cargar las variables de base de datos del .env" "ERROR"
        exit 1
      fi
      ;;
    "2")
      if ! validate_file "$config_file" "wp-config.php"; then
        msg "Error: El archivo wp-config.php no existe. Use la opción 1 para cargar desde .env" "ERROR"
        exit 1
      fi

      DB_HOST=$(get_wp_var "$config_file" "DB_HOST")
      DB_PORT=3306
      DB_USER=$(get_wp_var "$config_file" "DB_USER")
      DB_PASSWORD=$(get_wp_var "$config_file" "DB_PASSWORD")
      [[ -z "$DB_PASSWORD" || "$DB_PASSWORD" = "''" ]] && DB_PASSWORD=""
      DB_NAME=$(get_wp_var "$config_file" "DB_NAME")

      if [[ -z "$DB_HOST" || -z "$DB_NAME" ]]; then
        msg "Error: No se pudieron cargar las variables de base de datos de wp-config.php" "ERROR"
        exit 1
      fi
      ;;
    *)
      msg "Opción no válida" "ERROR"
      exit 1
      ;;
  esac

  msg "Variables de base de datos cargadas exitosamente" "SUCCESS"
}

configure_php() {
  msg "Detectando PHP en el sistema..." "INFO"

  PHP_BIN=$(search_php_bin)
  if [[ $? -eq 0 ]]; then
    msg "PHP encontrado en: $PHP_BIN" "SUCCESS"
    sleep 1
  else
    msg "Error: No se encontró PHP instalado. Verifique la ruta con phpinfo();" "ERROR"
    exit 1
  fi

  # Configurar alias de WP-CLI
  WP_CLI="${PHP_BIN} wp-cli.phar"

  msg "PHP configurado exitosamente" "SUCCESS"
}

configure_mysql() {
  msg "Configurando MySQL..." "INFO"

  # Configurar URL de descarga de WordPress

  # Configurar archivos de configuración
  PATH_CONFIG_MYSQL="${CURRENT_DIR}/config_mysql.cnf"
  PATH_FILE_SQL="${CURRENT_DIR}/backup.sql"

  # Detectar rutas de MySQL según el sistema operativo
  if [[ -n "$SO_SYSTEM" ]] && { [[ "$SO_SYSTEM" = "ubuntu" ]] || [[ "$SO_SYSTEM" = "debian" ]] || [[ "$SO_SYSTEM" = "redhat" ]]; }; then
    PATH_MYSQL="mysql"
    PATH_MYSQL_DUMP="mysqldump"
  else
    PATH_MYSQL="C:/laragon/bin/mysql/mysql-8.4.3-winx64/bin/mysql.exe"
    PATH_MYSQL_DUMP="C:/laragon/bin/mysql/mysql-8.4.3-winx64/bin/mysqldump.exe"
  fi

  # Configurar parámetros adicionales de mysqldump
  PATH_MYSQL_DUMP_PARAMETERS=""

  if [[ "$DB_USER" = "root" ]] || [[ "$SO_SYSTEM" = "gitbash" ]]; then
    PATH_MYSQL_DUMP_PARAMETERS="  --set-gtid-purged=AUTO "
  fi

  msg "MySQL configurado" "SUCCESS"
}

validate_paths_system() {
  echo -e "${Gray}============================================================${Color_Off}"
  echo -e "${Gray} Verificando rutas del sistema ${Color_Off}"
  echo -e "${Gray}============================================================${Color_Off}"
  echo "Sistema: ${SO_SYSTEM}"
  echo ""
  sleep 1

  if [[ -n "$SO_SYSTEM" ]] && { [[ "$SO_SYSTEM" = "ubuntu" ]] || [[ "$SO_SYSTEM" = "debian" ]] || [[ "$SO_SYSTEM" = "redhat" ]]; }; then
    msg "Verificando rutas Linux..." "INFO"
    echo ""

    if check_paths "${PATH_ENV}" "${PATH_DOMAIN}" "${PATH_TEMP}"; then
      msg "Todas las rutas son válidas" "SUCCESS"
      sleep 1
    else
      msg "Algunas rutas no existen" "ERROR"
      pause_continue
      exit 1
    fi
  else
    msg "Verificando rutas Windows..." "INFO"
    echo ""

    if check_paths "${PATH_MYSQL}" "${PATH_MYSQL_DUMP}" "${PATH_ENV}" "${PATH_DOMAIN}" "${PATH_TEMP}"; then
      msg "Todas las rutas son válidas" "SUCCESS"
      sleep 1
    else
      msg "Algunas rutas no existen" "ERROR"
      pause_continue
      exit 1
    fi
  fi
}

configure_mysql_ssl() {
  echo -e "${Gray}============================================================${Color_Off}"
  echo -e "${Gray} MySQL - Configuración SSL ${Color_Off}"
  echo -e "${Gray}============================================================${Color_Off}"
  echo ""
  sleep 1

  local is_ssl_mode=$(check_verify_ssl_mode)

  if [[ "$is_ssl_mode" -eq 1 ]]; then
    msg "Soporta --ssl-mode=DISABLED" "SUCCESS"
    SSL_OPTION="--ssl-mode=DISABLED"
  else
    msg "No soporta --ssl-mode=DISABLED (MariaDB)" "INFO"
    SSL_OPTION="--ssl=0"
  fi

  msg "Verificación SSL completa" "SUCCESS"
}

create_mysql_config_file() {
  echo -e "${Gray}============================================================${Color_Off}"
  echo -e "${Gray} Creando archivo de configuración MySQL ${Color_Off}"
  echo -e "${Gray}============================================================${Color_Off}"
  echo ""

  {
    echo "[client]"
    echo "user=\"$DB_USER\""
    [[ -n "$DB_PASSWORD" ]] && echo "password=\"$DB_PASSWORD\""
    echo "host=\"$DB_HOST\""
    echo "port=$DB_PORT"
  } >"$PATH_CONFIG_MYSQL" || {
    msg "Error: No se pudo crear el archivo de configuración MySQL" "ERROR"
    exit 1
  }

  msg "Archivo de configuración creado: $PATH_CONFIG_MYSQL" "SUCCESS"
  sleep 2
}

# =============================================================================
# SECTION: Menú Principal
# =============================================================================

show_menu() {
  while true; do
    clear
    echo -e "${Blue}============================================================${Color_Off}"
    echo -e "${Blue} ADMINISTRACIÓN WORDPRESS v3.2.0 ${Color_Off}"
    echo -e "${Blue} -----------------------------------------------------------${Color_Off}"
    echo -e "${Blue} Sistema: ${SO_SYSTEM}${Color_Off}"
    echo -e "${Blue} Usuario: ${CURRENT_USER}${Color_Off}"
    echo -e "${Blue} Directorio actual: ${CURRENT_DIR}${Color_Off}"
    echo -e "${Blue} Directorio raíz: ${ROOT_PATH}${Color_Off}"
    echo -e "${Blue} Directorio WordPress: ${PATH_DOMAIN}${Color_Off}"
    echo -e "${Blue}============================================================${Color_Off}"

    print_menu_option() {
      local idx="$1"
      local text="$2"
      printf "${Yellow}●${Color_Off} ${Green}%2d)${Color_Off} %s\n" "$idx" "$text"
    }

    print_menu_option 1  "Test de Ficheros"
    print_menu_option 2  "Nueva instalación de WordPress - ${VERSION_WP}"
    print_menu_option 3  "Descargar WP-CLI"
    print_menu_option 4  "Backup de archivos de WordPress"
    print_menu_option 5  "Backup Completo Web, Files y DB"
    print_menu_option 6  "Backup de base de datos"
    print_menu_option 7  "Restaurar backup de base de datos"
    print_menu_option 8  "(WP-CLI) Mantenimiento WordPress"
    print_menu_option 9  "(WP-CLI) Instalar plugin"
    print_menu_option 10 "(WP-CLI) Desactivar plugin"
    print_menu_option 11 "(WP-CLI) Listar plugins instalados"
    print_menu_option 12 "(WP-CLI) Verificar instalación de WordPress"
    print_menu_option 13 "(WP-CLI) Actualizar plugins y temas"
    print_menu_option 14 "Crear (o recrear) base de datos WordPress"
    print_menu_option 15 "Reparar Action Scheduler (WooCommerce)"
    print_menu_option 16 "Ver Variables de configuración"
    print_menu_option 17 "Ver php.ini configuración"
    print_menu_option 18 "Ver permisos de usuario MySQL"
    print_menu_option 19 "(WP-CLI) Vaciar caché de WordPress"
    print_menu_option 20 "Comandos Favoritos - WP_CLI"
    print_menu_option 21 "Salir"

    echo -e "${Blue}============================================================${Color_Off}"
    printf "${Cyan}Seleccione una opción [1-21 | x para salir]: ${Color_Off}"

    read -r opt

    case $opt in
      1)
        clear
        echo -e "${Green}Test de ficheros...${Color_Off}"
        test_script
        ;;
      2)
        clear
        echo -e "${Green}Instalando WordPress...${Color_Off}"
        fn_download_wordpress
        ;;
      3)
        clear
        echo -e "${Green}Descargando WP-CLI...${Color_Off}"
        fn_download_wp_cli
        ;;
      4)
        clear
        echo -e "${Green}Creando backup de archivos...${Color_Off}"
        fn_backup_wordpress
        ;;
      5)
        clear
        echo -e "${Green}Creando backup completo...${Color_Off}"
        fn_backup_completo_wordpress
        ;;
      6)
        clear
        echo -e "${Green}Creando backup de base de datos...${Color_Off}"
        fn_backup_wordpress_db
        ;;
      7)
        clear
        echo -e "${Green}Restaurando base de datos...${Color_Off}"
        fn_restore_wordpress_db
        ;;
      8)
        clear
        echo -e "${Green}Mantenimiento WordPress WP-CLI...${Color_Off}"
        fn_manteniment_wp_cli
        ;;
      9)
        clear
        echo -e "${Green}Instalando plugin...${Color_Off}"
        fn_install_plugin
        ;;
      10)
        clear
        echo -e "${Green}Desactivando plugin...${Color_Off}"
        fn_deactivate_plugin
        ;;
      11)
        clear
        echo -e "${Green}Listando plugins...${Color_Off}"
        fn_list_plugins
        ;;
      12)
        clear
        echo -e "${Green}Verificando instalación de WordPress...${Color_Off}"
        fn_check_site_status
        ;;
      13)
        clear
        echo -e "${Green}Actualizando plugins y temas...${Color_Off}"
        fn_update_all
        ;;
      14)
        clear
        echo -e "${Green}Creando base de datos WordPress...${Color_Off}"
        fn_create_wordpress_db
        ;;
      15)
        clear
        echo -e "${Green}Reparando Action Scheduler...${Color_Off}"
        fn_fix_action_scheduler
        ;;
      16)
        clear
        echo -e "${Green}Variables de configuración...${Color_Off}"
        view_vars_config && pause_continue
        ;;
      17)
        clear
        echo -e "${Green}Configuración de PHP...${Color_Off}"
        fn_info_php
        ;;
      18)
        clear
        echo -e "${Green}Permisos de usuario MySQL...${Color_Off}"
        fn_check_mysql_permissions
        ;;
      19)
        clear
        echo -e "${Green}Vaciando caché de WordPress...${Color_Off}"
        fn_flush_cache
        ;;
      20)
        clear
        echo -e "${Green}Comandos Favoritos...${Color_Off}"
        favorites
        ;;
      21 | x | X)
        clear
        echo -e "${Red}Saliendo del programa...${Color_Off}"
        exit 0
        ;;
      *)
        echo -e "${Red}Opción inválida. Intente nuevamente.${Color_Off}"
        sleep 1
        ;;
    esac
  done
}

# =============================================================================
# SECTION: Main - Ejecución Principal
# =============================================================================

main() {
  # Mostrar banner
  my_banner

  # Inicializar script
  initialize_script

  # Configurar base de datos
  configure_database

  # Configurar PHP
  configure_php

  # Configurar MySQL
  configure_mysql

  # Validar rutas del sistema
  validate_paths_system

  # Configurar SSL de MySQL
  configure_mysql_ssl

  # Crear archivo de configuración MySQL
  create_mysql_config_file

  # Mostrar menú principal
  show_menu
}

# Ejecutar el script
main

log_to_file "INFO" "========== FIN DE EJECUCIÓN =========="
msg "📝 Revisa el log en: ${LOG_FILE}" "INFO"