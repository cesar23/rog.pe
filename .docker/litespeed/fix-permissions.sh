#!/bin/bash
# Script para corregir permisos de archivos de configuración
# Se ejecuta al iniciar el contenedor

set -euo pipefail

echo "🔧 Configurando permisos de archivos de configuración..."

# Crear directorios si no existen
mkdir -p /usr/local/lsws/conf/vhosts
mkdir -p /var/www/vhosts

# Configurar permisos para archivos de configuración
chown -R lsadm:lsadm /usr/local/lsws/conf/ 2>/dev/null || true
chmod -R 755 /usr/local/lsws/conf/ 2>/dev/null || true

# Configurar permisos para sitios web
chown -R lsadm:lsadm /var/www/vhosts/ 2>/dev/null || true
chmod -R 755 /var/www/vhosts/ 2>/dev/null || true

echo "✅ Permisos configurados correctamente"
