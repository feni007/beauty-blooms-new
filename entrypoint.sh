#!/bin/bash
set -e

cd /var/www/html

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views
mkdir -p storage/logs bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache

php artisan storage:link --force

php artisan config:cache

exec apache2-foreground
