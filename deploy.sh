#!/bin/bash
# Exit immediately if any command fails
set -e

echo "🚀 Starting deployment for PetroAddHub..."

# Move to the website directory
cd /home/fawaz/htdocs/petroaddhub.com

# 1. Clean house and pull latest code from GitHub
# This drops local automated server tracking files so the pull succeeds perfectly
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