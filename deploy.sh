#!/bin/bash
set -euo pipefail

PROJECT="$(cd "$(dirname "$0")" && pwd)"  # 👈 auto-détecte le dossier
USER="papoel"

echo "🚀 Déploiement de ${PROJECT} - $(date)"

cd "$PROJECT"

echo "📥 Récupération du code..."
git fetch origin
git reset --hard origin/main

echo "📦 Dépendances..."
composer install --no-dev --optimize-autoloader --no-interaction
composer dump-env prod

echo "🧹 Cache & assets..."
php bin/console cache:clear --env=prod --no-debug
php bin/console asset-map:compile

echo "🗄️  Migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

echo "🔒 Permissions..."
sudo chown -R "$USER:www-data" "$PROJECT"
sudo chmod -R 775 "$PROJECT/var"

echo "🔄 Rechargement Apache..."
sudo systemctl reload apache2

echo "✅ Déploiement terminé - $(date)"
