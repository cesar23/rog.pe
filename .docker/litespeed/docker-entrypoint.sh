#!/bin/sh
set -e

echo "🚀 Iniciando servicios..."

# Iniciar SSH en puerto 2222
echo "🔐 Iniciando SSH en puerto 2222..."
/usr/sbin/sshd -D &


# Limpiar sockets y PIDs antiguos
rm -rf /tmp/lshttpd/*.sock \
    /usr/local/lsws/tmp/lshttpd/*.sock \
    /var/run/*.pid \
    /usr/local/lsws/logs/*.pid 2>/dev/null || true

# Asegurar directorios
mkdir -p /tmp/lshttpd/swap /usr/local/lsws/logs

# Ajustar permisos
chown -R lsadm:lsadm /usr/local/lsws/conf /usr/local/lsws/logs 2>/dev/null || true

# Iniciar OpenLiteSpeed en foreground
exec /usr/local/lsws/bin/openlitespeed -d
