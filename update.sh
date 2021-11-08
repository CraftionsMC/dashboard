#!/usr/bin/env bash
php artisan down
git stash
git pull
chmod -R 755 /var/www/dashboard
composer install --no-dev --optimize-autoloader
php artisan view:clear
php artisan config:clear
chown -R www-data:www-data /var/www/dashboard/*
php artisan queue:restart
php artisan up
