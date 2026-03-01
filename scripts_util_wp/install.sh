#!/usr/bin/env bash

# =============================================================================
# Script: install.sh
# Descripción: Instalación post-clone del proyecto rog.pe (WordPress)
# Ubicación: scripts_util_wp/install.sh
# Uso: bash scripts_util_wp/install.sh
# Versión: 1.0.0
# =============================================================================

set -euo pipefail

export LC_ALL="C.UTF-8" 2>/dev/null || export LC_ALL="C"

# =============================================================================
# SECTION: Configuración Inicial
# =============================================================================

DATE_HOUR=$(date -u -d "-5 hours" "+%Y-%m-%d_%H:%M:%S" 2>/dev/null || TZ="America/Lima" date "+%Y-%m-%d_%H:%M:%S" 2>/dev/null || date "+%Y-%m-%d_%H:%M:%S")
CURRENT_USER=$(id -un)
CURRENT_PC_NAME=$(hostname)
PATH_SCRIPT=$(readlink -f "${BASH_SOURCE:-$0}" 2>/dev/null || realpath "${BASH_SOURCE:-$0}" 2>/dev/null || echo "$0")
SCRIPT_NAME=$(basename "$PATH_SCRIPT")
CURRENT_DIR=$(dirname "$PATH_SCRIPT")
NAME_DIR=$(basename "$CURRENT_DIR")
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

log_to_file() {
  local level="${1:-INFO}"
  local message="$2"
  local timestamp
  timestamp=$(date -u -d "-5 hours" "+%Y-%m-%d %H:%M:%S" 2>/dev/null || TZ="America/Lima" date "+%Y-%m-%d %H:%M:%S" 2>/dev/null || date "+%Y-%m-%d %H:%M:%S")

  mkdir -p "$(dirname "$LOG_FILE")" 2>/dev/null || true
  echo "[${timestamp}] [${level}] ${message}" >> "$LOG_FILE" 2>/dev/null || true
}

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

cleanup_temp_files() {
  : # noop - ampliar si se generan temporales
}

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
  msg "Usuario: ${CURRENT_USER}" "ERROR"
  msg "Directorio: ${CURRENT_DIR}" "ERROR"
  msg "=================================================" "ERROR"

  log_to_file "ERROR" "Error crítico en línea ${line_number} con código ${exit_code}: ${BASH_COMMAND:-N/A}"

  cleanup_temp_files
  exit "${exit_code}"
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
# SECTION: Utilidades
# =============================================================================

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

detect_system() {
  if [[ -f /data/data/com.termux/files/usr/bin/pkg ]]; then
    echo "termux"
  elif grep -q Microsoft /proc/version 2>/dev/null; then
    echo "wsl"
  elif [[ -f /etc/os-release ]]; then
    source /etc/os-release
    case $ID in
      ubuntu|debian) echo "ubuntu" ;;
      rhel|centos|fedora|rocky|almalinux) echo "redhat" ;;
      *) echo "unknown" ;;
    esac
  elif [[ -n "${MSYSTEM:-}" ]]; then
    echo "gitbash"
  else
    echo "unknown"
  fi
}

my_banner() {
  echo ""
  echo -e "  ${BRed}╔══════════════════════════════════════════════════╗${Color_Off}"
  echo -e "  ${BRed}║${Color_Off}  ${BWhite}██████╗  ██████╗  ██████╗      ██████╗ ███████╗${Color_Off}  ${BRed}║${Color_Off}"
  echo -e "  ${BRed}║${Color_Off}  ${BWhite}██╔══██╗██╔═══██╗██╔════╝     ██╔══██╗██╔════╝${Color_Off}  ${BRed}║${Color_Off}"
  echo -e "  ${BRed}║${Color_Off}  ${BWhite}██████╔╝██║   ██║██║  ███╗    ██████╔╝█████╗  ${Color_Off}  ${BRed}║${Color_Off}"
  echo -e "  ${BRed}║${Color_Off}  ${BWhite}██╔══██╗██║   ██║██║   ██║    ██╔═══╝ ██╔══╝  ${Color_Off}  ${BRed}║${Color_Off}"
  echo -e "  ${BRed}║${Color_Off}  ${BWhite}██║  ██║╚██████╔╝╚██████╔╝    ██║     ███████╗${Color_Off}  ${BRed}║${Color_Off}"
  echo -e "  ${BRed}║${Color_Off}  ${BWhite}╚═╝  ╚═╝ ╚═════╝  ╚═════╝     ╚═╝     ╚══════╝${Color_Off}  ${BRed}║${Color_Off}"
  echo -e "  ${BRed}║${Purple}        INSTALL POST-CLONE  -  rog.pe              ${BRed}║${Color_Off}"
  echo -e "  ${BRed}╚══════════════════════════════════════════════════╝${Color_Off}"
  echo ""
}

# =============================================================================
# SECTION: Configuración del proyecto
# =============================================================================

# ─── PLUGINS DE TERCEROS (los tuyos ya vienen del repo) ───
PLUGINS_TERCEROS=(
  "woocommerce"
  "wordfence"
  "wps-hide-login"
  "limit-login-attempts-reloaded"
  "loco-translate"
  "litespeed-cache"
  "contact-form-7"
  # Agrega más plugins de terceros aquí
)

# ─── TEMAS DE TERCEROS (flatsome es premium - ver paso manual) ───
THEMES_TERCEROS=(
  "twentytwentyfour"
)

# =============================================================================
# SECTION: Paso 1 — Cargar .env.development
# =============================================================================

step_load_env() {
  echo ""
  echo -e "${Blue}============================================================${Color_Off}"
  msg "PASO 1/6 — Verificando .env.development" "INFO"
  echo -e "${Blue}============================================================${Color_Off}"

  local root_project
  root_project=$(realpath "${CURRENT_DIR}/..")
  PATH_ENV="${root_project}/.env.development"

  if [[ ! -f "$PATH_ENV" ]]; then
    local path_example="${root_project}/.env.example"
    if [[ -f "$path_example" ]]; then
      msg "No existe .env.development — copiando desde .env.example..." "WARNING"
      cp "$path_example" "$PATH_ENV"
      msg "Edita el archivo con tus credenciales: ${PATH_ENV}" "WARNING"
      echo ""
      read -p "¿Ya editaste .env.development? (s/N): " confirm
      [[ "${confirm,,}" != "s" ]] && {
        msg "Edita .env.development primero y vuelve a ejecutar." "ERROR"
        exit 1
      }
    else
      msg "No existe .env.development ni .env.example en: ${root_project}" "ERROR"
      exit 1
    fi
  fi

  # Leer variables necesarias
  APACHE_PUBLIC_ROOT=$(find_env 'APACHE_PUBLIC_ROOT' "$PATH_ENV")
  DB_HOST=$(find_env 'MYSQL_HOST' "$PATH_ENV")
  DB_PORT=$(find_env 'MYSQL_PORT' "$PATH_ENV")
  DB_USER=$(find_env 'MYSQL_USER_ROOT' "$PATH_ENV")
  DB_PASSWORD=$(find_env 'MARIADB_ROOT_PASSWORD' "$PATH_ENV")
  DB_NAME=$(find_env 'MARIADB_DATABASE' "$PATH_ENV")

  # Fallback de password si no viene de ROOT
  [[ -z "$DB_PASSWORD" ]] && DB_PASSWORD=$(find_env 'MYSQL_ROOT_PASSWORD' "$PATH_ENV")
  [[ -z "$DB_HOST" ]]     && DB_HOST="localhost"
  [[ -z "$DB_PORT" ]]     && DB_PORT="3306"

  # Validar variables críticas
  [[ -z "$APACHE_PUBLIC_ROOT" ]] && { msg "APACHE_PUBLIC_ROOT no definido en .env.development" "ERROR"; exit 1; }
  [[ -z "$DB_NAME" ]]            && { msg "MARIADB_DATABASE no definido en .env.development" "ERROR"; exit 1; }
  [[ -z "$DB_USER" ]]            && { msg "MYSQL_USER_ROOT no definido en .env.development" "ERROR"; exit 1; }

  # Calcular rutas
  ROOT_PATH=$(realpath -m "${CURRENT_DIR}/..")
  WP_DIR="${ROOT_PATH}/www/html/${APACHE_PUBLIC_ROOT}"
  WP_LANG="es_ES"

  log_to_file "INFO" ".env.development cargado correctamente"
  msg ".env.development cargado correctamente" "SUCCESS"

  echo ""
  echo -e "  ${Gray}APACHE_PUBLIC_ROOT : ${APACHE_PUBLIC_ROOT}${Color_Off}"
  echo -e "  ${Gray}WP_DIR             : ${WP_DIR}${Color_Off}"
  echo -e "  ${Gray}DB_HOST            : ${DB_HOST}:${DB_PORT}${Color_Off}"
  echo -e "  ${Gray}DB_NAME            : ${DB_NAME}${Color_Off}"
  echo -e "  ${Gray}DB_USER            : ${DB_USER}${Color_Off}"
  echo ""
  sleep 1
}

# =============================================================================
# SECTION: Paso 2 — Instalar WP-CLI
# =============================================================================

step_install_wpcli() {
  echo ""
  echo -e "${Blue}============================================================${Color_Off}"
  msg "PASO 2/6 — Verificando WP-CLI" "INFO"
  echo -e "${Blue}============================================================${Color_Off}"

  local wp_phar="${WP_DIR}/wp-cli.phar"

  if [[ ! -f "$wp_phar" ]]; then
    msg "Descargando WP-CLI en ${WP_DIR}..." "INFO"

    mkdir -p "$WP_DIR"
    cd "$WP_DIR" || { msg "No se pudo acceder a: ${WP_DIR}" "ERROR"; exit 1; }

    if curl -sSLO https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar 2>/dev/null; then
      chmod +x wp-cli.phar
      msg "WP-CLI descargado exitosamente" "SUCCESS"
    else
      msg "Error: No se pudo descargar WP-CLI — verifica tu conexión" "ERROR"
      exit 1
    fi
  else
    msg "WP-CLI ya existe en: ${wp_phar}" "SUCCESS"
  fi

  # Detectar PHP
  local php_bin
  php_bin=$(command -v php 2>/dev/null || echo "php")
  WP_CLI="${php_bin} ${WP_DIR}/wp-cli.phar --path=${WP_DIR} --allow-root"

  msg "PHP: $(${php_bin} --version 2>/dev/null | head -1)" "DEBUG"
  log_to_file "INFO" "WP-CLI configurado: ${WP_DIR}/wp-cli.phar"
  sleep 1
}

# =============================================================================
# SECTION: Paso 3 — Descargar WordPress Core
# =============================================================================

step_download_wp() {
  echo ""
  echo -e "${Blue}============================================================${Color_Off}"
  msg "PASO 3/6 — Descargando WordPress Core" "INFO"
  echo -e "${Blue}============================================================${Color_Off}"

  if [[ -f "${WP_DIR}/wp-login.php" ]]; then
    msg "WordPress core ya existe en ${WP_DIR} — omitiendo descarga" "WARNING"
    log_to_file "INFO" "WP core ya existía, omitido"
    return 0
  fi

  mkdir -p "$WP_DIR"
  cd "$WP_DIR" || { msg "No se pudo acceder a: ${WP_DIR}" "ERROR"; exit 1; }

  msg "Descargando WordPress (${WP_LANG})..." "INFO"

  if $WP_CLI core download \
      --locale="${WP_LANG}" \
      --skip-content 2>/dev/null; then

    msg "WordPress core descargado correctamente" "SUCCESS"
    log_to_file "SUCCESS" "WordPress core descargado"
  else
    msg "Error al descargar WordPress core" "ERROR"
    log_to_file "ERROR" "Falló descarga de WordPress core"
    exit 1
  fi
  sleep 1
}

# =============================================================================
# SECTION: Paso 4 — Crear wp-config.php
# =============================================================================

step_create_wpconfig() {
  echo ""
  echo -e "${Blue}============================================================${Color_Off}"
  msg "PASO 4/6 — Creando wp-config.php" "INFO"
  echo -e "${Blue}============================================================${Color_Off}"

  local config_file="${WP_DIR}/wp-config.php"

  if [[ -f "$config_file" ]]; then
    msg "wp-config.php ya existe — omitiendo" "WARNING"
    log_to_file "INFO" "wp-config.php ya existía, omitido"
    return 0
  fi

  local sample_file="${WP_DIR}/wp-config-sample.php"

  if validate_file "$sample_file" "wp-config-sample.php"; then
    cp "$sample_file" "$config_file" || { msg "No se pudo copiar wp-config-sample.php" "ERROR"; exit 1; }

    sed -i "s/database_name_here/${DB_NAME}/g"   "$config_file"
    sed -i "s/username_here/${DB_USER}/g"         "$config_file"
    sed -i "s/password_here/${DB_PASSWORD}/g"     "$config_file"
    sed -i "s/localhost/${DB_HOST}/g"             "$config_file"

    msg "wp-config.php creado desde wp-config-sample.php" "SUCCESS"
    log_to_file "SUCCESS" "wp-config.php creado"
  else
    # Fallback: usar WP-CLI
    msg "wp-config-sample.php no encontrado — usando WP-CLI..." "WARNING"

    if $WP_CLI config create \
        --dbname="${DB_NAME}" \
        --dbuser="${DB_USER}" \
        --dbpass="${DB_PASSWORD}" \
        --dbhost="${DB_HOST}" \
        --locale="${WP_LANG}" 2>/dev/null; then

      msg "wp-config.php creado con WP-CLI" "SUCCESS"
      log_to_file "SUCCESS" "wp-config.php creado via WP-CLI"
    else
      msg "No se pudo crear wp-config.php" "ERROR"
      log_to_file "ERROR" "Falló creación de wp-config.php"
      exit 1
    fi
  fi
  sleep 1
}

# =============================================================================
# SECTION: Paso 5 — Instalar Plugins de Terceros
# =============================================================================

step_install_plugins() {
  echo ""
  echo -e "${Blue}============================================================${Color_Off}"
  msg "PASO 5/6 — Instalando plugins de terceros" "INFO"
  echo -e "${Blue}============================================================${Color_Off}"

  cd "$WP_DIR" || { msg "No se pudo acceder a: ${WP_DIR}" "ERROR"; exit 1; }

  local success_count=0
  local total=${#PLUGINS_TERCEROS[@]}

  for plugin in "${PLUGINS_TERCEROS[@]}"; do
    echo ""
    msg "  📦 Instalando: ${plugin}..." "INFO"

    if $WP_CLI plugin install "${plugin}" --activate 2>/dev/null; then
      msg "  ${plugin} instalado y activado" "SUCCESS"
      log_to_file "SUCCESS" "Plugin instalado: ${plugin}"
      ((success_count++))
    else
      msg "  ${plugin} falló (puede ser premium o nombre incorrecto)" "WARNING"
      log_to_file "WARNING" "Plugin falló: ${plugin}"
    fi
  done

  echo ""
  echo -e "${Blue}============================================================${Color_Off}"
  msg "Resumen plugins:" "INFO"
  msg "  Total   : ${total}" "INFO"
  msg "  Exitosos: ${success_count}" "SUCCESS"
  msg "  Fallos  : $((total - success_count))" "WARNING"
  echo -e "${Blue}============================================================${Color_Off}"
  sleep 1
}

# =============================================================================
# SECTION: Paso 6 — Instalar Temas Base y Permisos
# =============================================================================

step_install_themes_and_perms() {
  echo ""
  echo -e "${Blue}============================================================${Color_Off}"
  msg "PASO 6/6 — Temas base y permisos" "INFO"
  echo -e "${Blue}============================================================${Color_Off}"

  cd "$WP_DIR" || { msg "No se pudo acceder a: ${WP_DIR}" "ERROR"; exit 1; }

  for theme in "${THEMES_TERCEROS[@]}"; do
    msg "  🎨 Instalando tema: ${theme}..." "INFO"
    $WP_CLI theme install "${theme}" 2>/dev/null \
      && msg "  ${theme} instalado" "SUCCESS" \
      || msg "  ${theme} falló" "WARNING"
  done

  echo ""
  msg "Configurando permisos de archivos..." "INFO"
  find "${WP_DIR}" -type d -exec chmod 755 {} \; 2>/dev/null || true
  find "${WP_DIR}" -type f -exec chmod 644 {} \; 2>/dev/null || true
  msg "Permisos configurados" "SUCCESS"
  log_to_file "SUCCESS" "Permisos configurados correctamente"
  sleep 1
}

# =============================================================================
# SECTION: Resumen Final
# =============================================================================

show_summary() {
  echo ""
  echo -e "  ${BGreen}╔══════════════════════════════════════════════════╗${Color_Off}"
  echo -e "  ${BGreen}║${Color_Off}  ${BWhite}✅  INSTALACIÓN COMPLETADA CORRECTAMENTE        ${BGreen}║${Color_Off}"
  echo -e "  ${BGreen}╚══════════════════════════════════════════════════╝${Color_Off}"
  echo ""
  echo -e "  ${BYellow}📌 PASOS MANUALES PENDIENTES:${Color_Off}"
  echo ""
  echo -e "  ${BRed}1. FLATSOME (tema premium — no está en repos públicos):${Color_Off}"
  echo -e "     ${Gray}→ Sube el .zip desde: wp-admin › Apariencia › Temas › Subir tema${Color_Off}"
  echo -e "     ${Gray}→ O copia manualmente a: ${WP_DIR}/wp-content/themes/${Color_Off}"
  echo ""
  echo -e "  ${BBlue}2. Base de datos:${Color_Off}"
  echo -e "     ${Gray}→ Importa tu .sql si tienes un backup existente${Color_Off}"
  echo -e "     ${Gray}→ O instala WP en: http://localhost/wp-admin/install.php${Color_Off}"
  echo ""
  echo -e "  ${BBlue}3. Docker (si usas el stack local):${Color_Off}"
  echo -e "     ${Gray}→ docker-compose up -d${Color_Off}"
  echo ""
  echo -e "  ${BBlue}4. Acceder al panel:${Color_Off}"
  echo -e "     ${Gray}→ http://localhost/wp-admin${Color_Off}"
  echo ""
  echo -e "  ${BGray}📝 Log guardado en: ${LOG_FILE}${Color_Off}"
  echo ""

  log_to_file "INFO" "========== FIN DE INSTALACIÓN POST-CLONE =========="
}

# =============================================================================
# SECTION: Main
# =============================================================================

main() {
  my_banner

  log_to_file "INFO" "========== INICIO INSTALL POST-CLONE =========="
  log_to_file "INFO" "Usuario: ${CURRENT_USER} | PC: ${CURRENT_PC_NAME} | Fecha: ${DATE_HOUR}"

  step_load_env
  step_install_wpcli
  step_download_wp
  step_create_wpconfig
  step_install_plugins
  step_install_themes_and_perms
  show_summary
}

main