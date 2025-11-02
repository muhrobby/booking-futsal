# CI/CD Asset Handling Guide

## Problem We Fixed

Previously, assets (CSS/JS) would go stale and not update on deployments because:

1. **Old Docker Image Assets**: Docker built assets BEFORE host npm run build
2. **Manifest Mismatch**: HTML referenced old hash but file didn't exist
3. **Bootstrap Cache Lock**: Cache files couldn't be deleted/overwritten
4. **No CSS Result**: Application loaded without styling

## Solution Architecture

### Asset Build Pipeline

```
┌─────────────────────────────────────────────────────────────┐
│ Developer Makes Changes Locally                             │
│ - Update resources/css/app.css                              │
│ - Update resources/js/app.js                                │
│ - Update blade templates                                    │
└────────────────┬────────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────────┐
│ Local Testing (Optional)                                    │
│ $ npm run build                                             │
│ $ npm run dev (or local server)                             │
└────────────────┬────────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────────┐
│ Git Push to GitHub                                          │
│ $ git add .                                                 │
│ $ git commit -m "Update styles"                             │
│ $ git push origin main                                      │
└────────────────┬────────────────────────────────────────────┘
                 │
                 ▼
         ┌───────────────────────────────────────────────────────────┐
         │ GITHUB ACTIONS: Deploy Workflow                          │
         ├───────────────────────────────────────────────────────────┤
         │ Step 1: Checkout Code                                    │
         │ Step 2: Setup PHP 8.2                                    │
         │ Step 3: Install Composer Dependencies                    │
         │ Step 4: Setup Node.js 18                                 │
         │ Step 5: Install NPM Dependencies                         │
         │ Step 6: Generate APP_KEY                                 │
         │ Step 7: *** npm run build ***  ← CRITICAL!               │
         │         Creates: public/build/manifest.json              │
         │         Creates: public/build/assets/*.css/js            │
         │ Step 8: Run Tests                                        │
         │ Step 9: Run Linting                                      │
         │ Step 10: If ALL PASS → Deploy                            │
         └──────────────┬───────────────────────────────────────────┘
                        │ (if tests pass)
                        ▼
         ┌───────────────────────────────────────────────────────────┐
         │ DEPLOY TO VPS (as user: robby)                           │
         ├───────────────────────────────────────────────────────────┤
         │ 1. SSH Connect                                            │
         │ 2. cd /home/robby/stacks/prod/booking-futsal              │
         │ 3. git pull origin main                                   │
         │ 4. npm run build (builds fresh assets locally on VPS)     │
         │ 5. podman-compose down                                    │
         │ 6. podman-compose up -d --build (rebuilds image)          │
         │ 7. Wait 10 seconds                                        │
         │ 8. Copy fresh public/build to container                   │
         │ 9. Run migrations                                         │
         │ 10. Run seeders                                           │
         │ 11. Clear caches (view:clear + optimize:clear)            │
         └──────────────┬───────────────────────────────────────────┘
                        │
                        ▼
         ┌───────────────────────────────────────────────────────────┐
         │ APPLICATION LIVE                                          │
         │ - public/build/assets/app-z1DdQQg4.css ✅                 │
         │ - public/build/assets/app-Bj43h_rG.js ✅                  │
         │ - manifest.json matches ✅                                │
         │ - Cache cleared ✅                                        │
         │ - Styles applied ✅                                       │
         └───────────────────────────────────────────────────────────┘
```

## Key Points to Prevent Asset Issues

### 1. **Always Build Assets Before Deployment**

```bash
# On your machine before push
npm run build

# This creates:
# - public/build/manifest.json (defines all asset hashes)
# - public/build/assets/app-*.css
# - public/build/assets/app-*.js
```

### 2. **GitHub Actions Automatically Builds**

The workflow file (`deploy.yml`) includes:

```yaml
- name: Build frontend assets
  run: npm run build
```

This runs BEFORE tests and deployment.

### 3. **VPS Also Builds**

During deployment, the VPS runs:

```bash
npm run build
```

This ensures assets are fresh on production server.

### 4. **Assets Synced to Container**

After containers start, we explicitly copy fresh assets:

```bash
podman cp public/build <app-container>:/var/www/public/
```

This prevents old Docker image assets from being used.

### 5. **Cache Cleared Properly**

We run BOTH view clear and optimize clear:

```bash
php artisan view:clear      # Clear compiled blade views
php artisan optimize:clear  # Clear config, routes, events caches
```

## Dockerfile Strategy

Our Dockerfile uses a **3-stage build**:

```dockerfile
# Stage 1: Frontend Build (builds assets for fallback)
FROM node:20-alpine AS frontend
RUN npm run build

# Stage 2: PHP Runtime (with assets copied)
FROM php:8.2-fpm
# Copy assets from frontend stage (or use host assets)
COPY --from=frontend /app/public/build /var/www/public/build
```

**Why this works:**
- If host assets are present, they get used
- If not, fallback to Docker-built assets
- Always has something to serve

## Manifest File

The `public/build/manifest.json` is crucial:

```json
{
  "resources/css/app.css": {
    "file": "assets/app-z1DdQQg4.css",
    "src": "resources/css/app.css",
    "isEntry": true
  },
  "resources/js/app.js": {
    "file": "assets/app-Bj43h_rG.js",
    "name": "app",
    "src": "resources/js/app.js",
    "isEntry": true
  }
}
```

**Important:**
- Hash changes when CSS/JS content changes
- Laravel uses this to know which files to load
- Mismatch between manifest and actual files = no styling

## Troubleshooting

### Problem: No CSS on site after deployment

**Check these:**

```bash
# 1. Are assets built?
ls -lah /home/robby/stacks/prod/booking-futsal/public/build/

# 2. Do they exist in container?
podman exec futsal-neo-s-app ls -lah /var/www/public/build/

# 3. Is manifest correct?
cat public/build/manifest.json

# 4. Are caches cleared?
podman exec futsal-neo-s-app php artisan config:cache
podman exec futsal-neo-s-app php artisan view:clear
```

### Solution: Resync assets

```bash
cd /home/robby/stacks/prod/booking-futsal

# Build fresh
npm run build

# Copy to container
podman cp public/build futsal-neo-s-app:/var/www/public/

# Clear caches
podman exec futsal-neo-s-app php artisan optimize:clear
```

## Best Practices Going Forward

✅ **DO:**
- Always test locally before pushing
- Run `npm run build` to verify no errors
- Push code with commit message
- Check GitHub Actions for green checkmark
- Monitor first deployment closely

❌ **DON'T:**
- Modify built assets directly in container
- Skip testing phase
- Force push without reason
- Assume deployment succeeded without checking

## Deployment Checklist

Before each push:

- [ ] Code changes tested locally
- [ ] `npm run build` runs without errors
- [ ] `git push origin main`
- [ ] GitHub Actions shows green checkmark
- [ ] Visit https://kelompok1-de.humahub.my.id
- [ ] Check console for 404 errors on assets
- [ ] Verify styles are applied correctly

## Environment Variables

Make sure `.env` is correct:

```bash
APP_NAME="Futsal Neo S"    # Show in titles
APP_ENV=production         # For optimization
APP_DEBUG=false             # Never true in production
VITE_APP_NAME="Futsal Neo S"  # Used by Vite
```

## Commands Reference

### Local Development

```bash
# Watch mode (auto rebuild on changes)
npm run dev

# Build for production
npm run build

# Build and check output
npm run build && ls -lah public/build/
```

### Production (on VPS)

```bash
# SSH to VPS
ssh robby@<your-ip>

# Navigate to app
cd /home/robby/stacks/prod/booking-futsal

# Manual build
npm run build

# Manual deploy
podman-compose down
podman-compose up -d --build

# Copy assets
podman cp public/build futsal-neo-s-app:/var/www/public/

# Clear caches
podman exec futsal-neo-s-app php artisan optimize:clear
```

## Summary

The new CI/CD pipeline:

1. ✅ Builds assets in GitHub Actions
2. ✅ Tests everything before deploy
3. ✅ Rebuilds on VPS for consistency
4. ✅ Copies fresh assets to container
5. ✅ Clears caches properly
6. ✅ Result: Always fresh, always working

**No more stale assets!** 🎉
