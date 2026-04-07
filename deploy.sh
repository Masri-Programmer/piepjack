#!/bin/bash
set -e
cd /var/www/virtual/piepjack/testing-piepjack

echo "🚀 Starting Deployment Logic..."

# Ensure we have the code we just pushed
git fetch --all
git reset --hard origin/main

# Dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Migrations & Cache
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# rm -rf storage/framework/views/*
# rm -rf bootstrap/cache/*
rm -rf storage/framework/cache/data/
php artisan up

echo "✅ Server tasks complete!"