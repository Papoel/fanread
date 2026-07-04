#!/bin/bash
set -e

PROJECT="/var/www/sites/fanread"
echo "Deploiement demarre - $(date)"

cd $PROJECT

# Force la synchronisation avec GitHub sans conflits
git fetch origin
git reset --hard origin/main

composer install --no-dev --optimize-autoloader
composer dump-env prod

php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
php bin/console asset-map:compile

sudo chown -R papoel:www-data $PROJECT
sudo chmod -R 775 $PROJECT
sudo chmod -R 777 $PROJECT/var

sudo systemctl restart cloudflared

echo "Deploiement termine - $(date)"
