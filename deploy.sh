#!/usr/bin/env bash
git pull
composer install --optimize-autoloader
bin/console ckeditor:install --tag=4.22.0 --clear=drop
bin/console assets:install
yarn build
chown -R www-data:www-data .