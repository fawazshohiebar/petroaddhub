#!/bin/bash
# Move to the website directory
cd /home/fawaz/htdocs/petroaddhub.com

# Pull latest code from GitHub
git pull origin main

# Install PHP dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Install Node dependencies
npm ci --production=false

# Build frontend assets
npm run build

# Clear and optimize caches
php please stache:clear
php please static:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
chmod -R 775 storage bootstrap/cache
chmod -R 775 public/build 2>/dev/null || true

echo "✅ Deployment completed successfully!"
