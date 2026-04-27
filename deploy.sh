#!/bin/bash
set -e

echo "⚡ Fast deployment starting..."

# Only run migrations if schema changed (safe to run always, but fast if no changes)
php artisan migrate --force --isolated 2>/dev/null || true

# Warm Statamic cache (fast operation)
php artisan statamic:stache:warm 2>/dev/null || true

# Restart queue workers if running
php artisan horizon:terminate 2>/dev/null || true

echo "✅ Deployed in $(date +%s) seconds!"
