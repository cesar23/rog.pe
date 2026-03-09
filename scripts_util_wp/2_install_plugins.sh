#!/usr/bin/env bash

# =============================================================================
# Script: install_plugins.sh
# Descripción: Instala y activa plugins de WordPress
# Ubicación: (raíz de WordPress) — ejecutar desde dentro del contenedor
# Uso: bash install_plugins.sh
# Versión: 1.1.0
# =============================================================================

set -euo pipefail

# =============================================================================
# SECTION: Colores y mensajería
# =============================================================================

Color_Off='\033[0m'
Gray='\033[0;90m'
Blue='\033[0;34m'
BRed='\033[1;31m'
BGreen='\033[1;32m'
BYellow='\033[1;33m'
BBlue='\033[1;34m'
BPurple='\033[1;35m'
BWhite='\033[1;37m'
BGray='\033[1;90m'

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

# =============================================================================
# SECTION: Plugins a instalar
# Agrega o comenta slugs según necesites
# =============================================================================

PLUGINS_INSTALAR=(
  "duplicate-post"                   # Yoast Duplicate Post
  "limit-login-attempts-reloaded"    # Limit Login Attempts Reloaded
  "litespeed-cache"                  # LiteSpeed Cache
  "woocommerce"                      # WooCommerce
  "wordfence"                        # Wordfence Security
  "wordpress-seo"                    # Yoast SEO (free)
  "wps-hide-login"                   # WPS Hide Login
  # "wp-mail-smtp-pro"               # PREMIUM — subir .zip manualmente
  # "wordpress-seo-premium"          # PREMIUM — subir .zip manualmente
)

# =============================================================================
# SECTION: Main
# =============================================================================

main() {
  echo ""
  echo -e "  ${BBlue}╔══════════════════════════════════════════════════╗${Color_Off}"
  echo -e "  ${BBlue}║${Color_Off}  ${BWhite}  INSTALL PLUGINS — rog.pe                    ${BBlue}║${Color_Off}"
  echo -e "  ${BBlue}╚══════════════════════════════════════════════════╝${Color_Off}"
  echo ""
  echo -e "  ${BPurple}  ⚠ Ejecutar dentro del contenedor Docker${Color_Off}"
  echo ""
  read -rp "  Presiona [ENTER] para continuar o Ctrl+C para cancelar..."
  echo ""

  # ── Detectar WP-CLI ──────────────────────────────────────────────────────────
  local WP_CLI=""

  if command -v wp >/dev/null 2>&1; then
    # wp global disponible (típico dentro de contenedor)
    WP_CLI="wp --allow-root"
  elif [[ -f "./wp-cli.phar" ]]; then
    # wp-cli.phar en el directorio actual (raíz de WordPress)
    WP_CLI="php wp-cli.phar --allow-root"
  else
    msg "WP-CLI no encontrado." "ERROR"
    msg "  Opciones:" "WARNING"
    msg "    1. Descarga: curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && chmod +x wp-cli.phar" "WARNING"
    msg "    2. Ejecuta desde dentro del contenedor donde wp esté disponible" "WARNING"
    exit 1
  fi

  msg "WP-CLI: ${WP_CLI}" "DEBUG"

  # ── Verificar que estamos en un WordPress ────────────────────────────────────
  if [[ ! -f "./wp-login.php" ]]; then
    msg "No se detectó WordPress en el directorio actual: $(pwd)" "ERROR"
    msg "Ejecuta el script desde la raíz de WordPress." "WARNING"
    exit 1
  fi

  msg "WordPress detectado en: $(pwd)" "SUCCESS"
  echo ""

  # ── Instalar plugins ─────────────────────────────────────────────────────────
  echo -e "${Blue}============================================================${Color_Off}"
  msg "Instalando plugins..." "INFO"
  echo -e "${Blue}============================================================${Color_Off}"

  local total=${#PLUGINS_INSTALAR[@]}
  local success_count=0
  local fail_count=0

  for plugin in "${PLUGINS_INSTALAR[@]}"; do
    echo ""
    echo -e "${Blue}------------------------------------------------------------${Color_Off}"
    msg "  📦 ${plugin}" "INFO"

    # Si ya está instalado, solo activar
    if $WP_CLI plugin is-installed "${plugin}" 2>/dev/null; then
      msg "  → Ya instalado — activando..." "WARNING"

      if $WP_CLI plugin activate "${plugin}" 2>&1 | while IFS= read -r line; do
           msg "    ${line}" "DEBUG"
         done; [[ "${PIPESTATUS[0]}" -eq 0 ]]; then
        msg "  ✔ ${plugin} activado" "SUCCESS"
        success_count=$(( success_count + 1 ))
      else
        msg "  ✘ ${plugin} — error al activar" "WARNING"
        fail_count=$(( fail_count + 1 ))
      fi
      continue
    fi

    # Instalar y activar
    if $WP_CLI plugin install "${plugin}" --activate 2>&1 | while IFS= read -r line; do
         msg "    ${line}" "DEBUG"
       done; [[ "${PIPESTATUS[0]}" -eq 0 ]]; then
      msg "  ✔ ${plugin} instalado y activado" "SUCCESS"
      success_count=$(( success_count + 1 ))
    else
      msg "  ✘ ${plugin} falló" "WARNING"
      fail_count=$(( fail_count + 1 ))
    fi
  done

  # ── Resumen ──────────────────────────────────────────────────────────────────
  echo ""
  echo -e "${Blue}============================================================${Color_Off}"
  msg "  Total    : ${total}" "INFO"
  msg "  ✔ Éxitos : ${success_count}" "SUCCESS"
  [[ $fail_count -gt 0 ]] && msg "  ✘ Fallos : ${fail_count}" "WARNING"
  echo -e "${Blue}============================================================${Color_Off}"

  if [[ $fail_count -gt 0 ]]; then
    echo ""
    msg "  Plugins PREMIUM — instalar manualmente desde wp-admin › Plugins › Subir plugin:" "WARNING"
    echo -e "  ${Gray}→ wp-mail-smtp-pro      : https://wpmailsmtp.com${Color_Off}"
    echo -e "  ${Gray}→ wordpress-seo-premium : https://yoast.com${Color_Off}"
  fi
  echo ""
}

main