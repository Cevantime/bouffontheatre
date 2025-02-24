#!/usr/bin/env bash
git reset --hard
git pull -r
composer install --optimize-autoloader
bin/console ckeditor:install --tag=4.22.0 --clear=drop
bin/console assets:install
bin/console doctrine:migrations:migrate --no-interaction
bin/console cache:clear
yarn install --force
yarn build
chown -R www-data:www-data .