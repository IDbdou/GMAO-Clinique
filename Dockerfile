# Dockerfile pour Railway - GMAO Clinique Laravel + Filament + SQLite
FROM dunglas/frankenphp:php8.3.33 AS builder

# Installer Node.js 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Installer les extensions PHP nécessaires
RUN install-php-extensions intl zip pdo_sqlite sqlite3 dom mbstring xml curl fileinfo openssl tokenizer

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copier les fichiers de dépendances d'abord (pour le cache Docker)
COPY composer.json composer.lock .npmrc package.json package-lock.json ./
COPY artisan ./

# Installer les dépendances PHP (sans scripts pour éviter les erreurs Laravel pendant le build)
RUN composer install --ignore-platform-reqs --optimize-autoloader --no-interaction --no-scripts

# Installer les dépendances Node (sans builder tant que le code source n'est pas copié)
RUN npm ci --ignore-scripts

# Copier le reste du code (vite.config.js, resources/, etc. nécessaires au build)
COPY . .

# Build les assets front (vite.config.js et resources/ sont maintenant présents)
RUN npm run build

# Post-install Laravel
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan filament:upgrade

# Image finale (runtime)
FROM dunglas/frankenphp:php8.3.33

# Installer les extensions PHP nécessaires pour le runtime
RUN install-php-extensions intl zip pdo_sqlite sqlite3 dom mbstring xml curl fileinfo openssl tokenizer

# Installer SQLite
RUN apt-get update && apt-get install -y sqlite3 && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Copier l'application depuis le builder
COPY --from=builder /app .

# Permissions
RUN chmod -R 755 /app \
    && chown -R www-data:www-data /app

# Le script de démarrage gère tout (migrations, volume, etc.)
CMD ["bash", "railway-start.sh"]
