#!/bin/bash
set -e

# Générer la clé si elle n'existe pas
if [ -z "$APP_KEY" ]; then
    export APP_KEY=$(php artisan key:generate --show)
fi

# Créer le lien storage s'il n'existe pas
php artisan storage:link || true

# Caches Laravel
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Migrations et seeders
php artisan migrate --force || true
php artisan db:seed --force || true

# Lancer Apache
exec apache2-foreground
