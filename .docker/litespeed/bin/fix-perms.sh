#!/bin/sh
# /usr/local/bin/fix-perms.sh
# fix-perms.sh - Corrige permisos para LiteSpeed + WordPress
# Uso: fix-perms.sh [ruta]

# =============================================================================
# 🎨 SECCIÓN: Colores para el Terminal
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

set -e

USER="nobody"
GROUP="nogroup"
TARGET="${1:-/var/www/default/public}"

if [ ! -d "$TARGET" ]; then
    echo "${Red}❌ Error:${Color_Off} $TARGET no es un directorio"
    exit 1
fi

echo "${Cyan}🔧 Corrigiendo permisos en:${Color_Off} ${White}$TARGET${Color_Off}"
echo "${Blue}👤 Usuario:${Color_Off} ${White}$USER:$GROUP${Color_Off}"

# Cambiar propietario
chown -R "$USER:$GROUP" "$TARGET"

# Permisos estándar
find "$TARGET" -type f -exec chmod 644 {} \;
find "$TARGET" -type d -exec chmod 755 {} \;

# Caso especial: wp-config.php (más restrictivo)
if [ -f "$TARGET/wp-config.php" ]; then
    chmod 600 "$TARGET/wp-config.php"
    echo "${Green}🔒 wp-config.php →${Color_Off} ${White}600${Color_Off}"
fi

# Directorios que WordPress necesita escribir
for dir in wp-content/uploads wp-content/cache wp-content/upgrade; do
    if [ -d "$TARGET/$dir" ]; then
        chmod 755 "$TARGET/$dir"
        echo "${Green}📁 $dir →${Color_Off} ${White}755${Color_Off}"
    fi
done

echo "${Green}✅ Permisos corregidos correctamente${Color_Off}"