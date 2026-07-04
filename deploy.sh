#!/bin/bash
set -euo pipefail

PROJECT="/var/www/sites/fanread"
USER="papoel"

echo "🚀 Déploiement démarré - $(date)"

cd "$PROJECT"

# --- 1. Synchronisation GitHub ---
echo "📥 Récupération du code..."
git fetch origin
git reset --hard origin/main

# --- 2. Dépendances PHP ---
echo "📦 Installation des dépendances..."
composer install --no-dev --optimize-autoloader --no-interaction
composer dump-env prod

# --- 3. Cache & Assets ---
echo "🧹 Cache & assets..."
php bin/console cache:clear --env=prod --no-debug
php bin/console asset-map:compile

# --- 4. Migrations BDD ---
echo "🗄️  Migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

# --- 5. Permissions ---
echo "🔒 Permissions..."
sudo chown -R "$USER:www-data" "$PROJECT"
sudo chmod -R 775 "$PROJECT/var"

# --- 6. Recharger Apache (PAS cloudflared !) ---
echo "🔄 Rechargement Apache..."
sudo systemctl reload apache2

echo "✅ Déploiement terminé - $(date)"
