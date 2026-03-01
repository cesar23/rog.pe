#!/bin/bash
# ===========================================
# Script de configuración PHP para LiteSpeed
# LiteSpeed Stack - Configuración optimizada
# ===========================================

echo "🔧 Configurando PHP para LiteSpeed..."

# Archivo principal de PHP
PHP_INI="/usr/local/lsws/lsphp82/etc/php/8.2/litespeed/php.ini"

# Backup del archivo original
cp "$PHP_INI" "$PHP_INI.backup.$(date +%Y%m%d_%H%M%S)"

# Configuraciones a aplicar
echo "📝 Aplicando configuraciones PHP..."

# Función para actualizar o agregar configuración
update_php_config() {
    local key="$1"
    local value="$2"
    local file="$3"
    
    if grep -q "^$key" "$file"; then
        # Actualizar valor existente
        sed -i "s/^$key.*/$key = $value/" "$file"
        echo "✅ Actualizado: $key = $value"
    else
        # Agregar nueva configuración
        echo "$key = $value" >> "$file"
        echo "➕ Agregado: $key = $value"
    fi
}

# Aplicar configuraciones
update_php_config "memory_limit" "512M" "$PHP_INI"
update_php_config "max_execution_time" "180" "$PHP_INI"
update_php_config "max_input_time" "180" "$PHP_INI"
update_php_config "upload_max_filesize" "128M" "$PHP_INI"
update_php_config "post_max_size" "128M" "$PHP_INI"
update_php_config "max_input_vars" "5000" "$PHP_INI"
update_php_config "max_file_uploads" "20" "$PHP_INI"
update_php_config "date.timezone" "\"America/Lima\"" "$PHP_INI"
update_php_config "display_errors" "Off" "$PHP_INI"
update_php_config "log_errors" "On" "$PHP_INI"
update_php_config "error_log" "/usr/local/lsws/logs/php_errors.log" "$PHP_INI"
update_php_config "expose_php" "Off" "$PHP_INI"
update_php_config "allow_url_fopen" "On" "$PHP_INI"
update_php_config "allow_url_include" "Off" "$PHP_INI"
update_php_config "default_socket_timeout" "60" "$PHP_INI"

# Configuraciones de sesión
update_php_config "session.gc_maxlifetime" "1440" "$PHP_INI"
update_php_config "session.cookie_lifetime" "0" "$PHP_INI"
update_php_config "session.cookie_secure" "0" "$PHP_INI"
update_php_config "session.cookie_httponly" "1" "$PHP_INI"

# Configuraciones de OPcache
update_php_config "opcache.enable" "1" "$PHP_INI"
update_php_config "opcache.memory_consumption" "128" "$PHP_INI"
update_php_config "opcache.interned_strings_buffer" "8" "$PHP_INI"
update_php_config "opcache.max_accelerated_files" "4000" "$PHP_INI"
update_php_config "opcache.revalidate_freq" "2" "$PHP_INI"
update_php_config "opcache.fast_shutdown" "1" "$PHP_INI"

echo "✅ Configuración PHP completada!"
echo "🔄 Reiniciando LiteSpeed para aplicar cambios..."

# Reiniciar LiteSpeed
/usr/local/lsws/bin/lswsctrl restart

echo "🎉 ¡Configuración aplicada exitosamente!"
