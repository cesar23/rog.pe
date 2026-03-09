#!/usr/bin/env bash

# =============================================================================
# 🏆 SECTION: Configuración Inicial
# =============================================================================

DATE_HOUR=$(date "+%Y-%m-%d_%H:%M:%S")
DATE_HOUR_PE=$(date -u -d "-5 hours" "+%Y-%m-%d_%H:%M:%S" 2>/dev/null || TZ="America/Lima" date "+%Y-%m-%d_%H:%M:%S" 2>/dev/null || echo "$DATE_HOUR")
CURRENT_USER=$(id -un)
CURRENT_PC_NAME=$(hostname)
MY_INFO="${CURRENT_USER}@${CURRENT_PC_NAME}"
PATH_SCRIPT=$(readlink -f "${BASH_SOURCE:-$0}")
SCRIPT_NAME=$(basename "$PATH_SCRIPT")
CURRENT_DIR=$(dirname "$PATH_SCRIPT")
ROOT_PATH=$(realpath -m "${CURRENT_DIR}")

# Stacks a levantar en orden (rutas relativas desde ROOT_PATH)
STACKS=(
  "filebrowser"
  "heimdall"
  "portainer"
  "monitoreo"
)

# =============================================================================
# 🎨 SECTION: Colores para su uso
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
# ⚙️ SECTION: Core Functions
# =============================================================================

msg() {
  local message="$1"
  local level="${2:-OTHER}"
  case "$level" in
    INFO)    echo -e "${BBlue}${message}${Color_Off}" ;;
    WARNING) echo -e "${BYellow}${message}${Color_Off}" ;;
    DEBUG)   echo -e "${BPurple}${message}${Color_Off}" ;;
    ERROR)   echo -e "${BRed}${message}${Color_Off}" ;;
    SUCCESS) echo -e "${BGreen}${message}${Color_Off}" ;;
    *)       echo -e "${BGray}${message}${Color_Off}" ;;
  esac
}

msg_time() {
  local message="$1"
  local level="${2:-OTHER}"
  local timestamp
  timestamp=$(date -u -d "-5 hours" "+%Y-%m-%d_%H:%M:%S" 2>/dev/null \
    || TZ="America/Lima" date "+%Y-%m-%d_%H:%M:%S" 2>/dev/null \
    || date "+%Y-%m-%d_%H:%M:%S")
  case "$level" in
    INFO)    echo -e "${timestamp} ${BBlue}${message}${Color_Off}" ;;
    WARNING) echo -e "${timestamp} ${BYellow}${message}${Color_Off}" ;;
    DEBUG)   echo -e "${timestamp} ${BPurple}${message}${Color_Off}" ;;
    ERROR)   echo -e "${timestamp} ${BRed}${message}${Color_Off}" ;;
    SUCCESS) echo -e "${timestamp} ${BGreen}${message}${Color_Off}" ;;
    *)       echo -e "${timestamp} ${BGray}${message}${Color_Off}" ;;
  esac
}

# ==============================================================================
# 📝 Función: print_header
# Descripción: Imprime una cabecera decorativa para el script
# ==============================================================================
print_header() {
  echo ""
  echo -e "${BCyan}╔══════════════════════════════════════════════════════════╗${Color_Off}"
  echo -e "${BCyan}║          🚀  START ALL — project: util                   ║${Color_Off}"
  echo -e "${BCyan}╚══════════════════════════════════════════════════════════╝${Color_Off}"
  echo -e "  ${BGray}Usuario : ${BWhite}${MY_INFO}${Color_Off}"
  echo -e "  ${BGray}Fecha   : ${BWhite}${DATE_HOUR_PE}${Color_Off}"
  echo -e "  ${BGray}Stacks  : ${BWhite}${#STACKS[@]}${Color_Off}"
  echo ""
}

# ==============================================================================
# 📝 Función: print_separator
# Descripción: Imprime una línea separadora
# ==============================================================================
print_separator() {
  echo -e "${BGray}──────────────────────────────────────────────────────────${Color_Off}"
}

# ==============================================================================
# 📝 Función: check_docker
# Descripción: Verifica que Docker esté disponible y corriendo
# ==============================================================================
check_docker() {
  if ! command -v docker &>/dev/null; then
    msg "❌ Docker no está instalado o no está en el PATH." "ERROR"
    exit 1
  fi
  if ! docker info &>/dev/null; then
    msg "❌ El daemon de Docker no está corriendo." "ERROR"
    exit 1
  fi
  msg "✅ Docker disponible y corriendo." "SUCCESS"
}

# ==============================================================================
# 📝 Función: start_stack
# Descripción: Levanta un stack de Docker Compose dado su nombre de carpeta
# Parámetros:
#   $1 - Nombre del stack (carpeta relativa a ROOT_PATH)
# ==============================================================================
start_stack() {
  local stack="$1"
  local stack_path="${ROOT_PATH}/${stack}"

  print_separator
  msg_time "▶️  Arrancando stack: ${stack}" "INFO"

  if [ ! -d "$stack_path" ]; then
    msg "   ⚠️  Carpeta no encontrada: ${stack_path}" "WARNING"
    return 1
  fi

  if [ ! -f "${stack_path}/docker-compose.yml" ]; then
    msg "   ⚠️  No se encontró docker-compose.yml en: ${stack_path}" "WARNING"
    return 1
  fi

  cd "$stack_path" || { msg "   ❌ No se pudo acceder a: ${stack_path}" "ERROR"; return 1; }

  if docker compose up -d 2>&1; then
    msg_time "   ✅ Stack [${stack}] levantado correctamente." "SUCCESS"
  else
    msg_time "   ❌ Error al levantar el stack [${stack}]." "ERROR"
    cd "$ROOT_PATH"
    return 1
  fi

  cd "$ROOT_PATH"
}

# ==============================================================================
# 📝 Función: show_status
# Descripción: Muestra el estado final de todos los contenedores del proyecto util
# ==============================================================================
show_status() {
  print_separator
  msg "📋 Estado final de los contenedores [project=util]:" "INFO"
  echo ""
  docker ps --filter "label=project=util" \
    --format "  ${Green}▸${Color_Off} {{.Names}}\t{{.Status}}\t{{.Ports}}" \
  | column -t
  echo ""
}

# =============================================================================
# 🔥 SECTION: Main Code
# =============================================================================

clear
print_header

check_docker

ERRORS=0

for stack in "${STACKS[@]}"; do
  start_stack "$stack" || ((ERRORS++))
done

echo ""
show_status
print_separator

if [ "$ERRORS" -eq 0 ]; then
  echo ""
  echo -e "${BGreen}"
  echo "  ███████ ████████  █████  ██████  ████████     ██   ██ ██████  "
  echo "  ██         ██    ██   ██ ██   ██    ██        ██   ██ ██   ██ "
  echo "  ███████    ██    ███████ ██████     ██        ██   ██ ██████  "
  echo "       ██    ██    ██   ██ ██   ██    ██        ██   ██ ██      "
  echo "  ███████    ██    ██   ██ ██   ██    ██         █████  ██      "
  echo ""
  echo -e "${Color_Off}"
  msg_time "🎉 Todos los stacks arrancaron correctamente." "SUCCESS"
else
  echo ""
  msg_time "⚠️  Proceso completado con ${ERRORS} error(es). Revisa los logs." "WARNING"
  msg "   docker compose logs -f  (desde la carpeta del stack)" "DEBUG"
fi

echo ""
exit 0