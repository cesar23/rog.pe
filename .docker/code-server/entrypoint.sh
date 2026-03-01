#!/bin/bash
set -e

echo "╔══════════════════════════════════════════════════════════════╗"
echo "║          🚀 code-server - LiteSpeed Stack                   ║"
echo "║             Usuario: nobody (65534:65534)                    ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# Verificar usuario actual
CURRENT_USER=$(whoami)
CURRENT_UID=$(id -u)
CURRENT_GID=$(id -g)

echo "👤 Usuario: $CURRENT_USER (UID: $CURRENT_UID, GID: $CURRENT_GID)"
echo "📁 Workspace: /workspace"
echo "🌐 URL: http://localhost:8443"
echo "🔑 Password: ${PASSWORD:-cesar203}"
echo ""

# Verificar permisos del workspace
if [ -d "/workspace/www" ]; then
    OWNER=$(stat -c '%U:%G' /workspace/www 2>/dev/null || echo "unknown")
    echo "📂 Owner de www: $OWNER"
    
    if [ "$OWNER" = "nobody:nogroup" ]; then
        echo "✅ Permisos correctos"
    else
        echo "⚠️  Advertencia: Los archivos deben pertenecer a nobody:nogroup"
    fi
fi

echo ""
echo "🎉 Iniciando code-server..."
echo ""

# Iniciar code-server
exec code-server \
    --bind-addr 0.0.0.0:8080 \
    --auth password \
    --user-data-dir /home/nobody/.local/share/code-server \
    --config /home/nobody/.config/code-server/config.yaml \
    "$@"