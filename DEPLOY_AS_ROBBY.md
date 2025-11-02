# 🚀 Deployment untuk User Robby (Current Directory)

**Setup**: User `robby` di `/home/robby/stacks/prod/booking-futsal`  
**Method**: Direct deployment dengan Podman  
**Strategy**: Simple git pull + podman rebuild

---

## 📋 Quick Setup (10 Menit)

### Step 1: Add SSH Key ke GitHub

Generate SSH key:
```bash
ssh-keygen -t ed25519 -C "booking-futsal" -f ~/.ssh/booking_futsal
cat ~/.ssh/booking_futsal
```

Tambahkan ke GitHub:
1. Go to: https://github.com/settings/keys
2. Click "New SSH key"
3. Paste public key (`cat ~/.ssh/booking_futsal.pub`)

### Step 2: Configure Git SSH

```bash
# Create config untuk GitHub
cat >> ~/.ssh/config << 'EOF'
Host github.com
    HostName github.com
    User git
    IdentityFile ~/.ssh/booking_futsal
EOF

chmod 600 ~/.ssh/config

# Test
ssh -T git@github.com
```

### Step 3: Add GitHub Secrets

Go to: https://github.com/muhrobby/booking-futsal/settings/secrets/actions

Add these secrets:

```
Name:        VPS_HOST
Value:       YOUR_VPS_IP (atau localhost jika local)

---

Name:        VPS_USER
Value:       robby

---

Name:        VPS_PORT
Value:       22

---

Name:        VPS_PATH
Value:       /home/robby/stacks/prod/booking-futsal

---

Name:        DEPLOY_KEY
Value:       (paste private SSH key dari step 1)
```

### Step 4: Setup Podman (jika belum)

```bash
# Check if already installed
podman --version
podman-compose --version

# If not, install
sudo apt update
sudo apt install -y podman podman-compose
```

### Step 5: Test Deployment Manual

```bash
cd /home/robby/stacks/prod/booking-futsal

# Pull latest
git pull origin main

# Deploy
podman-compose down
podman-compose up -d --build

# Check
podman ps
podman logs -f booking_futsal_app_1
```

---

## 🔄 How It Works Now

```
Developer Push
    ↓
GitHub Actions triggered
    ├─ Run tests
    ├─ Build frontend
    └─ If pass → Deploy
        ↓
    SSH into VPS as robby
        ↓
    cd /home/robby/stacks/prod/booking-futsal
        ↓
    git pull origin main
        ↓
    podman-compose down
    podman-compose up -d --build
        ↓
    Application LIVE ✅
```

---

## 📊 Deployment Script Explanation

GitHub Actions akan menjalankan commands ini:

```bash
# 1. Pull latest code
ssh robby@YOUR_VPS_IP "cd /home/robby/stacks/prod/booking-futsal && git pull origin main"

# 2. Stop containers lama
ssh robby@YOUR_VPS_IP "cd /home/robby/stacks/prod/booking-futsal && podman-compose down"

# 3. Build dan start containers baru
ssh robby@YOUR_VPS_IP "cd /home/robby/stacks/prod/booking-futsal && podman-compose up -d --build"
```

---

## ✅ Verifikasi Setup

```bash
# 1. SSH key working
ssh robby@YOUR_VPS_IP "echo 'SSH connection OK'"

# 2. Git credentials
cd /home/robby/stacks/prod/booking-futsal
git pull origin main

# 3. Podman working
podman ps
podman-compose version

# 4. Application running
curl http://localhost/health
```

---

## 🎯 Testing Deployment

### Manual Test:
```bash
cd /home/robby/stacks/prod/booking-futsal

# Make small change
echo "# Updated $(date)" >> README.md

# Commit & push
git add .
git commit -m "Test deployment"
git push origin main

# Watch at: https://github.com/muhrobby/booking-futsal/actions
```

### Check Logs:
```bash
# Application logs
podman logs -f booking_futsal_app_1

# Compose logs
podman-compose logs -f
```

---

## 🔧 Common Commands

```bash
# View running containers
podman ps

# View all containers
podman ps -a

# View logs
podman logs -f container_name

# Stop all containers
podman-compose down

# Start containers
podman-compose up -d

# Rebuild containers
podman-compose up -d --build

# Execute command in container
podman exec container_name php artisan migrate

# SSH to VPS
ssh robby@YOUR_VPS_IP

# Check current directory
ssh robby@YOUR_VPS_IP "pwd && ls -la /home/robby/stacks/prod/booking-futsal"
```

---

## 🚀 Complete Workflow

### 1. Local Development
```bash
cd /home/robby/stacks/prod/booking-futsal
# Make changes
git add .
git commit -m "Feature: xyz"
git push origin main
```

### 2. GitHub Actions Runs
```
✅ Checkout code
✅ Setup PHP 8.2
✅ Install deps
✅ Run tests
✅ Build assets
✅ If pass → Deploy
```

### 3. Deployment Executes
```bash
ssh robby@VPS "cd /home/robby/stacks/prod/booking-futsal && git pull && podman-compose down && podman-compose up -d --build"
```

### 4. Application Live
```
✅ Containers running
✅ Application accessible
✅ Database migrated
✅ All good! 🎉
```

---

## ⚠️ Troubleshooting

### SSH Key Not Working
```bash
# Debug SSH
ssh -vvv robby@YOUR_VPS_IP

# Check key permissions
ls -la ~/.ssh/booking_futsal
chmod 600 ~/.ssh/booking_futsal

# Add key to SSH agent
ssh-add ~/.ssh/booking_futsal
```

### Git Pull Permission Denied
```bash
# Setup SSH for Git
git config --global core.sshCommand "ssh -i ~/.ssh/booking_futsal"

# Or update SSH config
cat >> ~/.ssh/config << 'EOF'
Host github.com
    HostName github.com
    User git
    IdentityFile ~/.ssh/booking_futsal
EOF
```

### Podman Error
```bash
# Check podman status
systemctl status podman

# Restart podman
sudo systemctl restart podman

# Check containers
podman ps -a

# View error logs
podman logs container_name
```

### GitHub Actions Fails
```bash
# Check workflow logs
https://github.com/muhrobby/booking-futsal/actions

# Click failed run
# View "Deploy via SSH" step
# See error message
```

---

## 🔐 Security Checklist

- [ ] SSH key generated
- [ ] SSH key added to GitHub secrets
- [ ] SSH key permissions 600
- [ ] GitHub SSH config updated
- [ ] VPS_HOST secret configured (your IP)
- [ ] VPS_USER = robby
- [ ] VPS_PORT = 22
- [ ] VPS_PATH = /home/robby/stacks/prod/booking-futsal
- [ ] Git credentials working
- [ ] Podman installed
- [ ] podman-compose installed

---

## 📝 GitHub Secrets Required

```
VPS_HOST:    YOUR_VPS_IP
VPS_USER:    robby
VPS_PORT:    22
VPS_PATH:    /home/robby/stacks/prod/booking-futsal
DEPLOY_KEY:  (SSH private key)
```

---

## 🎯 First Deployment

```bash
# 1. Add secrets to GitHub
# 2. Commit changes
git push origin main

# 3. Watch GitHub Actions
https://github.com/muhrobby/booking-futsal/actions

# 4. If successful, check application
curl http://YOUR_VPS_IP/health

# 5. View logs
ssh robby@YOUR_VPS_IP
podman-compose logs -f
```

---

## ✅ Success Indicators

✅ GitHub Actions workflow passes  
✅ SSH connection successful  
✅ Git pull completes  
✅ podman-compose up succeeds  
✅ Containers are running  
✅ Application responds to requests  
✅ Database accessible  

---

**Status**: ✅ Ready for Deployment

**Next**: Add GitHub secrets and push! 🚀
