# GitHub Actions CI/CD Setup untuk Booking Futsal

## Overview

Setup ini menggunakan **GitHub Actions** untuk:
- ✅ Automated testing (PHP/Laravel tests)
- ✅ Build Docker images
- ✅ Push ke container registry
- ✅ Auto-deploy ke server production

## Workflow Files

```
.github/workflows/
├── ci.yml           → Run tests on every push/PR
├── docker-build.yml → Build & push Docker image
└── deploy.yml       → Trigger deployment on main branch
```

## 🔧 Setup Instructions

### Step 1: Configure GitHub Secrets

Go to: https://github.com/muhrobby/booking-futsal/settings/secrets/actions

Add these secrets:

#### 1. DEPLOY_WEBHOOK_URL (untuk trigger deployment)
```
Value: http://YOUR_IP:5000/webhook
```

#### 2. SLACK_WEBHOOK_URL (opsional, untuk notifikasi)
```
Value: https://hooks.slack.com/services/YOUR/SLACK/WEBHOOK
```

Cara generate Slack webhook:
1. Buka https://api.slack.com/apps
2. Create New App → From scratch
3. Name: "Booking Futsal Deploy"
4. Pilih workspace Anda
5. Pergi ke "Incoming Webhooks"
6. Aktifkan Incoming Webhooks
7. Add New Webhook to Workspace
8. Pilih channel untuk notifikasi
9. Copy Webhook URL

#### 3. GITHUB_TOKEN (auto-generated, tidak perlu)
GitHub sudah sediakan ini secara otomatis

### Step 2: Verify Container Registry Access

GitHub Actions akan push image ke `ghcr.io` (GitHub Container Registry):

1. Go to: https://github.com/settings/tokens
2. Generate new token (classic)
3. Scopes: `write:packages`, `read:packages`
4. Copy token

Atau GitHub Actions bisa langsung pakai `secrets.GITHUB_TOKEN` (sudah built-in)

### Step 3: Create .env.example (untuk testing)

Pastikan file `.env.example` exist untuk CI testing:

```bash
cat > /home/robby/stacks/prod/booking-futsal/.env.example << 'EOF'
APP_NAME="Booking Futsal"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=futsal_booking
DB_USERNAME=futsal_user
DB_PASSWORD=password
EOF
```

## 📊 Workflow Explanations

### 1. CI Workflow (ci.yml)
Triggers: Push to main/develop, PR to main/develop

**Steps:**
- Checkout code
- Setup PHP 8.2
- Install Composer dependencies
- Run database migrations
- Run Laravel tests
- Check code style (pint)
- Run static analysis (phpstan)

### 2. Docker Build (docker-build.yml)
Triggers: Push to main/develop/tags, PR to main

**Steps:**
- Checkout code
- Setup Docker Buildx
- Login to GitHub Container Registry
- Build Docker image
- Push image dengan tags:
  - `ghcr.io/muhrobby/booking-futsal:main` (latest)
  - `ghcr.io/muhrobby/booking-futsal:sha-xxxxx` (commit hash)
  - Version tags jika ada

### 3. Deploy Workflow (deploy.yml)
Triggers: Push to main only

**Steps:**
- Build & push Docker image
- Call webhook untuk trigger deployment di server
- Send Slack notification (success/failure)

## 🚀 How It All Works Together

```
Developer push code
    ↓
GitHub Actions triggered
    ↓
CI Workflow:
  ├─ Run tests
  ├─ Check code style
  └─ Run static analysis
    ↓
Docker Build Workflow:
  ├─ Build image
  └─ Push ke ghcr.io
    ↓
Deploy Workflow (if on main):
  ├─ Call webhook http://YOUR_IP:5000/webhook
  ├─ Your server auto-update.sh runs
  └─ Containers rebuild & deploy
    ↓
Slack notification sent
    ↓
✅ LIVE UPDATE!
```

## 📝 Workflow Status

View workflow runs at:
```
https://github.com/muhrobby/booking-futsal/actions
```

## 🔍 Monitoring Workflows

### View workflow status
- https://github.com/muhrobby/booking-futsal/actions

### View specific workflow
- https://github.com/muhrobby/booking-futsal/actions/workflows/ci.yml
- https://github.com/muhrobby/booking-futsal/actions/workflows/docker-build.yml
- https://github.com/muhrobby/booking-futsal/actions/workflows/deploy.yml

### Check logs
Click on the workflow run → Click job → View logs

## ✅ Verification Checklist

Before workflows work:

- [ ] All secrets configured in GitHub Settings
- [ ] `.env.example` exists in repo root
- [ ] `Dockerfile` exists (for docker-build.yml)
- [ ] `podman-compose.yml` exists (for deployment)
- [ ] Database exists (for migrations)
- [ ] composer.lock exists (for testing)

## 🐛 Troubleshooting

### Tests fail in CI
1. Check logs at https://github.com/muhrobby/booking-futsal/actions
2. View "Run tests" step
3. Common issues:
   - Missing `.env.example`
   - Database connection issues
   - Missing dependencies in composer.json

### Docker build fails
1. Check "Build and push Docker image" step
2. Ensure Dockerfile exists and is valid
3. Check Docker registry permissions

### Deployment webhook not triggered
1. Verify DEPLOY_WEBHOOK_URL secret is correct
2. Check webhook server running: `curl http://YOUR_IP:5000/health`
3. Check deploy.yml file is in `.github/workflows/`

### Slack notification not sending
1. Verify SLACK_WEBHOOK_URL secret is correct
2. Test webhook manually:
   ```bash
   curl -X POST $SLACK_WEBHOOK_URL \
     -H 'Content-type: application/json' \
     -d '{"text":"Test"}'
   ```

## 🔐 Security Best Practices

✓ Use GitHub Secrets untuk semua sensitive data
✓ Jangan commit secrets ke repository
✓ Use GITHUB_TOKEN built-in GitHub Actions
✓ Limit secret access ke specific workflows
✓ Review action permissions regularly

## 📚 References

- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [Docker Build Action](https://github.com/docker/build-push-action)
- [PHP Setup Action](https://github.com/shivammathur/setup-php-action)

## 🎯 Common Customizations

### Change PHP version
Edit `ci.yml`:
```yaml
php-version: '8.3'  # Change this
```

### Add more tests
Edit `ci.yml`:
```yaml
- name: Run custom tests
  run: php artisan test --filter=MyTest
```

### Deploy ke multiple branches
Edit `deploy.yml`:
```yaml
on:
  push:
    branches:
      - main
      - staging  # Add this
```

### Add environment variables
Edit respective workflow:
```yaml
env:
  MY_VAR: value
```

---

**Setup complete!** Workflows akan auto-run setiap kali push ke GitHub.
