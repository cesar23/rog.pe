FROM codercom/code-server:latest

ENV BASHRC_PATH=/etc/bash.bashrc
ENV DEBIAN_FRONTEND=noninteractive

USER root

# ========================================
# Instalar TODO desde repositorios oficiales
# ========================================
RUN apt-get update && apt-get install -y \
    ca-certificates \
    curl \
    wget \
    git \
    vim \
    nano \
    zip \
    unzip \
    htop \
    tree \
    bash \
    bash-completion \
    mariadb-client \
    php \
    php-cli \
    php-common \
    php-curl \
    php-mysql \
    php-mbstring \
    php-xml \
    php-zip \
    php-gd \
    php-intl \
    php-bcmath \
    php-soap \
    php-redis \
    php-opcache \
    && rm -rf /var/lib/apt/lists/*

# Verificar PHP
RUN php -v

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer && \
    chmod +x /usr/local/bin/composer && \
    composer --version

# Instalar Node.js 22.x
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && \
    apt-get install -y nodejs && \
    node --version && \
    npm --version && \
    rm -rf /var/lib/apt/lists/*

# ========================================
# Configuración de usuario nobody
# ========================================

# Cambiar shell de nobody a bash
RUN usermod -s /bin/bash nobody || \
    sed -i 's|nobody:x:65534:65534:nobody:/nonexistent:/usr/sbin/nologin|nobody:x:65534:65534:nobody:/home/nobody:/bin/bash|g' /etc/passwd

# Crear directorios
RUN mkdir -p /home/nobody/.config/code-server && \
    mkdir -p /home/nobody/.local/share/code-server/extensions && \
    mkdir -p /home/nobody/.local/share/code-server/User && \
    mkdir -p /home/nobody/.cache && \
    mkdir -p /home/nobody/.composer && \
    mkdir -p /workspace

# Permisos
RUN chown -R 65534:65534 /home/nobody && \
    chmod -R 755 /home/nobody

# Configurar bashrc
RUN echo 'export PS1="\[\033[01;32m\]\u@code-server\[\033[00m\]:\[\033[01;34m\]\w\[\033[00m\]\$ "' >> $BASHRC_PATH && \
    echo '' >> $BASHRC_PATH && \
    echo '# Aliases básicos' >> $BASHRC_PATH && \
    echo 'alias ll="ls -lah --color=auto"' >> $BASHRC_PATH && \
    echo 'alias la="ls -A --color=auto"' >> $BASHRC_PATH && \
    echo 'alias l="ls -CF --color=auto"' >> $BASHRC_PATH && \
    echo '' >> $BASHRC_PATH && \
    echo '# Docker' >> $BASHRC_PATH && \
    echo 'alias dc="docker-compose"' >> $BASHRC_PATH && \
    echo 'alias dcp="docker-compose ps"' >> $BASHRC_PATH && \
    echo 'alias dcl="docker-compose logs -f"' >> $BASHRC_PATH && \
    echo 'alias dcr="docker-compose restart"' >> $BASHRC_PATH && \
    echo '' >> $BASHRC_PATH && \
    echo '# Git' >> $BASHRC_PATH && \
    echo 'alias gs="git status"' >> $BASHRC_PATH && \
    echo 'alias ga="git add"' >> $BASHRC_PATH && \
    echo 'alias gc="git commit"' >> $BASHRC_PATH && \
    echo 'alias gp="git push"' >> $BASHRC_PATH && \
    echo 'alias gpl="git pull"' >> $BASHRC_PATH && \
    echo 'alias gl="git log --oneline --graph --decorate"' >> $BASHRC_PATH && \
    echo '' >> $BASHRC_PATH && \
    echo '# Laravel' >> $BASHRC_PATH && \
    echo 'alias art="php artisan"' >> $BASHRC_PATH && \
    echo 'alias tinker="php artisan tinker"' >> $BASHRC_PATH && \
    echo 'alias migrate="php artisan migrate"' >> $BASHRC_PATH && \
    echo 'alias seed="php artisan db:seed"' >> $BASHRC_PATH && \
    echo '' >> $BASHRC_PATH && \
    echo '# Composer' >> $BASHRC_PATH && \
    echo 'alias ci="composer install"' >> $BASHRC_PATH && \
    echo 'alias cu="composer update"' >> $BASHRC_PATH && \
    echo 'alias cr="composer require"' >> $BASHRC_PATH && \
    echo 'alias cda="composer dump-autoload"' >> $BASHRC_PATH && \
    echo '' >> $BASHRC_PATH && \
    echo '# NPM' >> $BASHRC_PATH && \
    echo 'alias ni="npm install"' >> $BASHRC_PATH && \
    echo 'alias nrd="npm run dev"' >> $BASHRC_PATH && \
    echo 'alias nrb="npm run build"' >> $BASHRC_PATH && \
    echo '' >> $BASHRC_PATH && \
    echo 'cd /workspace' >> $BASHRC_PATH && \
    echo '' >> $BASHRC_PATH && \
    echo 'echo ""' >> $BASHRC_PATH && \
    echo 'echo "╔══════════════════════════════════════════════════════════════╗"' >> $BASHRC_PATH && \
    echo 'echo "║          🚀 code-server - LiteSpeed Stack                   ║"' >> $BASHRC_PATH && \
    echo 'echo "╚══════════════════════════════════════════════════════════════╝"' >> $BASHRC_PATH && \
    echo 'echo ""' >> $BASHRC_PATH && \
    echo 'echo "📁 Workspace: /workspace"' >> $BASHRC_PATH && \
    echo 'echo "🐘 PHP: $(php -v | head -n 1)"' >> $BASHRC_PATH && \
    echo 'echo "📦 Composer: $(composer --version --no-ansi | head -n 1)"' >> $BASHRC_PATH && \
    echo 'echo "🟢 Node: $(node --version)"' >> $BASHRC_PATH && \
    echo 'echo "📘 NPM: $(npm --version)"' >> $BASHRC_PATH && \
    echo 'echo ""' >> $BASHRC_PATH

# Sudoers
RUN echo "nobody ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers

# Git config
RUN git config --system user.name "Developer" && \
    git config --system user.email "dev@localhost" && \
    git config --system core.editor "code --wait" && \
    git config --system init.defaultBranch main

# Config de code-server
RUN echo 'bind-addr: 0.0.0.0:8080' > /home/nobody/.config/code-server/config.yaml && \
    echo 'auth: password' >> /home/nobody/.config/code-server/config.yaml && \
    echo 'cert: false' >> /home/nobody/.config/code-server/config.yaml && \
    echo 'user-data-dir: /home/nobody/.local/share/code-server' >> /home/nobody/.config/code-server/config.yaml && \
    echo 'extensions-dir: /home/nobody/.local/share/code-server/extensions' >> /home/nobody/.config/code-server/config.yaml

# Permisos finales
RUN chown -R 65534:65534 /home/nobody && \
    chmod 644 $BASHRC_PATH && \
    chmod 644 /home/nobody/.config/code-server/config.yaml

# Verificación
RUN echo "╔═══════════════════════════════════════════════════════╗" && \
    echo "║       ✅ Instalaciones completadas                   ║" && \
    echo "╚═══════════════════════════════════════════════════════╝" && \
    echo "" && \
    php -v && echo "" && \
    composer --version && echo "" && \
    node --version && echo "" && \
    npm --version && echo ""

USER 65534:65534

WORKDIR /workspace

EXPOSE 8080

CMD ["code-server", "--bind-addr", "0.0.0.0:8080"]