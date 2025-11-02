# CI/CD & Zero Downtime Deployment Guide

## 📋 Overview

Sistem CI/CD yang telah dibuat menggunakan **GitHub Actions** untuk automation dan **Blue-Green Deployment** strategy untuk mencapai **zero downtime deployment**. Website Anda akan selalu online tanpa downtime saat ada update.

## 🎯 Fitur Utama

- ✅ **Automated Testing** - Setiap push ditest otomatis
- ✅ **Zero Downtime** - Blue-green deployment strategy
- ✅ **Auto Rollback** - Jika deployment gagal, otomatis kembali ke versi sebelumnya
- ✅ **Database Migrations** - Automatic schema updates
- ✅ **Cache Optimization** - Config/route/view caching
- ✅ **Health Checks** - Verifikasi aplikasi berjalan dengan baik
- ✅ **Old Release Cleanup** - Otomatis hapus release lama

## 📦 Workflow Files

### 1. `.github/workflows/deploy.yml`
- Trigger: Push ke branch `main` atau `master`
- Melakukan:
  - PHP syntax check
  - Install dependencies
  - Build frontend assets
  - Run automated tests
  - Deploy ke VPS jika semua test passed

### 2. `deploy.sh`
- Zero downtime deployment script
- Symlink-based switching antara releases
- Automatic migration running
- Health checks sebelum switching
- Rollback otomatis jika ada error

### 3. `setup-vps.sh`
- Setup script untuk VPS sekali pakai
- Install semua dependencies (PHP, Node, Nginx, PostgreSQL)
- Configure PHP-FPM, Nginx, Supervisor
- Setup SSL dengan Let's Encrypt

## 🚀 Setup Guide

### Step 1: Prepare VPS

```bash
# SSH ke VPS Anda
ssh root@your_vps_ip

# Download dan jalankan setup script
curl -O https://raw.githubusercontent.com/yourusername/booking-futsal/main/setup-vps.sh
chmod +x setup-vps.sh
sudo ./setup-vps.sh
```

Script akan:
1. Update system packages
2. Install PHP 8.2 + extensions
3. Install Nginx, Node.js, Composer
4. Setup PostgreSQL database
5. Create deploy user dan directories
6. Configure PHP-FPM pools
7. Setup Nginx virtual host
8. Configure Supervisor untuk queue workers

### Step 2: Manual VPS Configuration

Setelah script selesai, lakukan ini secara manual:

#### Update .env File
```bash
sudo nano /home/deploy/projects/futsal/shared/.env
```

Update nilai:
- `APP_URL=https://yourdomain.com`
- `DB_PASSWORD=your_secure_password`
- `MAIL_*` settings
- Semua API keys yang diperlukan

#### Setup SSL Certificate
```bash
sudo certbot certonly --nginx -d yourdomain.com
```

Update paths di `/etc/nginx/sites-available/futsal` dengan certificate path yang baru.

#### Reload Nginx
```bash
sudo systemctl reload nginx
```

### Step 3: Setup GitHub Secrets

Di GitHub repository Settings > Secrets and variables > Actions:

1. **DEPLOY_KEY**
   ```bash
   # Get dari VPS
   sudo cat /home/deploy/.ssh/id_rsa
   ```
   Copy seluruh private key (include BEGIN dan END lines)

2. **VPS_HOST**
   - Gunakan IP address atau domain VPS Anda
   - Contoh: `123.45.67.89` atau `vps.yourdomain.com`

3. **VPS_USER**
   - Isi: `deploy`

4. **VPS_PORT**
   - Isi: `22` (atau port SSH custom Anda)

### Step 4: Setup SSH Access

Di VPS, add GitHub Actions SSH key:

```bash
# Login sebagai deploy user
su - deploy

# Add GitHub Actions public key ke authorized_keys
echo "ssh-rsa AAAA... (public key dari GitHub Actions)" >> ~/.ssh/authorized_keys

# Verify
ssh -i ~/.ssh/id_rsa deploy@localhost
```

### Step 5: First Deployment

Sekarang push code ke GitHub:

```bash
git add .
git commit -m "Setup CI/CD pipeline"
git push origin main
```

GitHub Actions akan:
1. Run tests
2. Build assets
3. Deploy ke VPS dengan zero downtime
4. Run migrations
5. Verify health checks

Monitor di GitHub > Actions tab.

## 📊 Deployment Directory Structure

```
/home/deploy/projects/futsal/
├── current -> ./releases/20241102_131500  (symlink ke versi aktif)
├── releases/
│   ├── 20241102_131500/  (Latest release)
│   ├── 20241102_120000/
│   ├── 20241101_180000/
│   └── ...
└── shared/
    ├── .env
    ├── storage/
    │   ├── app/
    │   ├── logs/
    │   └── framework/
    └── bootstrap/cache/
```

## 🔄 How Zero Downtime Deployment Works

### Current Approach (Blue-Green)

1. **Clone new release** ke `/releases/TIMESTAMP/`
2. **Install dependencies** (composer + npm)
3. **Build assets** tanpa touch current running app
4. **Run migrations** (database schema updates)
5. **Optimize cache** (config/route/view)
6. **Switch symlink** dari `/current` ke release baru
7. **Reload PHP-FPM dan Nginx** (graceful restart)
8. **Health checks** untuk verify
9. **Cleanup old releases** (keep last 5)

### Kenapa Zero Downtime?

- **Database-first**: Migrations run sebelum switch, backwards compatible
- **Atomic switch**: Symlink change instantaneous
- **Graceful reload**: Nginx/PHP-FPM graceful restart, existing connections complete
- **Rollback ready**: Jika gagal, symlink switch balik ke versi lama
- **No shared session**: Cookie-based sessions, tidak perlu sticky sessions

## 🚨 Rollback Manual

Jika ada masalah, rollback ke versi sebelumnya:

```bash
# SSH ke VPS
ssh deploy@your_vps_ip

# List available releases
ls -la /home/deploy/projects/futsal/releases/

# Rollback ke release tertentu
cd /home/deploy/projects/futsal
rm current
ln -s ./releases/TIMESTAMP_LAMA current

# Reload Nginx dan PHP-FPM
sudo systemctl reload php8.2-fpm
sudo systemctl reload nginx
```

## 📝 Monitoring Logs

### GitHub Actions
- Settings > Secrets and variables > Actions
- Actions tab - lihat build logs

### VPS Deployment
```bash
# Nginx error log
sudo tail -f /var/log/nginx/futsal_error.log

# PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log

# Laravel application logs
sudo tail -f /home/deploy/projects/futsal/shared/storage/logs/laravel.log

# Supervisor queue workers
sudo tail -f /var/log/supervisor/futsal-worker.log
```

## 🔧 Troubleshooting

### Deployment stuck/failed?

1. Check GitHub Actions logs
2. SSH ke VPS dan check:
   ```bash
   # Check last deployment
   ls -la /home/deploy/projects/futsal/releases/ | head -n 5
   
   # Check current symlink
   ls -la /home/deploy/projects/futsal/current
   
   # Check PHP-FPM status
   sudo systemctl status php8.2-fpm
   
   # Check Nginx status
   sudo systemctl status nginx
   ```

### SSH key authentication failed?

```bash
# Verify key di VPS
sudo -u deploy cat /home/deploy/.ssh/authorized_keys

# Check SSH config
sudo cat /etc/ssh/sshd_config | grep "PasswordAuthentication"

# Reload SSH service
sudo systemctl reload ssh
```

### Database migration failed?

```bash
# SSH ke VPS
cd /home/deploy/projects/futsal/current

# Check migration status
php artisan migrate:status

# Rollback last migration
php artisan migrate:rollback

# Re-run migrations
php artisan migrate --force
```

### Application won't start?

```bash
# Check .env file
cat /home/deploy/projects/futsal/shared/.env

# Check storage permissions
ls -la /home/deploy/projects/futsal/shared/storage

# Check PHP-FPM pool
sudo php-fpm8.2 -t

# Check Nginx config
sudo nginx -t
```

## 📈 Performance Optimization

### Caching Strategy
```bash
# Config cache (run otomatis)
php artisan config:cache

# Route cache (run otomatis)
php artisan route:cache

# View cache (run otomatis)
php artisan view:cache

# Query cache
php artisan cache:clear
```

### Database Optimization
```bash
# Prune old data (via cron)
php artisan model:prune

# Backup database (via cron)
php artisan backup:run --only-files
```

## 🔐 Security Best Practices

1. **SSH Key Security**
   - Never share private key
   - Rotate keys regularly
   - Use strong passphrases

2. **Environment Variables**
   - Keep .env in shared/ not in repo
   - Use strong DB passwords
   - Rotate API keys periodically

3. **SSL/TLS**
   - Enable HTTPS (Let's Encrypt)
   - Auto-renew certificates
   - Use security headers

4. **Access Control**
   - Limit SSH access by IP if possible
   - Use deploy user (not root)
   - Monitor access logs

## 📞 Support & Issues

### Common Issues

**Issue**: "Permission denied (publickey)"
- Check GitHub secret DEPLOY_KEY format
- Ensure deploy user has .ssh directory
- Verify authorized_keys file permissions

**Issue**: "Nginx: [error] open() "/run/nginx.pid" failed"
- Run: `sudo systemctl restart nginx`
- Check: `sudo nginx -t`

**Issue**: "SQLSTATE[HY000]: General error"
- Check database credentials in .env
- Verify PostgreSQL is running
- Check database exists

**Issue**: "Disk space full after deployments"
- Run cleanup: `sudo rm -rf /home/deploy/projects/futsal/releases/OLD_*`
- Or modify script to keep fewer releases

## 🎊 Next Steps

1. ✅ Push code to GitHub
2. ✅ Monitor first deployment in Actions
3. ✅ Test website at https://yourdomain.com
4. ✅ Setup monitoring/alerting
5. ✅ Configure backups
6. ✅ Document custom environment variables

## 📚 Additional Resources

- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [Laravel Deployment](https://laravel.com/docs/deployment)
- [Nginx Configuration](https://nginx.org/en/docs/)
- [PostgreSQL Management](https://www.postgresql.org/docs/)
- [SSL Let's Encrypt](https://certbot.eff.org/)

---

**Last Updated**: 2024-11-02
**Status**: Production Ready ✅
