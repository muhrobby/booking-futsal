# 🚀 Best Practice CI/CD Setup untuk Booking Futsal

**Status**: ✅ Fully Configured dengan GitHub Actions + Zero Downtime Deployment

---

## 📋 CURRENT STATUS

✅ GitHub Actions workflow sudah ada (`.github/workflows/deploy.yml`)
✅ Deployment scripts sudah ada (`deploy.sh`, `setup-vps.sh`, `health-check.sh`)
✅ Latest code pulled dari GitHub
✅ Ready untuk deploy ke VPS dengan ZERO downtime

---

## 🎯 Architecture

```
┌─────────────────────────────────────────────────────────────┐
│ Developer commits code to main branch                       │
└──────────────────────┬──────────────────────────────────────┘
                       ↓
┌─────────────────────────────────────────────────────────────┐
│ GitHub Actions CI/CD Pipeline Triggered                    │
│                                                              │
│ 1. Run Tests (PHP, Laravel, JavaScript)                    │
│ 2. Build Frontend Assets (npm run build)                   │
│ 3. Check Code Quality                                      │
│ 4. Trigger Deployment to VPS                              │
└──────────────────────┬──────────────────────────────────────┘
                       ↓
┌─────────────────────────────────────────────────────────────┐
│ Deploy.sh on VPS (Blue-Green Strategy)                     │
│                                                              │
│ 1. Clone release dari GitHub (Green)                       │
│ 2. Install dependencies                                    │
│ 3. Build assets                                            │
│ 4. Run database migrations (safe)                          │
│ 5. Run health checks                                       │
│ 6. Switch symlink (atomic, 0 downtime)                     │
│ 7. Graceful reload PHP-FPM & Nginx                         │
│ 8. Cleanup old releases                                    │
└──────────────────────┬──────────────────────────────────────┘
                       ↓
┌─────────────────────────────────────────────────────────────┐
│ ✅ Application LIVE with new version                        │
│ ✅ No downtime                                              │
│ ✅ Can rollback instantly if needed                         │
│ ✅ Database migrations applied safely                       │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔧 PART 1: Setup SSH Deploy User (VPS Only)

### 1a. SSH ke VPS sebagai root
```bash
ssh root@YOUR_VPS_IP
```

### 1b. Create deploy user
```bash
# Create user
useradd -m -s /bin/bash deploy

# Add to sudoers (no password)
echo "deploy ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers.d/deploy

# Create SSH directory
sudo -u deploy mkdir -p /home/deploy/.ssh
```

### 1c. Add GitHub Deploy Key (atau SSH key Anda)
```bash
# Option 1: Add your SSH public key
cat >> /home/deploy/.ssh/authorized_keys << 'EOF'
ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQC... (your public key)
EOF

# Set correct permissions
chmod 700 /home/deploy/.ssh
chmod 600 /home/deploy/.ssh/authorized_keys
chown -R deploy:deploy /home/deploy/.ssh

# Test SSH login
ssh deploy@YOUR_VPS_IP
```

---

## 🔧 PART 2: Setup GitHub Secrets (untuk CI/CD)

### 2a. Buka GitHub Settings
https://github.com/muhrobby/booking-futsal/settings/secrets/actions

### 2b. Add Secrets

```
SECRET NAME:           DEPLOY_HOST
SECRET VALUE:          YOUR_VPS_IP
DESCRIPTION:           VPS IP address

---

SECRET NAME:           DEPLOY_USER
SECRET VALUE:          deploy
DESCRIPTION:           SSH user for deployment

---

SECRET NAME:           DEPLOY_KEY
SECRET VALUE:          (paste your private SSH key)
DESCRIPTION:           SSH private key for deploy user

---

SECRET NAME:           DEPLOY_PATH
SECRET VALUE:          /home/deploy/projects/futsal
DESCRIPTION:           Base deployment directory on VPS

---

SECRET NAME:           DEPLOY_PORT
SECRET VALUE:          22
DESCRIPTION:           SSH port (default 22)
```

### How to add SSH key:
```bash
# Generate if tidak ada
ssh-keygen -t ed25519 -C "deploy-key"

# Display private key untuk copy ke GitHub
cat ~/.ssh/id_ed25519
```

---

## 🔧 PART 3: One-Time VPS Setup

### 3a. SSH ke VPS
```bash
ssh root@YOUR_VPS_IP
```

### 3b. Run setup script
```bash
# Download dari GitHub
curl -O https://raw.githubusercontent.com/muhrobby/booking-futsal/main/setup-vps.sh
chmod +x setup-vps.sh

# Jalankan (takes ~10 minutes)
sudo ./setup-vps.sh
```

Atau manual:
```bash
sudo -u deploy bash << 'EOF'
mkdir -p /home/deploy/projects/futsal/{releases,shared}
mkdir -p /home/deploy/projects/futsal/shared/{storage,bootstrap/cache}
cd /home/deploy/projects/futsal
git clone https://github.com/muhrobby/booking-futsal.git releases/initial
ln -s releases/initial current
EOF
```

### 3c. Setup environment
```bash
sudo -u deploy cp /home/deploy/projects/futsal/.env.production.example \
                     /home/deploy/projects/futsal/shared/.env

# Edit dan sesuaikan
sudo -u deploy nano /home/deploy/projects/futsal/shared/.env
```

---

## 📤 PART 4: Setup Nginx & PHP-FPM (VPS)

### 4a. Copy Nginx config
```bash
sudo cp /home/deploy/projects/futsal/nginx.conf.example \
        /etc/nginx/sites-available/futsal

# Sesuaikan domain
sudo nano /etc/nginx/sites-available/futsal
```

### 4b. Enable & restart
```bash
sudo ln -s /etc/nginx/sites-available/futsal /etc/nginx/sites-enabled/futsal
sudo nginx -t
sudo systemctl restart nginx
```

---

## 🚀 PART 5: Manual Test Deployment (First Time)

### 5a. SSH ke deploy user
```bash
ssh deploy@YOUR_VPS_IP
```

### 5b. Test deploy script
```bash
cd /home/deploy/projects/futsal

# Make deploy.sh executable
chmod +x deploy.sh

# Run deployment
./deploy.sh
```

Output should be:
```
✅ Release cloned
✅ Dependencies installed
✅ Assets built
✅ Database migrations done
✅ Health checks passed
✅ Switched to new release
✅ Cleanup completed
✅ Deployment successful!
```

---

## ✅ PART 6: First Automatic Deployment

### 6a. Push ke GitHub
```bash
cd /home/robby/stacks/prod/booking-futsal

# Check status
git status

# Add files (jika ada changes)
git add .
git commit -m "Update CI/CD configuration"
git push origin main
```

### 6b. Watch GitHub Actions
1. Buka: https://github.com/muhrobby/booking-futsal/actions
2. Lihat workflow running
3. Check logs untuk debugging jika ada error

### 6c. View deployment
```bash
# SSH ke VPS
ssh deploy@YOUR_VPS_IP

# Check current release
ls -la /home/deploy/projects/futsal/current/

# Check logs
tail -100f /home/deploy/projects/futsal/shared/storage/logs/laravel.log
```

---

## 📊 How It Works

### When you push to main:

1. **GitHub Actions starts**
   - Checks out your code
   - Installs dependencies
   - Runs tests
   - Builds frontend assets
   - If all pass → triggers deployment

2. **Deployment to VPS**
   - SSH ke deploy user
   - Run `/home/deploy/projects/futsal/deploy.sh`
   - Blue-green strategy:
     - Prepare new release (Green)
     - Run migrations
     - Health checks
     - Switch symlink (atomic, 0 downtime)
     - Graceful reload services
     - Cleanup old releases

3. **Result**
   - Application updated
   - Zero downtime
   - Rollback ready (previous version still there)

---

## 🔄 Rollback (jika ada masalah)

```bash
# SSH ke VPS
ssh deploy@YOUR_VPS_IP

# List releases
ls -la /home/deploy/projects/futsal/releases/

# Rollback ke release sebelumnya
ln -sfn /home/deploy/projects/futsal/releases/PREVIOUS_RELEASE \
        /home/deploy/projects/futsal/current

# Reload Nginx & PHP-FPM
sudo systemctl reload nginx
sudo systemctl reload php-fpm
```

---

## 📈 Monitoring & Health Checks

### 1. Application Health
```bash
curl http://YOUR_VPS_IP/health
```

Response:
```json
{
  "status": "ok",
  "database": "ok",
  "storage": "ok",
  "queue": "ok",
  "timestamp": "2024-11-02T12:00:00Z"
}
```

### 2. Monitor deployment script
```bash
# SSH ke deploy user
ssh deploy@YOUR_VPS_IP

# Run health check
./health-check.sh

# Or continuous monitoring
watch -n 5 ./health-check.sh
```

### 3. View logs
```bash
# Application logs
tail -f /home/deploy/projects/futsal/shared/storage/logs/laravel.log

# Nginx logs
sudo tail -f /var/log/nginx/futsal-error.log

# PHP-FPM logs
sudo tail -f /var/log/php-fpm.log

# Deployment logs
cat /home/deploy/deployment.log
```

---

## 🔐 Environment Variables

### Local (.env)
```bash
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...
DATABASE_URL=pgsql://...
```

### VPS (shared/.env)
```bash
# Copy dari .env.production.example
scp .env.production.example deploy@VPS_IP:/home/deploy/projects/futsal/shared/.env

# Edit di VPS
ssh deploy@VPS_IP
nano /home/deploy/projects/futsal/shared/.env
```

---

## 🛠️ Common Commands

### View all releases
```bash
ssh deploy@YOUR_VPS_IP
ls -la /home/deploy/projects/futsal/releases/
```

### Check current active release
```bash
readlink /home/deploy/projects/futsal/current
```

### Manual trigger deploy
```bash
ssh deploy@YOUR_VPS_IP
/home/deploy/projects/futsal/deploy.sh
```

### View deployment status
```bash
ssh deploy@YOUR_VPS_IP
cat /home/deploy/deployment.log
```

### Verify GitHub Actions
```bash
# Check last workflow runs
https://github.com/muhrobby/booking-futsal/actions

# View specific workflow
https://github.com/muhrobby/booking-futsal/actions/workflows/deploy.yml
```

---

## ⚠️ Troubleshooting

### Tests fail in GitHub Actions
```
→ Fix code locally
→ Push to GitHub
→ Actions will retry
```

### Deployment fails
```bash
# SSH ke VPS
ssh deploy@YOUR_VPS_IP

# Check logs
tail -50 /home/deploy/deployment.log

# Manual deploy untuk debug
./deploy.sh -v  # verbose mode
```

### Database migration error
```bash
# SSH ke VPS sebagai deploy user
ssh deploy@YOUR_VPS_IP
cd /home/deploy/projects/futsal/current

# Rollback manually
php artisan migrate:rollback

# Check migrations
php artisan migrate:status

# Rerun
php artisan migrate
```

### Health check failed
```bash
# SSH ke VPS
ssh deploy@YOUR_VPS_IP
./health-check.sh

# Fix issues based on output
```

### Nginx tidak reload
```bash
# SSH as root
ssh root@YOUR_VPS_IP
sudo nginx -t  # test config
sudo systemctl reload nginx
```

---

## 📚 Files Reference

```
.github/workflows/deploy.yml          → GitHub Actions workflow
deploy.sh                              → Main deployment script
setup-vps.sh                           → VPS one-time setup
health-check.sh                        → Monitoring & health checks
app/Http/Controllers/HealthController  → Health check endpoints
.env.production.example                → Production env template
nginx.conf.example                     → Nginx configuration
CICD_DEPLOYMENT.md                     → Detailed CI/CD guide
DEPLOYMENT_QUICK_START.md              → Quick reference
verify-cicd.sh                         → Verification script
```

---

## ✅ Deployment Checklist

- [ ] SSH key created and shared with GitHub
- [ ] GitHub secrets configured
- [ ] VPS setup script run successfully
- [ ] Deployment directory created
- [ ] Environment variables configured
- [ ] Nginx configured
- [ ] Database migrations tested
- [ ] First manual deployment successful
- [ ] First automatic deployment (push) successful
- [ ] Rollback tested
- [ ] Health checks working
- [ ] Logs monitored

---

## 🎉 You're All Set!

### Next: Make a commit and watch it auto-deploy! 🚀

```bash
# Make a change
echo "# Updated" >> README.md

# Commit & push
git add .
git commit -m "Test deployment"
git push origin main

# Watch at:
https://github.com/muhrobby/booking-futsal/actions
```

---

**NO MORE WEBHOOKS NEEDED!**
GitHub Actions handles everything from test → build → deploy!

