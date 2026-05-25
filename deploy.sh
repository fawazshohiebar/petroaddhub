#!/bin/bash
# Exit immediately if a command exits with a non-zero status
set -e

echo "🚀 Starting deployment for PetroAddHub..."

# 1. Pull the latest code cleanly
# We use 'git reset --hard' to throw away the automated server cache files before pulling
git fetch origin main
git reset --hard origin/main

# 2. Install PHP dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# 3. Install Node dependencies & build assets
npm ci
npm run build

# 4. Clear and optimize Statamic / Laravel caches
php please stache:clear
php please static:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Deployment completed successfully!"