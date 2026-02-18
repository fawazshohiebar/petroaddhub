# Coolify Deployment Configuration
# Copy this configuration to your Coolify dashboard

## Build Pack: Nixpacks or Dockerfile

## Install Command:
composer install --optimize-autoloader --no-dev && npm install

## Build Command:
npm run build && chmod +x deploy.sh

## Start Command:
bash deploy.sh && php-fpm & nginx -g 'daemon off;' & php artisan horizon

## Port:
8000

## Health Check:
Path: /
Port: 8000
Interval: 30
Timeout: 10

## Required Environment Variables:
# APP_NAME=PetroAddHub
# APP_ENV=production
# APP_KEY=base64:your-key-here
# APP_DEBUG=false
# APP_URL=https://yourdomain.com
# 
# DB_CONNECTION=mysql
# DB_HOST=your-db-host
# DB_PORT=3306
# DB_DATABASE=your-database
# DB_USERNAME=your-username
# DB_PASSWORD=your-password
# 
# CACHE_STORE=redis
# QUEUE_CONNECTION=redis
# SESSION_DRIVER=redis
# REDIS_HOST=your-redis-host
# REDIS_PORT=6379
# 
# STATAMIC_LICENSE_KEY=your-license-key
# STATAMIC_STACHE_WATCHER=false
# 
# AWS_ACCESS_KEY_ID=your-key
# AWS_SECRET_ACCESS_KEY=your-secret
# AWS_DEFAULT_REGION=your-region
# AWS_BUCKET=your-bucket
# FILESYSTEM_DISK=s3

## Persistent Storage (Add these volumes in Coolify):
# /app/storage/app -> Persistent volume
# /app/content -> Persistent volume (Statamic content)

## Post-deployment Command:
php artisan migrate --force && php artisan statamic:stache:warm
