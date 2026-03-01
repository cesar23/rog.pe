#!/bin/bash
set -e  # Detener script al primer error
# =============================================================================
# 🏆 SECTION: Configuración Inicial
# =============================================================================
# Establece la codificación a UTF-8 para evitar problemas con caracteres especiales.
export LC_ALL="es_ES.UTF-8"

# Fecha y hora actual en formato: YYYY-MM-DD_HH:MM:SS (hora local)
DATE_HOUR=$(date "+%Y-%m-%d_%H:%M:%S")
# Fecha y hora actual en Perú (UTC -5)
DATE_HOUR_PE=$(date -u -d "-5 hours" "+%Y-%m-%d_%H:%M:%S") # Fecha y hora actuales en formato YYYY-MM-DD_HH:MM:SS.
CURRENT_USER=$(id -un)             # Nombre del usuario actual.
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
Color_Off='\e[0m'       # Reset de color.
Black='\e[0;30m'        # Negro.
Red='\e[0;31m'          # Rojo.
Green='\e[0;32m'        # Verde.
Yellow='\e[0;33m'       # Amarillo.
Blue='\e[0;34m'         # Azul.
Purple='\e[0;35m'       # Púrpura.
Cyan='\e[0;36m'         # Cian.
White='\e[0;37m'        # Blanco.
Gray='\e[0;90m'         # Gris.

# Colores en Negrita
BBlack='\e[1;30m'       # Negro (negrita).
BRed='\e[1;31m'         # Rojo (negrita).
BGreen='\e[1;32m'       # Verde (negrita).
BYellow='\e[1;33m'      # Amarillo (negrita).
BBlue='\e[1;34m'        # Azul (negrita).
BPurple='\e[1;35m'      # Púrpura (negrita).
BCyan='\e[1;36m'        # Cian (negrita).
BWhite='\e[1;37m'       # Blanco (negrita).
BGray='\e[1;90m'        # Gris (negrita).


# =============================================================================
# 🔥 SECTION: Main Code
# =============================================================================



# ========================================
# Script de limpieza de tablas temporales
# para WordPress (WooCommerce, Wordfence, etc.)
# ========================================

DB_NAME="rog_web"
MYSQL_USER="rog_web"
#MYSQL_PASSWORD=$(cat /etc/cyberpanel/mysqlPassword)
MYSQL_PASSWORD="cesar203"

# Preguntar si usar SSL mode (solo compatible con MySQL, no con MariaDB)
echo ""
echo -e "${BCyan}🔐 ¿Deseas usar SSL mode?${Color_Off}"
echo -e "${Yellow}   Nota: MariaDB no soporta --ssl-mode=DISABLED${Color_Off}"
echo -e "${Yellow}   Solo selecciona 'sí' si estás usando MySQL${Color_Off}"
read -p "   [s/N]: " USE_SSL_MODE
USE_SSL_MODE=${USE_SSL_MODE:-N}

# Configurar opción SSL
SSL_OPTION=""
if [[ "$USE_SSL_MODE" =~ ^[sS]$ ]]; then
    SSL_OPTION="--ssl-mode=DISABLED"
    echo -e "${Green}✓ SSL mode será usado: --ssl-mode=DISABLED${Color_Off}"
else
    echo -e "${Green}✓ SSL mode no será usado (compatible con MariaDB)${Color_Off}"
fi
echo ""

# Función para vaciar una tabla si existe
truncate_if_exists() {
    local TABLE="$1"
    local DESCRIPCION="$2"

    # Construir comando base
    local MYSQL_CMD=("mysql")

    # Añadir opción SSL si está configurada
    if [[ -n "$SSL_OPTION" ]]; then
        MYSQL_CMD+=("$SSL_OPTION")
    fi

    MYSQL_CMD+=("-u" "$MYSQL_USER" "-D" "$DB_NAME")

    # Añadir contraseña si existe
    if [[ -n "$MYSQL_PASSWORD" ]]; then
        MYSQL_CMD+=("-p$MYSQL_PASSWORD")
    fi

    # Verificar si la tabla existe
    local EXISTS=$("${MYSQL_CMD[@]}" -sse "SHOW TABLES LIKE '$TABLE'")
    if [ "$EXISTS" == "$TABLE" ]; then
        echo -e "${BBlue}🧹 Vaciando tabla:${Color_Off} $TABLE → $DESCRIPCION"
        "${MYSQL_CMD[@]}" -e "TRUNCATE TABLE \`$TABLE\`;"
    else
        echo -e "${BYellow}⚠️  Tabla no encontrada:${Color_Off} $TABLE"
    fi
}


echo ""
echo "==========================================="
echo "🧼 Iniciando limpieza de tablas WordPress"
echo "==========================================="

# ------------------------
# 🔄 YITH WooCommerce Filter
# ------------------------
truncate_if_exists "wp_yith_wcan_filter_sessions" "Filtros de productos (YITH)"

# ------------------------
# 🛒 WooCommerce
# ------------------------
truncate_if_exists "wp_woocommerce_sessions" "Sesiones de clientes/carrito"

# ------------------------
# 🛡️ Wordfence (seguridad)
# ------------------------
wordfence_tables=(
  wp_wfhits wp_wftrafficrates wp_wflogins wp_wfsnipcache wp_wfissues
  wp_wfpendingissues wp_wfstatus wp_wfhoover wp_wfreversecache
  wp_wffilemods wp_wffilechanges wp_wfnotifications wp_wfauditevents
  wp_wfwaffailures wp_wfblockediplog wp_wfknownfilelist wp_wfsecurityevents
  wp_wfcrawlers wp_wflivetraffichuman
)

for TABLE in "${wordfence_tables[@]}"; do
  truncate_if_exists "$TABLE" "Wordfence (seguridad / logs)"
done

# ------------------------
# 🕓 Action Scheduler (WooCommerce)
# ------------------------
action_scheduler_tables=(
  wp_actionscheduler_actions wp_actionscheduler_claims
  wp_actionscheduler_logs wp_actionscheduler_groups
)

for TABLE in "${action_scheduler_tables[@]}"; do
  truncate_if_exists "$TABLE" "Action Scheduler (tareas programadas)"
done

# ------------------------
# 📋 WP Activity Log
# ------------------------
truncate_if_exists "wp_wsal_metadata" "Logs de actividad (WP Activity Log)"
truncate_if_exists "wp_wsal_occurrences" "Eventos registrados (WP Activity Log)"

# ------------------------
# 💬 Comentarios
# ------------------------
truncate_if_exists "wp_commentmeta" "Metadatos de comentarios"

# ------------------------
# 🖼️ Smush
# ------------------------
truncate_if_exists "wp_smush_dir_images" "Imágenes optimizadas (Smush)"

# ------------------------
# 📝 WPForms
# ------------------------
wpforms_tables=(
  wp_wpforms_entry_meta wp_wpforms_entries
  wp_wpforms_entry_fields wp_wpforms_tasks_meta
)

for TABLE in "${wpforms_tables[@]}"; do
  truncate_if_exists "$TABLE" "Entradas de formularios (WPForms)"
done

# ------------------------
# 📬 WP Mail SMTP y colas personalizadas
# ------------------------
truncate_if_exists "wp_wpmailsmtp_tasks_meta" "Tareas de correo (WP Mail SMTP)"
truncate_if_exists "wp_my_log" "Logs personalizados"
truncate_if_exists "wp_queue" "Cola personalizada"
truncate_if_exists "wp_failed_jobs" "Trabajos fallidos"

# ------------------------
# 🔍 Ajax Search for WooCommerce
# ------------------------
ajax_search_tables=(
  wp_dgwt_wcas_index wp_dgwt_wcas_var_index
  wp_dgwt_wcas_invindex_doclist wp_dgwt_wcas_invindex_info
  wp_dgwt_wcas_invindex_wordlist
)

for TABLE in "${ajax_search_tables[@]}"; do
  truncate_if_exists "$TABLE" "Índices de búsqueda (Ajax Search)"
done



echo ""
echo "================================================="
echo "🧽 Ejecutando limpieza avanzada de contenido..."
echo "================================================="

# Construir comando base
MYSQL_CMD_CLEAN=("mysql")

# Añadir opción SSL si está configurada
if [[ -n "$SSL_OPTION" ]]; then
    MYSQL_CMD_CLEAN+=("$SSL_OPTION")
fi

MYSQL_CMD_CLEAN+=("-u" "$MYSQL_USER" "-D" "$DB_NAME")

if [[ -n "$MYSQL_PASSWORD" ]]; then
    MYSQL_CMD_CLEAN+=("-p$MYSQL_PASSWORD")
fi


# Comentarios marcados como SPAM
"${MYSQL_CMD_CLEAN[@]}" -e "DELETE FROM wp_comments WHERE comment_approved = 'spam';"
echo "🗑️  Comentarios SPAM eliminados."

# Comentarios no aprobados
"${MYSQL_CMD_CLEAN[@]}" -e "DELETE FROM wp_comments WHERE comment_approved = '0';"
echo "🗑️  Comentarios no aprobados eliminados."

# Pingbacks y trackbacks
"${MYSQL_CMD_CLEAN[@]}" -e "DELETE FROM wp_comments WHERE comment_type = 'pingback';"
"${MYSQL_CMD_CLEAN[@]}" -e "DELETE FROM wp_comments WHERE comment_type = 'trackback';"
echo "🗑️  Pingbacks y Trackbacks eliminados."

# Revisiones de entradas
"${MYSQL_CMD_CLEAN[@]}" -e "
DELETE a,b,c
 FROM wp_posts a
 LEFT JOIN wp_term_relationships b ON ( a.ID = b.object_id)
 LEFT JOIN wp_postmeta c ON ( a.ID = c.post_id )
 LEFT JOIN wp_term_taxonomy d ON ( b.term_taxonomy_id = d.term_taxonomy_id)
 WHERE a.post_type = 'revision'
 AND d.taxonomy != 'link_category';"
echo "🗑️  Revisiones de entradas eliminadas."

# Etiquetas sin usar
"${MYSQL_CMD_CLEAN[@]}" -e "
DELETE FROM wp_terms WHERE term_id IN (SELECT term_id FROM wp_term_taxonomy WHERE count = 0);
DELETE FROM wp_term_taxonomy WHERE term_id NOT IN (SELECT term_id FROM wp_terms);
DELETE FROM wp_term_relationships WHERE term_taxonomy_id NOT IN (SELECT term_taxonomy_id FROM wp_term_taxonomy);"
echo "🏷️  Etiquetas sin uso eliminadas."

# Transients
"${MYSQL_CMD_CLEAN[@]}" -e "DELETE FROM wp_options WHERE option_name LIKE '%_transient_%';"
echo "🧼 Transients eliminados."

# Limpieza de tablas del plugin de seguridad AIOWPS
for T in wp_aiowps_failed_logins wp_aiowps_events wp_aiowps_global_meta wp_aiowps_login_activity wp_aiowps_login_lockdown; do
    truncate_if_exists "$T" "AIOWPS (seguridad)"
done

# Opcionalmente limpiar de nuevo logs de ActionScheduler si aún persisten
truncate_if_exists "wp_actionscheduler_logs" "ActionScheduler (logs finales)"

echo ""
echo "✅ Limpieza avanzada completada."


