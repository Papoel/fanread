#!/bin/bash
set -euo pipefail  # arrête au moindre problème + variables non définies

PROJECT="/var/www/sites/fanread"
USER="papoel"

echo "🚀 Déploiement démarré - $(date)"

cd "$PROJECT"

# --- 1. Synchronisation avec GitHub ---
echo "📥 Récupération du code..."
git fetch origin
git reset --hard origin/main

# --- 2. Dépendances ---
echo "📦 Installation des dépendances..."
composer install --no-dev --optimize-autoloader --no-interaction
composer dump-env prod

# --- 3. Cache & Assets ---
echo "🧹 Nettoyage du cache..."
php bin/console cache:clear --env=prod --no-debug
php bin/console cache:warmup --env=prod

echo "🎨 Compilation des assets..."
php bin/console asset-map:compile

# --- 4. Migrations base de données (optionnel mais recommandé) ---
echo "🗄️  Migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

# --- 5. Permissions ---
echo "🔒 Réglage des permissions..."
sudo chown -R "$USER:www-data" "$PROJECT"
sudo chmod -R 775 "$PROJECT/var"

# --- 6. Redémarrage services ---
echo "🔄 Redémarrage des services..."
sudo systemctl restart cloudflared

echo "✅ Déploiement terminé - $(date)"
