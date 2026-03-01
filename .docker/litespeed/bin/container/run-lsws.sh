#!/bin/bash
set -euo pipefail

if [ -x /usr/local/lsws/bin/lswsctrl ]; then
  /usr/local/lsws/bin/lswsctrl start || true
else
  /usr/local/lsws/bin/lshttpd || true
fi

echo "OpenLiteSpeed iniciado."
tail -F /usr/local/lsws/logs/error.log /usr/local/lsws/logs/access.log || true
