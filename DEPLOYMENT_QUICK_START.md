# CI/CD Pipeline & Zero Downtime Deployment

## 📋 Quick Start

### 1. VPS Setup (One-time)
```bash
# SSH to your VPS
ssh root@your_vps_ip

# Download and run setup
curl -O https://raw.githubusercontent.com/yourusername/booking-futsal/main/setup-vps.sh
chmod +x setup-vps.sh
sudo ./setup-vps.sh
```

### 2. GitHub Secrets Configuration

Add these secrets in GitHub repository:

- **DEPLOY_KEY** - SSH private key from `/home/deploy/.ssh/id_rsa` on VPS
- **VPS_HOST** - Your VPS IP or domain
- **VPS_USER** - `deploy` (user created by setup script)
- **VPS_PORT** - `22` (or your custom SSH port)

### 3. Push and Deploy

```bash
git push origin main
# GitHub Actions will automatically deploy!
```

## 📁 Files Overview

### `.github/workflows/deploy.yml`
- **Purpose**: GitHub Actions workflow
- **Triggers**: Push to main/master branch
- **Steps**:
  1. Run tests
  2. Build assets
  3. Deploy to VPS

### `setup-vps.sh`
- **Purpose**: One-time VPS setup
- **Installs**: PHP 8.2, Nginx, PostgreSQL, Node.js, Composer
- **Configures**: PHP-FPM pools, Nginx vhosts, Supervisor workers
- **Run once**: `sudo ./setup-vps.sh`

### `deploy.sh`
- **Purpose**: Zero-downtime deployment
- **Location**: Runs on VPS (called by GitHub Actions)
- **Strategy**: Blue-green deployment with symlink switching
- **Features**:
  - Clone new release
  - Install dependencies
  - Run migrations
  - Optimize cache
  - Switch with zero downtime
  - Auto-rollback on failure
  - Cleanup old releases

### `health-check.sh`
- **Purpose**: Monitor application health
- **Checks**:
  - Disk space, Memory, CPU
  - Web server status
  - Database connection
  - HTTP response
  - Queue workers
  - Application logs
- **Usage**: `./health-check.sh` or via cron

## 🚀 Deployment Process

```
GitHub Push
    ↓
GitHub Actions Triggered
    ↓
Run Tests (PHP, Assets)
    ↓
Build Frontend Assets
    ↓
All Tests Passed?
    ├─ NO → Deployment Stops, Create Issue
    └─ YES → Continue
        ↓
        SSH to VPS & Run deploy.sh
            ↓
            Clone new release
            Install dependencies
            Build assets
            Copy shared files
            Run migrations
            Optimize cache
            Switch symlink (ATOMIC)
            Reload PHP-FPM & Nginx
            Health checks
            Cleanup old releases
            ↓
        All OK?
        ├─ NO → Auto-rollback to previous version
        └─ YES → Deployment Complete ✅

RESULT: Website updated with ZERO DOWNTIME
```

## 📊 Directory Structure on VPS

```
/home/deploy/projects/futsal/
├── current → ./releases/20241102_131500/  (symlink to active version)
├── releases/
│   ├── 20241102_131500/  (Latest - ACTIVE)
│   ├── 20241102_120000/
│   ├── 20241101_180000/
│   └── ...
└── shared/
    ├── .env
    ├── storage/
    ├── bootstrap/cache/
```

## 🔍 Monitoring

### Real-time Logs

```bash
# SSH to VPS
ssh deploy@your_vps_ip

# Watch Nginx errors
sudo tail -f /var/log/nginx/futsal_error.log

# Watch Laravel logs
tail -f ~/projects/futsal/shared/storage/logs/laravel.log

# Watch PHP-FPM
sudo tail -f /var/log/php8.2-fpm.log

# Watch deployment
ls -lah ~/projects/futsal/releases/ | head
```

### Health Endpoint

```bash
# Check health from anywhere
curl https://yourdomain.com/health

# JSON output shows:
{
  "status": "healthy",
  "timestamp": "2024-11-02T13:15:00Z",
  "checks": {
    "database": "ok",
    "cache": "ok",
    "storage": "ok"
  }
}
```

## 🔄 Manual Rollback

```bash
# SSH to VPS
ssh deploy@your_vps_ip

# List releases
ls -1 ~/projects/futsal/releases/ | sort -r

# Rollback to previous
cd ~/projects/futsal
rm current
ln -s ./releases/TIMESTAMP current

# Reload services
sudo systemctl reload php8.2-fpm
sudo systemctl reload nginx
```

## 🛠️ Troubleshooting

### Deployment Failed?

1. Check GitHub Actions logs
2. SSH to VPS:
   ```bash
   cd ~/projects/futsal
   # Check current symlink
   ls -la current
   # Check latest release
   ls -lah releases/ | head
   # Check error logs
   sudo tail -100 /var/log/nginx/futsal_error.log
   tail -100 ~/projects/futsal/shared/storage/logs/laravel.log
   ```

### Permission Denied?

```bash
# Fix permissions
sudo chown -R deploy:deploy ~/projects/futsal
chmod -R 755 ~/projects/futsal
chmod -R 775 ~/projects/futsal/shared/storage
chmod -R 775 ~/projects/futsal/shared/bootstrap/cache
```

### Database Migration Failed?

```bash
# Check status
php ~/projects/futsal/current/artisan migrate:status

# Rollback
php ~/projects/futsal/current/artisan migrate:rollback

# Re-run
php ~/projects/futsal/current/artisan migrate --force
```

### Website Down?

1. Check processes:
   ```bash
   sudo systemctl status nginx
   sudo systemctl status php8.2-fpm
   ```

2. Restart services:
   ```bash
   sudo systemctl restart nginx
   sudo systemctl restart php8.2-fpm
   ```

3. Check logs:
   ```bash
   sudo tail -f /var/log/nginx/futsal_error.log
   ```

## 📈 Performance

### Cache Optimization (Automatic)
- Config caching reduces bootstrap time
- Route caching improves routing
- View caching improves template rendering

### Database
- Auto-migrations only run new migrations
- Backwards compatible migrations
- Connection pooling via PHP

### Assets
- Frontend built locally (not on VPS)
- Only static files deployed
- Gzip compression enabled

## 🔐 Security

### SSH Security
- Deploy key never logged
- Private key in GitHub Secrets only
- Public key authorized on VPS

### Database Security
- PostgreSQL local-only connection
- Strong password required in .env
- No exposed credentials in repo

### Application Security
- HTTPS enforced (Let's Encrypt)
- Security headers enabled
- CSRF protection active
- Input validation enabled

## 📞 Support

### Check Deployment Status
```bash
# On GitHub
Settings → Actions → Deployments
```

### Get Deployment Logs
```bash
# SSH to VPS
cat deploy.log  # If exists

# Or check system
sudo journalctl -u nginx -f
sudo journalctl -u php8.2-fpm -f
```

### Common Commands

```bash
# SSH to VPS
ssh deploy@your_vps_ip

# Run migrations manually
php projects/futsal/current/artisan migrate

# Clear cache
php projects/futsal/current/artisan cache:clear

# Run artisan command
php projects/futsal/current/artisan tinker

# Check queue
php projects/futsal/current/artisan queue:work --help

# View application logs
tail -f projects/futsal/shared/storage/logs/laravel.log
```

## ✅ Deployment Checklist

- [ ] VPS setup script run successfully
- [ ] .env updated with proper values
- [ ] SSL certificate installed
- [ ] Nginx reloaded after SSL setup
- [ ] Database created and user configured
- [ ] GitHub Secrets configured (4 secrets)
- [ ] SSH key added to VPS authorized_keys
- [ ] First push to main triggers deployment
- [ ] Website accessible at domain
- [ ] Health check endpoint returns 200
- [ ] Database queries working
- [ ] Logs being written

## 🎊 After First Deployment

1. ✅ Verify website works
2. ✅ Test health endpoints
3. ✅ Monitor logs for errors
4. ✅ Setup uptime monitoring
5. ✅ Configure database backups
6. ✅ Setup error alerts
7. ✅ Document custom environment variables

---

**Ready to deploy?** Push your code to GitHub!

```bash
git push origin main
```

Monitor the deployment in GitHub Actions tab. Your website will be live with zero downtime! 🚀
