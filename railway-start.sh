#!/bin/bash
set -e

# ============================================================
# Script de demarrage Railway avec volume persistant SQLite
# Le volume Railway est monte sur /data
# ============================================================

echo "[Railway Start] Preparation du volume persistant..."

# Creer les dossiers persistants dans le volume monte sur /data
mkdir -p /data/database
mkdir -p /data/storage/framework/cache
mkdir -p /data/storage/framework/sessions
mkdir -p /data/storage/framework/views
mkdir -p /data/storage/framework/testing
mkdir -p /data/storage/app/public
mkdir -p /data/storage/logs
mkdir -p /data/storage/pail
mkdir -p /app/bootstrap/cache

# Si /app/storage existe et n'est pas un lien symbolique, le remplacer par un lien vers /data/storage
if [ -d "/app/storage" ] && [ ! -L "/app/storage" ]; then
    echo "[Railway Start] Migration du storage vers le volume persistant..."
    # Si /data/storage est vide, copier le contenu actuel
    if [ -z "$(ls -A /data/storage 2>/dev/null)" ]; then
        cp -r /app/storage/* /data/storage/ 2>/dev/null || true
    fi
    rm -rf /app/storage
fi

# Creer le lien symbolique storage -> /data/storage
if [ ! -L "/app/storage" ]; then
    ln -s /data/storage /app/storage
fi

# Creer le fichier SQLite s'il n'existe pas encore
if [ ! -f "/data/database/database.sqlite" ]; then
    echo "[Railway Start] Initialisation de la base SQLite..."
    touch /data/database/database.sqlite
    chmod 777 /data/database/database.sqlite
    
    # Lancer les migrations et les seeders UNE SEULE FOIS
    php artisan migrate --force
    php artisan db:seed --force --class=DatabaseSeeder
    echo "[Railway Start] Base SQLite initialisee avec succes !"
else
    echo "[Railway Start] Base SQLite existante detectee. Pas de re-initialisation."
    # On lance quand meme les migrations au cas ou il y a des nouvelles
    php artisan migrate --force
fi

# Permissions
chmod -R 777 /data/storage /data/database /app/bootstrap/cache

# Lien symbolique pour le storage public (images/uploads)
php artisan storage:link --force

# Lancer le serveur Laravel
echo "[Railway Start] Demarrage du serveur Laravel..."
php artisan serve --host=0.0.0.0 --port=$PORT
