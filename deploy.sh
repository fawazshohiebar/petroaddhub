#!/bin/bash
set -e

echo "🚀 Starting deployment..."

# Run migrations
echo "📦 Running migrations..."
php artisan migrate --force

# Clear and cache config
echo "⚙️  Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan statamic:stache:warm

# Restart Horizon
echo "🔄 Restarting Horizon..."
php artisan horizon:terminate

echo "✅ Deployment complete!"
