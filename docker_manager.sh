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

# El docker-compose.yml vive en la misma carpeta que el script
COMPOSE_FILE="${CURRENT_DIR}/docker-compose.yml"

# Entornos disponibles
ENV_OPTIONS=(".env" ".env.development" ".env.production")
SELECTED_ENV=""

# =============================================================================
# 🎨 SECTION: Colores
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

print_separator() {
  echo -e "${BGray}──────────────────────────────────────────────────────────${Color_Off}"
}

print_separator_thin() {
  echo -e "${Gray}  ·  ·  ·  ·  ·  ·  ·  ·  ·  ·  ·  ·  ·  ·  ·  ·  ·  · ${Color_Off}"
}

# ==============================================================================
# 📝 Función: print_header
# ==============================================================================
print_header() {
  clear
  echo ""
  echo -e "${BCyan}╔══════════════════════════════════════════════════════════╗${Color_Off}"
  echo -e "${BCyan}║         🐳  Docker Stack Manager                         ║${Color_Off}"
  echo -e "${BCyan}╚══════════════════════════════════════════════════════════╝${Color_Off}"
  echo -e "  ${BGray}Usuario : ${BWhite}${MY_INFO}${Color_Off}"
  echo -e "  ${BGray}Fecha   : ${BWhite}${DATE_HOUR_PE}${Color_Off}"
  echo -e "  ${BGray}Compose : ${BWhite}${COMPOSE_FILE}${Color_Off}"
  if [ -n "$SELECTED_ENV" ]; then
    echo -e "  ${BGray}Entorno : ${BGreen}${SELECTED_ENV}${Color_Off}"
  fi
  echo ""
}

# ==============================================================================
# 📝 Función: check_docker
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
}

# ==============================================================================
# 📝 Función: check_compose_file
# ==============================================================================
check_compose_file() {
  if [ ! -f "$COMPOSE_FILE" ]; then
    msg "❌ No se encontró docker-compose.yml en: ${CURRENT_DIR}" "ERROR"
    exit 1
  fi
}

# ==============================================================================
# 📝 Función: check_env_file
# ==============================================================================
check_env_file() {
  local env_file="${CURRENT_DIR}/${SELECTED_ENV}"
  if [ ! -f "$env_file" ]; then
    msg "❌ No se encontró el archivo de entorno: ${env_file}" "ERROR"
    return 1
  fi
  return 0
}

# ==============================================================================
# 📝 Función: run_compose
# Descripción: Ejecuta docker compose con el env-file seleccionado
# Parámetros:  $@ — argumentos extra para docker compose
# ==============================================================================
run_compose() {
  local env_file="${CURRENT_DIR}/${SELECTED_ENV}"
  msg_time "▸ docker compose --env-file ${SELECTED_ENV} $*" "DEBUG"
  echo ""
  docker compose --env-file "$env_file" "$@"
}

# ==============================================================================
# 📝 Función: show_status
# Descripción: Muestra el estado actual de los contenedores del stack
# ==============================================================================
show_status() {
  print_separator
  msg "📋 Estado actual del stack:" "INFO"
  echo ""
  cd "$CURRENT_DIR"
  local env_file="${CURRENT_DIR}/${SELECTED_ENV}"
  docker compose --env-file "$env_file" ps 2>/dev/null
  echo ""
}

# ==============================================================================
# 📝 Función: press_enter
# Descripción: Pausa y espera que el usuario presione Enter
# ==============================================================================
press_enter() {
  echo ""
  echo -e "${BGray}  Presiona Enter para volver al menú...${Color_Off}"
  read -r
}

# ==============================================================================
# 📝 Función: confirm
# Descripción: Pide confirmación al usuario antes de una acción destructiva
# Parámetros:  $1 — mensaje de confirmación
# Retorna:     0 si confirma, 1 si cancela
# ==============================================================================
confirm() {
  local message="${1:-¿Estás seguro?}"
  echo ""
  echo -e "${BYellow}  ⚠️  ${message}${Color_Off}"
  echo -e "${BGray}  Escribe ${BWhite}si${BGray} para continuar, cualquier otra cosa cancela: ${Color_Off}"
  read -r answer
  [[ "$answer" == "si" || "$answer" == "sí" || "$answer" == "SI" ]]
}

# =============================================================================
# 🎛️ SECTION: Menú — Selección de Entorno
# =============================================================================

menu_select_env() {
  print_header
  print_separator
  echo -e "  ${BWhite}🌍  Selecciona el entorno:${Color_Off}"
  echo ""
  local i=1
  for env in "${ENV_OPTIONS[@]}"; do
    local env_file="${CURRENT_DIR}/${env}"
    if [ -f "$env_file" ]; then
      echo -e "   ${BGreen}[$i]${Color_Off}  ${BWhite}${env}${Color_Off} ${BGreen}✓${Color_Off}"
    else
      echo -e "   ${BGray}[$i]${Color_Off}  ${Gray}${env}${Color_Off} ${BYellow}(no encontrado)${Color_Off}"
    fi
    ((i++))
  done
  echo ""
  echo -e "   ${BRed}[0]${Color_Off}  ${BWhite}Salir${Color_Off}"
  print_separator
  echo -e "  ${BGray}Opción: ${Color_Off}"
  read -r choice

  case "$choice" in
    0)
      msg "👋 Hasta luego." "SUCCESS"
      exit 0
      ;;
    *)
      local idx=$((choice - 1))
      if [ "$idx" -ge 0 ] && [ "$idx" -lt "${#ENV_OPTIONS[@]}" ]; then
        SELECTED_ENV="${ENV_OPTIONS[$idx]}"
        if ! check_env_file; then
          msg "⚠️  El archivo no existe, pero puedes continuar si lo creas antes de ejecutar." "WARNING"
          press_enter
        fi
      else
        msg "❌ Opción inválida." "ERROR"
        sleep 1
        menu_select_env
      fi
      ;;
  esac
}

# =============================================================================
# 🎛️ SECTION: Menú — Acciones
# =============================================================================

menu_actions() {
  while true; do
    print_header
    print_separator
    echo -e "  ${BWhite}⚡  Acciones disponibles  ${BGray}[ env: ${BGreen}${SELECTED_ENV}${BGray} ]${Color_Off}"
    echo ""
    echo -e "   ${BGreen}[1]${Color_Off}  ▶️  ${BWhite}Levantar${Color_Off}             ${Gray}up -d${Color_Off}"
    echo -e "   ${BRed}[2]${Color_Off}  ⏹️  ${BWhite}Parar${Color_Off}                ${Gray}down${Color_Off}"
    echo -e "   ${BYellow}[3]${Color_Off}  🔄  ${BWhite}Reiniciar${Color_Off}            ${Gray}down + up -d${Color_Off}"
    echo -e "   ${BCyan}[4]${Color_Off}  🏗️  ${BWhite}Recompilar y levantar${Color_Off} ${Gray}up --build -d${Color_Off}"
    echo -e "   ${BBlue}[5]${Color_Off}  📋  ${BWhite}Ver logs en vivo${Color_Off}     ${Gray}logs -f${Color_Off}"
    echo -e "   ${BPurple}[6]${Color_Off}  🔍  ${BWhite}Estado del stack${Color_Off}     ${Gray}ps${Color_Off}"
    echo -e "   ${BRed}[7]${Color_Off}  🗑️  ${BWhite}Parar + borrar datos${Color_Off} ${Gray}down -v  ⚠️${Color_Off}"
    print_separator_thin
    echo -e "   ${BGray}[8]${Color_Off}  🌍  ${BWhite}Cambiar entorno${Color_Off}"
    echo -e "   ${BGray}[0]${Color_Off}  🚪  ${BWhite}Salir${Color_Off}"
    print_separator
    echo -e "  ${BGray}Opción: ${Color_Off}"
    read -r action

    cd "$CURRENT_DIR" || exit 1

    case "$action" in

      1) # ▶️ up -d
        print_header
        msg_time "▶️  Levantando stack con [${SELECTED_ENV}]..." "INFO"
        print_separator
        run_compose up -d
        show_status
        press_enter
        ;;

      2) # ⏹️ down
        print_header
        msg_time "⏹️  Parando stack [${SELECTED_ENV}]..." "INFO"
        print_separator
        run_compose down
        show_status
        press_enter
        ;;

      3) # 🔄 Reiniciar
        print_header
        msg_time "🔄 Reiniciando stack [${SELECTED_ENV}]..." "INFO"
        print_separator
        msg "⏹️  Paso 1/2 — Parando..." "WARNING"
        run_compose down
        echo ""
        msg "▶️  Paso 2/2 — Levantando..." "INFO"
        run_compose up -d
        show_status
        press_enter
        ;;

      4) # 🏗️ up --build
        print_header
        msg_time "🏗️  Recompilando imágenes y levantando [${SELECTED_ENV}]..." "INFO"
        print_separator
        run_compose up --build -d
        show_status
        press_enter
        ;;

      5) # 📋 logs -f
        print_header
        msg_time "📋 Mostrando logs en vivo [${SELECTED_ENV}]..." "INFO"
        msg "   Pulsa ${BWhite}Ctrl+C${BGray} para salir de los logs." "DEBUG"
        print_separator
        echo ""
        run_compose logs -f
        press_enter
        ;;

      6) # 🔍 ps / status
        print_header
        show_status
        echo ""
        # Info extra: uso de recursos si hay contenedores corriendo
        local ids
        ids=$(docker compose --env-file "${CURRENT_DIR}/${SELECTED_ENV}" ps -q 2>/dev/null)
        if [ -n "$ids" ]; then
          print_separator
          msg "📊 Uso de recursos (CPU / RAM):" "INFO"
          echo ""
          # shellcheck disable=SC2086
          docker stats --no-stream $ids
        fi
        press_enter
        ;;

      7) # 🗑️ down -v
        print_header
        if confirm "Esto parará el stack Y borrará los volúmenes. ¿Continuar? (escribe: si)"; then
          print_separator
          msg_time "🗑️  Parando y borrando volúmenes [${SELECTED_ENV}]..." "WARNING"
          run_compose down -v
          msg_time "✅ Volúmenes eliminados." "SUCCESS"
        else
          msg "❌ Operación cancelada." "ERROR"
        fi
        press_enter
        ;;

      8) # 🌍 Cambiar entorno
        menu_select_env
        ;;

      0) # 🚪 Salir
        echo ""
        msg_time "👋 Hasta luego — ${MY_INFO}" "SUCCESS"
        echo ""
        exit 0
        ;;

      *)
        msg "❌ Opción inválida. Elige entre 0 y 8." "ERROR"
        sleep 1
        ;;
    esac
  done
}

# =============================================================================
# 🔥 SECTION: Main
# =============================================================================

check_docker
check_compose_file
menu_select_env
menu_actions