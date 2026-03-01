# .docker/litespeed/Dockerfile
# Dockerfile: OpenLiteSpeed on ubuntu:22.04 with lsphp83 + Composer + Node.js 22
FROM ubuntu:22.04

LABEL maintainer="openlitespeed-stack"
ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=America/Lima
ENV PHP_VERSION=83
# ENV ADMIN_PASS=cesar203
ARG ADMIN_PASS=changeme
# ✅ añadir ARG antes del bloque SSH (igual que ADMIN_PASS)
ARG SSH_PASSWORD=changeme
ENV PHP_INI_DIR=/usr/local/lsws/lsphp${PHP_VERSION}/etc/php/8.3/litespeed
ENV PATH=/usr/local/lsws/lsphp${PHP_VERSION}/bin:/usr/local/lsws/bin:/usr/local/bin:$PATH

# ========================================
# 1) Instalación de paquetes base
# ========================================
RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates curl wget gnupg2 lsb-release software-properties-common \
    unzip zip git vim nano procps net-tools iputils-ping locales tzdata \
    build-essential && \
    ln -sf /usr/share/zoneinfo/${TZ} /etc/localtime && \
    dpkg-reconfigure -f noninteractive tzdata && \
    update-ca-certificates --fresh && \
    rm -rf /var/lib/apt/lists/*



# ========================================
# Instalar y configurar SSH en puerto 2222
# ========================================
RUN apt-get update && apt-get install -y openssh-server && \
    mkdir -p /var/run/sshd && \
    # Configurar contraseña
    echo "root:${SSH_PASSWORD}" | chpasswd && \
    # Cambiar puerto a 2222
    echo "Port 2222" >> /etc/ssh/sshd_config && \
    # Permitir login de root
    sed -i 's/#PermitRootLogin prohibit-password/PermitRootLogin yes/' /etc/ssh/sshd_config && \
    # Configuración adicional de seguridad
    sed -i 's/#PasswordAuthentication yes/PasswordAuthentication yes/' /etc/ssh/sshd_config && \
    sed -i 's@session\s*required\s*pam_loginuid.so@session optional pam_loginuid.so@g' /etc/pam.d/sshd && \
    # Optimizaciones
    echo "UseDNS no" >> /etc/ssh/sshd_config && \
    echo "GSSAPIAuthentication no" >> /etc/ssh/sshd_config

# Exponer puerto SSH personalizado
EXPOSE 2222


# ========================================
# 2) Agregar repositorio de LiteSpeed
# ========================================
RUN wget -O - https://repo.litespeed.sh | bash

# ========================================
# 3) Instalar OpenLiteSpeed
# ========================================
RUN apt-get update && \
    apt-get install -y openlitespeed && \
    rm -rf /var/lib/apt/lists/*

# ========================================
# 4) Agregar repositorio PHP (Ondřej)
# ========================================
RUN add-apt-repository ppa:ondrej/php -y && \
    apt-get update

# ========================================
# 5) Instalar PHP 8.3 y extensiones
# ========================================
RUN apt-get install -y --no-install-recommends \
    lsphp83 lsphp83-common lsphp83-mysql lsphp83-opcache \
    lsphp83-curl lsphp83-imap lsphp83-intl lsphp83-ldap \
    lsphp83-pgsql lsphp83-sqlite3 lsphp83-redis lsphp83-memcached \
    lsphp83-imagick lsphp83-apcu lsphp83-pear && \
    rm -rf /var/lib/apt/lists/*

# ========================================
# 6) Instalar Composer (Última versión estable)
# ========================================
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer && \
    chmod +x /usr/local/bin/composer && \
    composer --version

# ========================================
# 7) Instalar Node.js 22.x
# ========================================
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && \
    apt-get install -y nodejs && \
    node --version && \
    npm --version && \
    rm -rf /var/lib/apt/lists/*

# ========================================
# 8) Configuración de PHP 8.3
# ========================================
RUN sed -i 's/^max_execution_time = .*/max_execution_time = 600/' ${PHP_INI_DIR}/php.ini && \
    sed -i 's/^max_input_time = .*/max_input_time = 600/' ${PHP_INI_DIR}/php.ini && \
    sed -i 's/^;max_input_vars = .*/max_input_vars = 5000/' ${PHP_INI_DIR}/php.ini && \
    sed -i 's/^memory_limit = .*/memory_limit = 512M/' ${PHP_INI_DIR}/php.ini && \
    sed -i 's/^post_max_size = .*/post_max_size = 512M/' ${PHP_INI_DIR}/php.ini && \
    sed -i 's/^upload_max_filesize = .*/upload_max_filesize = 512M/' ${PHP_INI_DIR}/php.ini && \
    sed -i 's|^;error_log = php_errors.log|error_log = /usr/local/lsws/logs/php_errors.log|' ${PHP_INI_DIR}/php.ini && \
    sed -i "s|^;date.timezone =|date.timezone = ${TZ}|" ${PHP_INI_DIR}/php.ini

# ========================================
# 9) Configuración de OPcache
# ========================================
RUN sed -i 's/^;opcache.enable=.*/opcache.enable=1/' ${PHP_INI_DIR}/php.ini && \
    sed -i 's/^;opcache.enable_cli=.*/opcache.enable_cli=1/' ${PHP_INI_DIR}/php.ini && \
    sed -i 's/^;opcache.memory_consumption=.*/opcache.memory_consumption=256/' ${PHP_INI_DIR}/php.ini && \
    sed -i 's/^;opcache.interned_strings_buffer=.*/opcache.interned_strings_buffer=16/' ${PHP_INI_DIR}/php.ini && \
    sed -i 's/^;opcache.max_accelerated_files=.*/opcache.max_accelerated_files=20000/' ${PHP_INI_DIR}/php.ini && \
    sed -i 's/^;opcache.validate_timestamps=.*/opcache.validate_timestamps=1/' ${PHP_INI_DIR}/php.ini && \
    sed -i 's/^;opcache.revalidate_freq=.*/opcache.revalidate_freq=2/' ${PHP_INI_DIR}/php.ini && \
    sed -i 's/^;opcache.save_comments=.*/opcache.save_comments=1/' ${PHP_INI_DIR}/php.ini && \
    sed -i 's/^;opcache.enable_file_override=.*/opcache.enable_file_override=1/' ${PHP_INI_DIR}/php.ini

# ========================================
# 10) Configuración SSL/TLS para PHP
# ========================================
RUN sed -i 's/^;curl.cainfo =/curl.cainfo = \/etc\/ssl\/certs\/ca-certificates.crt/' ${PHP_INI_DIR}/php.ini && \
    sed -i 's/^;openssl.cafile=/openssl.cafile=\/etc\/ssl\/certs\/ca-certificates.crt/' ${PHP_INI_DIR}/php.ini && \
    sed -i 's/^;openssl.capath=/openssl.capath=\/etc\/ssl\/certs/' ${PHP_INI_DIR}/php.ini && \
    chmod 644 /etc/ssl/certs/ca-certificates.crt && \
    chmod 755 /etc/ssl/certs

# ========================================
# 11) Configuración Realpath Cache
# ========================================
RUN echo "" >> ${PHP_INI_DIR}/php.ini && \
    echo "; Realpath Cache" >> ${PHP_INI_DIR}/php.ini && \
    echo "realpath_cache_size = 4096K" >> ${PHP_INI_DIR}/php.ini && \
    echo "realpath_cache_ttl = 600" >> ${PHP_INI_DIR}/php.ini

# ========================================
# 12) Cambiar puerto por defecto de 8088 a 80
# ========================================
RUN sed -i 's/:8088/:80/g' /usr/local/lsws/conf/httpd_config.conf

# ========================================
# 13) Crear estructura de directorios
# ========================================
RUN mkdir -p /var/www/html/public_html \
    /var/www/html/logs \
    /usr/local/lsws/logs \
    /usr/local/lsws/conf/vhosts/default \
    /tmp/lshttpd/swap && \
    chmod 755 /var/www/html && \
    chmod 755 /usr/local/lsws/logs

# ========================================
# 14) Configurar permisos base
# ========================================
RUN chown -R lsadm:lsadm /usr/local/lsws/conf && \
    chown -R lsadm:lsadm /usr/local/lsws/admin && \
    chown -R lsadm:lsadm /usr/local/lsws/logs && \
    chown -R nobody:nogroup /var/www/html && \
    chmod 750 /usr/local/lsws/conf && \
    chmod 750 /usr/local/lsws/admin/conf

# ========================================
# 15) Configurar contraseña del administrador
# ========================================
RUN /usr/local/lsws/admin/misc/admpass.sh <<EOF
admin
${ADMIN_PASS}
${ADMIN_PASS}
EOF

# ========================================
# 16) Configuración del virtual host default
# ========================================
RUN mkdir -p /usr/local/lsws/conf/vhosts/default

# ========================================
# 17) Limpiar archivos temporales y locks
# ========================================
RUN rm -rf /tmp/lshttpd/*.sock \
    /usr/local/lsws/tmp/lshttpd/*.sock \
    /var/run/*.pid \
    /usr/local/lsws/logs/*.pid 2>/dev/null || true

# Brindando permisos a los archivos de logs
RUN touch /var/www/html/logs/access.log /var/www/html/logs/error.log && \
    chown nobody:nogroup /var/www/html/logs/*.log

# ========================================
# 18) Crear usuario composer (opcional pero recomendado)
# ========================================
RUN useradd -m -s /bin/bash composer && \
    mkdir -p /home/composer/.composer && \
    chown -R composer:composer /home/composer

# ========================================
# 19) Configurar npm global sin sudo
# ========================================
RUN mkdir -p /usr/local/share/npm-global && \
    npm config set prefix '/usr/local/share/npm-global' && \
    echo 'export PATH=/usr/local/share/npm-global/bin:$PATH' >> /etc/bash.bashrc

# ========================================
# 20) Exponer solo puerto 80 y 7080 (admin)
# ========================================
EXPOSE 80 7080

# ========================================
# 21) Healthcheck
# ========================================
HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# ========================================
# 22) Script de inicio
# ========================================
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

WORKDIR /var/www/html

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["/usr/local/lsws/bin/openlitespeed", "-d"]