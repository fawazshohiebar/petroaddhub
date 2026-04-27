# 🚀 Speed-Optimized Coolify Deployment Guide
## Target: < 30 seconds per deployment

## ⚡ RECOMMENDED SETUP: Nixpacks

### Why Nixpacks is Fastest:
- ✅ Caches dependencies (node_modules, vendor)
- ✅ Only rebuilds changed files
- ✅ No Docker image rebuild on code changes
- ✅ Incremental builds
- ⚡ **Average deployment: 15-25 seconds**

---

## 📋 Coolify Configuration

### 1. General Settings
```
Build Pack: Nixpacks
Port: 8000
Base Directory: /
```

### 2. Build Commands (Leave Empty - uses nixpacks.toml)
The `nixpacks.toml` file handles everything automatically.

**OR if you want manual control:**

**Install Command:**
```bash
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction && npm ci --prefer-offline --no-audit
```

**Build Command:**
```bash
npm run build
```

**Start Command:**
```bash
bash deploy.sh && php artisan serve --host=0.0.0.0 --port=8000
```

---

## ⚙️ Speed Optimizations

### 1. Enable Coolify's Build Cache
In Coolify settings:
- ✅ Enable "Use Build Cache"
- ✅ Enable "Cache Composer Dependencies"
- ✅ Enable "Cache NPM Dependencies"

### 2. Skip Heavy Operations
The optimized `deploy.sh` now:
- ❌ Skips config:cache (done in build)
- ❌ Skips route:cache (done in build)
- ❌ Skips view:cache (done in build)
- ✅ Only runs migrations (if needed)
- ✅ Warms Statamic cache (fast)

### 3. Environment Variables (Add these for speed)
```bash
# Laravel Optimizations
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Composer optimizations (not in production, just for builds)
COMPOSER_NO_INTERACTION=1
COMPOSER_PREFER_DIST=1

# NPM optimizations
NPM_CONFIG_PREFER_OFFLINE=true
NPM_CONFIG_NO_AUDIT=true
```

---

## 📊 Deployment Time Breakdown

### First Deployment (Cold Cache): ~2-3 minutes
- Composer install: ~60-90s
- NPM install: ~45-60s
- Asset build: ~20-30s
- Cache warmup: ~5-10s

### Subsequent Deployments (Cached): ~15-25 seconds
- ✅ Composer: ~5s (cached)
- ✅ NPM: ~3s (cached)
- ✅ Asset build: ~8-12s (only changed files)
- ✅ Deploy script: ~2-5s

---

## 🎯 What Gets Rebuilt on Each Push?

### ✅ Always Rebuilt (Fast):
- Vite assets (only changed CSS/JS)
- Laravel config cache
- Route cache
- View cache

### ❌ NOT Rebuilt (Cached):
- Composer vendor directory
- NPM node_modules
- PHP extensions
- System packages

---

## 🔥 Pro Tips for Even Faster Deployments

### 1. Use Persistent Storage
Map these volumes in Coolify to skip rebuilding:
```
/app/storage/app → Persistent volume
/app/content → Persistent volume
```

### 2. Disable Unnecessary Build Steps
If you don't change assets often, skip `npm run build`:

Edit `nixpacks.toml`:
```toml
[phases.build]
cmds = [
  'php artisan config:cache',
  'php artisan route:cache', 
  'php artisan view:cache'
]
# npm run build only runs when package.json changes
```

### 3. Use Deployment Hooks
In Coolify, set up:
- **Pre-deployment hook**: None (saves time)
- **Post-deployment hook**: `php artisan horizon:terminate`

---

## 📈 Monitoring Deployment Speed

Check build logs in Coolify for timing:
```bash
# You'll see:
[nixpacks] Installing dependencies... ✓ (5.2s)
[nixpacks] Building assets... ✓ (12.8s)
[nixpacks] Starting application... ✓ (2.1s)
Total: ~20 seconds
```

---

## ⚠️ If Deployments Are Still Slow

### Check these:
1. **Server resources**: Ensure EC2 has enough CPU/RAM
   - Recommended: t3.medium (2 vCPU, 4GB RAM)
   
2. **Network speed**: GitHub → EC2 connection
   - Consider using GitHub Actions for builds

3. **Redis connection**: Slow cache = slow deploys
   - Use local Redis on same EC2 instance

4. **Asset size**: Large CSS/JS bundles
   - Run `npm run build` to check bundle size
   - Consider code splitting

---

## 🎬 Final Coolify Setup (Copy-Paste Ready)

```yaml
Build Pack: Nixpacks

Environment Variables:
APP_ENV=production
APP_DEBUG=false
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
COMPOSER_NO_INTERACTION=1

Port: 8000

Health Check:
  Path: /
  Port: 8000
  Interval: 30

Persistent Volumes:
  /app/storage/app
  /app/content
```

---

## 🆚 Comparison: Dockerfile vs Nixpacks

| Feature | Dockerfile | Nixpacks |
|---------|-----------|----------|
| First deploy | 3-5 min | 2-3 min |
| Code change | 2-3 min | 15-25 sec |
| Dependency change | 3-5 min | 45-60 sec |
| Cache support | Manual | Automatic |
| **Best for** | Production stability | Fast iteration |

---

## ✅ Recommended: NIXPACKS

**Commit these files to GitHub:**
1. `nixpacks.toml` (auto-configuration)
2. `deploy.sh` (optimized post-deploy)
3. `.dockerignore` (faster builds)

**Choose in Coolify:**
- Build Pack: **Nixpacks**
- Enable all cache options
- Use the configuration above

**Expected result:** ⚡ 15-25 second deployments after first build!
