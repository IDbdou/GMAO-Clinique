# Guide de deploiement Railway - GMAO Clinique (SQLite + Volume Persistant)

## Resume en 4 etapes

1. Creer un projet Railway a partir de GitHub
2. Ajouter un **Volume** persistant monte sur `/data`
3. Configurer les variables d'environnement (SQLite)
4. Deployer

---

## Etape 1 : Pusher le code sur GitHub

Assure-toi que les fichiers suivants sont bien pousses :
- `railway.json`
- `railway-start.sh`
- `.env.railway` (a titre indicatif, pas besoin sur le repo)

```bash
git add railway.json railway-start.sh .env.railway RAILWAY_DEPLOY.md
git commit -m "Config Railway deployment avec SQLite persistant"
git push origin main
```

---

## Etape 2 : Creer le projet sur Railway

1. Va sur [https://railway.app](https://railway.app) et connecte-toi avec GitHub
2. Clique sur **"New Project"**
3. Choisis **"Deploy from GitHub repo"**
4. Selectionne ton repo `GMAO-Clinique`
5. Railway va automatiquement detecter la config `railway.json` et build

---

## Etape 3 : Ajouter un Volume Persistant (CRUCIAL)

Sans volume, ta base SQLite sera perdue a chaque redeploiement.

1. Dans ton projet Railway, clique sur le service Laravel
2. Va dans l'onglet **"Volumes"** (ou **"Settings"** > **"Volumes"**)
3. Clique sur **"New Volume"** ou **"Mount Volume"**
4. Configure :
   - **Mount Path** : `/data`
   - La taille par defaut suffit (1-5 GB)
5. Clique sur **"Create"**

> Le volume sera monte automatiquement sur `/data` dans ton conteneur.

---

## Etape 4 : Configurer les Variables d'Environnement

Dans Railway Dashboard > ton service Laravel > **"Variables"**, ajoute :

```env
APP_NAME=GMAO Clinique
APP_ENV=production
APP_KEY=base64:VOTRE_CLE_ICI
APP_DEBUG=false
APP_URL=https://votre-url-railway.up.railway.app

APP_LOCALE=fr
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=fr_FR

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=sqlite
DB_DATABASE=/data/database/database.sqlite

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=log
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME=GMAO Clinique

VITE_APP_NAME=GMAO Clinique
```

### Generer la APP_KEY

Dans ton terminal local (dans le projet) :
```bash
php artisan key:generate --show
```

Copie la valeur (ex: `base64:xxxxxxxxxxxxxxxxx`) dans la variable `APP_KEY` sur Railway.

---

## Etape 5 : Deployer

1. Railway va automatiquement build et deployer
2. Va dans l'onglet **"Deployments"** pour suivre le build
3. Une fois termine, clique sur l'URL du service

Le premier demarrage peut prendre 1-2 minutes car il initialise la base SQLite.

---

## Comment ca marche ?

Le fichier `railway-start.sh` gere tout automatiquement :

1. **Au premier demarrage** :
   - Cree le dossier `/data/database`
   - Cree le fichier `/data/database/database.sqlite`
   - Lance les migrations et les seeders (donnees de demo)
   - Cree les dossiers storage persistants

2. **Aux demarrages suivants** (reboot, update) :
   - Detecte que la base existe deja
   - Ne recree PAS la base (donnees conservees !)
   - Lance juste les nouvelles migrations si besoin

3. **Le volume `/data`** est persistant :
   - `database.sqlite` y reste toujours
   - Les uploads (`storage/app/public`) aussi
   - Les logs aussi

---

## Identifiants par defaut

| Role | Email | Mot de passe |
|------|-------|--------------|
| Admin | `admin@gmao.local` | `password` |
| Technicien | `tech1@gmao.local` | `password` |
| Chef de service | `chef-rad@gmao.local` | `password` |

---

## Astuces & Depannage

### Verifier les logs
- Railway Dashboard > Deployments > clique sur le deploiement > "Logs"
- Cherche `[Railway Start]` pour voir l'initialisation

### Base perdue ?
Si la base est vide apres un redeploiement, c'est que le volume n'est pas monte sur `/data`.
- Verifie dans Railway : Service > Volumes > Mount Path doit etre `/data`

### Changer l'URL
- Railway Dashboard > Settings > Domains
- Tu peux ajouter un domaine personnalise (ex: `gmao.mondomaine.com`)

### Mettre a jour l'app
- Pousse juste les modifications sur GitHub
- Railway rebuild et redeploie automatiquement
- La base SQLite reste intacte grace au volume

---

## Architecture du stockage persistant

```
/data                          <-- Volume persistant Railway
├── database/
│   └── database.sqlite        <-- Base SQLite (TRES IMPORTANT)
└── storage/                   <-- Logs, uploads, cache, sessions
    ├── app/public/
    ├── framework/cache/
    ├── framework/sessions/
    ├── framework/views/
    └── logs/

/app                           <-- Code source (rebuild a chaque deploy)
├── railway-start.sh
├── railway.json
└── storage -> /data/storage   <-- Lien symbolique
```

---

## Pourquoi SQLite avec volume plutot que PostgreSQL ?

| SQLite + Volume | PostgreSQL |
|-------|-------|
| Plus simple (pas de service externe) | Plus robuste |
| Gratuit, pas de limite de connexion | Gratuit aussi sur Railway |
| Tout dans un seul fichier | Plus complexe |
| Suffisant pour une GMAO petite/moyenne | Recommande si tres gros trafic |

Cette configuration est **parfaite** pour un projet demo ou de petite taille.
