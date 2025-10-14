#!/usr/bin/env bash
sed -i 's/MAINTENANCE_MODE=0/MAINTENANCE_MODE=1/' .env.local
git reset --hard
git pull -r
composer install --optimize-autoloader
bin/console ckeditor:install --tag=4.22.0 --clear=drop
bin/console assets:install
bin/console doctrine:migrations:migrate --no-interaction
bin/console cache:clear
yarn install --force
yarn build
sed -i 's/MAINTENANCE_MODE=1/MAINTENANCE_MODE=0/' .env.local
chown -R www-data:www-data .