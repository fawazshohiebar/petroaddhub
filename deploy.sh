#!/bin/bash
# Exit immediately if any command fails
set -e

echo "🚀 Starting deployment for PetroAddHub..."

# Move to the website directory
cd /home/fawaz/htdocs/petroaddhub.com

# ---------------------------------------------------------
# STEP 1: Pull your new code changes safely  
# ---------------------------------------------------------
echo "📥 Pulling latest changes from GitHub..."
git pull origin main

# 2. Install PHP dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# 3. Install Node dependencies & build assets
npm ci
npm run build

# 4. Clear and optimize Statamic / Laravel caches
php artisan statamic:glide:clear 
php please stache:clear
php please static:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Lock down folder permissions
chmod -R 775 storage bootstrap/cache

echo "✅ Deployment completed successfully!"