# 🚀 CI/CD & Zero Downtime Deployment - Complete Setup

## 📋 What's Been Created

### 1. GitHub Actions Workflow
**File**: `.github/workflows/deploy.yml`
- Automated testing on every push
- Builds frontend assets
- Deploys to VPS with zero downtime
- Auto-rollback on failure

### 2. Deployment Scripts

#### `deploy.sh` (Main Deployment Script)
- Blue-green deployment strategy
- Zero downtime switching
- Automatic database migrations
- Cache optimization
- Health checks before going live
- Auto-rollback if something fails
- Cleanup of old releases

#### `setup-vps.sh` (One-time VPS Setup)
- Installs all dependencies (PHP 8.2, Nginx, PostgreSQL, Node.js)
- Configures PHP-FPM pools
- Sets up Nginx virtual host
- Configures Supervisor for queue workers
- Creates deployment directories

#### `health-check.sh` (Monitoring Script)
- Monitors disk space, memory, CPU
- Checks database connectivity
- Verifies web server status
- Monitors queue workers
- Checks application logs
- Sends alerts on issues

### 3. Application Endpoints

**File**: `app/Http/Controllers/HealthController.php`
- `/health` - Full health check
- `/health/ready` - Kubernetes readiness probe
- `/health/live` - Kubernetes liveness probe

### 4. Configuration Examples

#### `.env.production.example`
- Complete production environment template
- Database, Redis, mail, storage settings
- Security configurations

#### `nginx.conf.example`
- Complete Nginx configuration
- SSL/TLS setup
- Security headers
- Rate limiting
- Gzip compression
- Caching rules

### 5. Documentation

- `CICD_DEPLOYMENT.md` - Complete CI/CD guide
- `DEPLOYMENT_QUICK_START.md` - Quick reference
- `verify-cicd.sh` - Setup verification script

## 🎯 How Zero Downtime Works

### The Blue-Green Strategy

```
Old Application (Blue)
    ↓
New Release Prepared (Green)
    ↓
Switch Symlink (Atomic)
    ↓
Graceful PHP-FPM & Nginx Reload
    ↓
Website Online with New Version
(No downtime, existing requests complete)
```

### Why This Works

1. **Atomic Symlink Switch**: Single filesystem operation
2. **Graceful Reload**: PHP-FPM/Nginx finish existing connections
3. **Database Migrations**: Run before switch, backwards compatible
4. **Health Checks**: Verify before going live
5. **Quick Rollback**: If something wrong, switch back instantly

## 📦 Directory Structure After Deployment

```
/home/deploy/projects/futsal/
├── current → ./releases/20241102_131500/     ← Symlink to active version
├── releases/
│   ├── 20241102_131500/                      ← Latest (ACTIVE)
│   │   ├── app/
│   │   ├── bootstrap/
│   │   ├── config/
│   │   ├── database/
│   │   ├── public/
│   │   ├── resources/
│   │   ├── routes/
│   │   ├── storage → ../../shared/storage/
│   │   ├── .env → ../../shared/.env
│   │   └── ...
│   ├── 20241102_120000/
│   ├── 20241101_180000/
│   └── ...
└── shared/
    ├── .env                                  ← Persistent environment
    ├── storage/
    │   ├── app/
    │   ├── logs/
    │   └── framework/
    └── bootstrap/cache/
```

## 🚀 Step-by-Step Setup Guide

### Phase 1: Local Repository

```bash
cd /path/to/booking-futsal

# Everything already added to repo
git add .github/workflows/deploy.yml
git add deploy.sh setup-vps.sh health-check.sh
git add app/Http/Controllers/HealthController.php
git add CICD_DEPLOYMENT.md DEPLOYMENT_QUICK_START.md
git add .env.production.example nginx.conf.example
git commit -m "Add CI/CD pipeline with zero downtime deployment"
git push origin main
```

### Phase 2: VPS Setup (One-Time)

```bash
# SSH to your VPS as root
ssh root@your_vps_ip

# Download setup script
curl -O https://raw.githubusercontent.com/yourusername/booking-futsal/main/setup-vps.sh
chmod +x setup-vps.sh

# Run setup (takes 5-10 minutes)
sudo ./setup-vps.sh

# Follow the on-screen prompts
# - Create deploy user
# - Install PHP, Nginx, PostgreSQL, Node.js
# - Configure everything automatically
```

### Phase 3: GitHub Secrets

Go to GitHub repository → Settings → Secrets and variables → Actions

Add 4 secrets:

| Secret Name | Value |
|------------|-------|
| `DEPLOY_KEY` | SSH private key from VPS `/home/deploy/.ssh/id_rsa` |
| `VPS_HOST` | Your VPS IP or domain (e.g., `123.45.67.89`) |
| `VPS_USER` | `deploy` |
| `VPS_PORT` | `22` (your SSH port) |

### Phase 4: Manual VPS Configuration

SSH to VPS:

```bash
ssh deploy@your_vps_ip

# Edit .env file
sudo nano /home/deploy/projects/futsal/shared/.env

# Update values:
# - APP_URL=https://yourdomain.com
# - DB_PASSWORD=strong_password
# - MAIL settings
# - API keys

# Exit editor (Ctrl+X, Y, Enter)
```

### Phase 5: SSL Certificate

```bash
# Still on VPS as deploy user
sudo certbot certonly --nginx -d yourdomain.com

# Update Nginx config
sudo nano /etc/nginx/sites-available/futsal

# Update SSL certificate paths, then:
sudo nginx -t
sudo systemctl reload nginx
```

### Phase 6: Database Setup

```bash
# As root on VPS
sudo -u postgres psql

# Inside psql:
CREATE ROLE futsal WITH LOGIN PASSWORD 'your_db_password';
CREATE DATABASE futsal OWNER futsal;
ALTER ROLE futsal CREATEDB;
\q

# Update .env with password
sudo nano /home/deploy/projects/futsal/shared/.env
```

### Phase 7: First Deployment

```bash
# From your local machine
git push origin main

# Watch deployment in GitHub Actions
# https://github.com/yourusername/booking-futsal/actions

# Check if deployed successfully
curl https://yourdomain.com/health
```

## 📊 What Happens on Each Push

```
1. You push to GitHub
    ↓
2. GitHub Actions triggered
    ↓
3. Run tests:
   - PHP syntax check
   - Composer validation
   - Install dependencies
   - Build frontend (npm)
   - Database migrations (test)
   - Run PHPUnit tests (if any)
    ↓
4. All tests passed?
   ├─ NO → Workflow stops, PR gets comment with failures
   └─ YES → Continue to deployment
    ↓
5. GitHub SSH to VPS and runs deploy.sh
    ↓
6. On VPS:
   - Clone new code to releases/TIMESTAMP/
   - Run composer install --no-dev
   - Run npm ci && npm run build
   - Copy .env from shared
   - Run php artisan migrate
   - Run php artisan config:cache, etc.
   - Switch symlink current → new release
   - Reload PHP-FPM (graceful)
   - Reload Nginx (graceful)
   - Run health checks
   - Cleanup old releases (keep last 5)
    ↓
7. Website live with new code
   (Zero downtime achieved!)
```

## 🔄 Rollback

If something goes wrong, automatic rollback happens:

```bash
# Automatic during deployment
# If health check fails → switch back to previous symlink

# Manual rollback
ssh deploy@your_vps_ip
cd ~/projects/futsal

# List versions
ls -1 releases/ | sort -r

# Rollback to specific version
rm current
ln -s ./releases/TIMESTAMP_OLD current

# Reload services
sudo systemctl reload php8.2-fpm nginx
```

## ✅ Testing Checklist

After first deployment:

- [ ] Website loads at https://yourdomain.com
- [ ] `/health` endpoint returns 200
- [ ] Database queries work
- [ ] Login/register works
- [ ] Admin dashboard accessible
- [ ] File uploads work
- [ ] Queue workers running
- [ ] Logs being written
- [ ] Email sending works
- [ ] No errors in application logs

## 📈 Monitoring

### Real-time Logs

```bash
ssh deploy@your_vps_ip

# Nginx errors
sudo tail -f /var/log/nginx/futsal_error.log

# Application logs
tail -f ~/projects/futsal/shared/storage/logs/laravel.log

# PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log
```

### Health Check from CLI

```bash
# Local or remote
curl https://yourdomain.com/health

# Response:
{
  "status": "healthy",
  "timestamp": "2024-11-02T13:15:00Z",
  "version": "1.0.0",
  "environment": "production",
  "checks": {
    "database": "ok",
    "cache": "ok",
    "storage": "ok"
  }
}
```

## 🔐 Security Features

✅ SSH key authentication (no passwords)
✅ HTTPS enforced (Let's Encrypt SSL)
✅ Security headers enabled
✅ Rate limiting configured
✅ Gzip compression enabled
✅ Database credentials in .env (not in code)
✅ Auto-rollback on failure
✅ Health checks verify application
✅ Logs monitored for errors

## 🛠️ Maintenance Tasks

### Regular Backups

```bash
# Add to crontab on VPS
0 2 * * * cd ~/projects/futsal/current && php artisan backup:run --only-files
```

### Database Backups

```bash
# Backup database daily
0 1 * * * pg_dump -U futsal futsal | gzip > /backups/futsal-$(date +\%Y\%m\%d).sql.gz
```

### Old Release Cleanup

```bash
# Deploy script auto-cleans, but manual cleanup:
rm -rf ~/projects/futsal/releases/OLD_TIMESTAMP
```

### Certificate Renewal

```bash
# Auto-renews with Let's Encrypt
# But can manually check:
sudo certbot renew --dry-run
```

## 📞 Troubleshooting Quick Reference

| Issue | Solution |
|-------|----------|
| Deployment stuck | Check GitHub Actions logs |
| Website shows 500 | Check `/storage/logs/laravel.log` |
| Database migration failed | SSH and run `php artisan migrate:rollback` |
| SSL certificate error | Check Let's Encrypt cert paths in Nginx config |
| High memory usage | Check `ps aux` for stuck processes, restart PHP-FPM |
| Database won't connect | Verify `.env` DB credentials match PostgreSQL setup |

## 🎊 You're All Set!

Your application now has:

✅ **Automated Testing** - On every push
✅ **Automated Deployment** - To production VPS
✅ **Zero Downtime** - Blue-green strategy
✅ **Auto Rollback** - On failure
✅ **Health Monitoring** - Multiple endpoints
✅ **Easy Rollback** - Manual if needed
✅ **Database Migrations** - Automated
✅ **Cache Optimization** - Automatic
✅ **Security** - SSL, headers, rate limiting
✅ **Production Ready** - Full configuration

## 🚀 Next Steps

1. ✅ Verify all files are in GitHub
2. ✅ Push to GitHub to trigger first deployment
3. ✅ Monitor deployment in Actions tab
4. ✅ Test website at your domain
5. ✅ Setup DNS if not done
6. ✅ Configure email if needed
7. ✅ Setup monitoring/alerting
8. ✅ Configure backups
9. ✅ Document any custom setup
10. ✅ Enjoy zero-downtime deployments! 🎉

---

**Last Updated**: 2024-11-02
**Status**: Production Ready ✅
**Deployment Strategy**: Blue-Green (Zero Downtime)
**Version Control**: GitHub
**Automation**: GitHub Actions
**Infrastructure**: VPS with Nginx + PHP-FPM + PostgreSQL
