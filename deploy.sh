#!/bin/bash
# Exit immediately if any command fails
set -e

echo "🚀 Starting deployment for PetroAddHub..."

# Move to the website directory
cd /home/fawaz/htdocs/petroaddhub.com

# ---------------------------------------------------------
# STEP 0: SAVE LIVE CONTENT EDITS FIRST (Crucial for Flat-File)
# ---------------------------------------------------------
echo "📦 Checking for live Control Panel updates..."

# Configure a temporary git profile for the server if not already done globally
git config user.name "Server Auto Deployment"
git config user.email "deploy@petroaddhub.com"

# Check if there are changes in content, users, or assets folders
if [ -n "$(git status --porcelain content/ users/ public/assets/)" ]; then
    echo "💾 Found live updates! Saving to GitHub..."
    git add content/ users/ public/assets/
    git commit -m "chore: save live updates from Control Panel [skip ci]"
    
    # Rebase against origin to stay clean, then push back to GitHub
    git pull --rebase origin main
    git push origin main
else
    echo "✨ No new live content edits found to save."
fi

# ---------------------------------------------------------
# STEP 1: Pull your new code changes safely
# ---------------------------------------------------------
echo "📥 Pulling latest code changes..."
git pull origin main

# 2. Install PHP dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# 3. Install Node dependencies & build assets
npm ci
npm run build

# 4. Clear and optimize Statamic / Laravel caches
# Added the glide cache clear we fixed earlier!
php artisan statamic:glide:clear 
php please stache:clear
php please static:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Lock down folder permissions for CloudPanel user fawaz
chmod -R 775 storage bootstrap/cache

echo "✅ Deployment completed successfully!"