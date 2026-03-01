#!/usr/bin/env bash

# =============================================================================
# Script: validate_structure.sh
# Descripción: Valida que la estructura de carpetas del repo sea correcta
# Uso: bash scripts_util_wp/validate_structure.sh
# =============================================================================

set -euo pipefail

# =============================================================================
# Colores
# =============================================================================

Color_Off='\033[0m'
BGreen='\033[1;32m'
BRed='\033[1;31m'
BYellow='\033[1;33m'
BBlue='\033[1;34m'
BGray='\033[1;90m'

msg() {
  local message="$1"
  local level="${2:-OTHER}"
  case "$level" in
    INFO)    echo -e "${BBlue}${message}${Color_Off}" ;;
    WARNING) echo -e "${BYellow}${message}${Color_Off}" ;;
    ERROR)   echo -e "${BRed}${message}${Color_Off}" ;;
    SUCCESS) echo -e "${BGreen}${message}${Color_Off}" ;;
    *)       echo -e "${BGray}${message}${Color_Off}" ;;
  esac
}

# =============================================================================
# Carpetas y archivos OBLIGATORIOS en el repo
# =============================================================================

REQUIRED_DIRS=(
  "scripts_util_wp"
  "www/html"
  "www/html/public_html"
  "www/html/public_html/wp-content"
  "www/html/public_html/wp-content/themes/flatsome"
  "www/html/public_html/wp-content/themes/flatsome-child"
  "www/html/public_html/wp-content/plugins/solu-admin-utils"
  "www/html/public_html/wp-content/plugins/solu-currencies-exchange-rate"
  "www/html/public_html/wp-content/plugins/solu-generate-html"
  "www/html/public_html/wp-content/plugins/solu-product-logs"
  "www/html/public_html/api_rest_sync"
  "www/html/public_html/soluciones-tools"
)

REQUIRED_FILES=(
  ".gitignore"
  ".env.example"
  "docker-compose.yml"
  "scripts_util_wp/install.sh"
  "scripts_util_wp/validate_structure.sh"
  "www/html/public_html/wp-config-sample.php"
)

# Carpetas que NO deben existir en el repo (fueron ignoradas en .gitignore)
FORBIDDEN_DIRS=(
  "www/html/public_html/wp-admin"
  "www/html/public_html/wp-includes"
  "www/html/public_html/wp-content/uploads"
  "docs_devops"
  "project_structure_docs"
)

# =============================================================================
# Validación
# =============================================================================

ERRORS=0
WARNINGS=0

echo ""
msg "============================================================" "INFO"
msg "  VALIDACIÓN DE ESTRUCTURA DEL REPO - rog.pe" "INFO"
msg "============================================================" "INFO"
echo ""

# ── Verificar carpetas obligatorias ──
msg "📁 Verificando carpetas obligatorias..." "INFO"
echo ""

for dir in "${REQUIRED_DIRS[@]}"; do
  if [[ -d "$dir" ]]; then
    msg "  ✅  $dir" "SUCCESS"
  else
    msg "  ❌  $dir  ← NO EXISTE" "ERROR"
    ((ERRORS++))
  fi
done

echo ""

# ── Verificar archivos obligatorios ──
msg "📄 Verificando archivos obligatorios..." "INFO"
echo ""

for file in "${REQUIRED_FILES[@]}"; do
  if [[ -f "$file" ]]; then
    msg "  ✅  $file" "SUCCESS"
  else
    msg "  ❌  $file  ← NO EXISTE" "ERROR"
    ((ERRORS++))
  fi
done

echo ""

# ── Verificar que carpetas prohibidas NO existan ──
msg "🚫 Verificando carpetas que NO deben estar en el repo..." "INFO"
echo ""

for dir in "${FORBIDDEN_DIRS[@]}"; do
  if [[ -d "$dir" ]]; then
    msg "  ⚠️   $dir  ← EXISTE (debería estar en .gitignore)" "WARNING"
    ((WARNINGS++))
  else
    msg "  ✅  $dir  (ausente — correcto)" "SUCCESS"
  fi
done

echo ""

# ── Verificar que .env.development NO esté commiteado ──
msg "🔐 Verificando que .env.development no esté en el repo..." "INFO"
echo ""

if [[ -f ".env.development" ]]; then
  msg "  ⚠️   .env.development EXISTE en el directorio de trabajo" "WARNING"
  msg "      Asegúrate de que esté en .gitignore y no commiteado" "WARNING"
  ((WARNINGS++))
else
  msg "  ✅  .env.development no encontrado (correcto)" "SUCCESS"
fi

echo ""

# ── Resultado final ──
msg "============================================================" "INFO"

if [[ $ERRORS -eq 0 && $WARNINGS -eq 0 ]]; then
  msg "  🎉  TODO CORRECTO — Estructura válida" "SUCCESS"
  msg "============================================================" "INFO"
  exit 0
elif [[ $ERRORS -eq 0 ]]; then
  msg "  ⚠️   WARNINGS: ${WARNINGS} — Revisar advertencias" "WARNING"
  msg "============================================================" "INFO"
  exit 0   # warnings no rompen el pipeline
else
  msg "  ❌  ERRORES: ${ERRORS}  |  WARNINGS: ${WARNINGS}" "ERROR"
  msg "  El repo no tiene la estructura mínima requerida" "ERROR"
  msg "============================================================" "INFO"
  exit 1   # rompe el pipeline en GitHub Actions
fi