# Étape 1 : Build des assets Node
FROM node:20-alpine AS node-build

WORKDIR /app
COPY package*.json ./
RUN npm install --no-audit --no-fund

COPY . .
RUN npm run build

# Étape 2 : Application PHP avec Apache
FROM php:8.3-apache

# Activer mod_rewrite pour Laravel
RUN a2enmod rewrite

# Installer les extensions PHP et outils nécessaires
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        intl \
        zip \
        mbstring \
        pdo \
        pdo_mysql \
        mysqli \
        gd \
        opcache \
        bcmath \
        xml \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurer le document root Apache sur /var/www/html/public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

# Copier les fichiers du projet
COPY . .

# Copier les assets buildés depuis l'étape Node
COPY --from=node-build /app/public/build ./public/build

# Installer les dépendances PHP en production
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts \
    && composer dump-autoload --optimize

# Permissions pour Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Exposer le port 80
EXPOSE 80

# Commande de démarrage
CMD ["apache2-foreground"]
