# 🚀 QUICKSTART - Auto-Deploy Booking Futsal dengan Podman

## Deskripsi Singkat
Setup ini memungkinkan **automatic deployment** setiap kali ada push ke GitHub. Cukup push code, dan aplikasi langsung ter-deploy!

```
Push ke GitHub → Webhook trigger → Auto-pull & rebuild → Live! 🎉
```

## Prerequisites
- ✅ Podman + podman-compose (sudah terinstall)
- ✅ Python3 (sudah ada)
- ✅ Git (sudah configured)

## Setup (3 Langkah Mudah)

### Step 1: Catat Webhook Secret
```bash
cat /home/robby/stacks/prod/booking-futsal/.webhook_secret
# Copy output ini, akan digunakan di step 3
```

Webhook Secret: `f892c5aae3adaffaae1758d9b18612e1847c4bedb4c3c542b58c7dfb85a4548e`

### Step 2: Dapatkan IP/Domain Server
```bash
# Untuk public IP (jika server public)
curl ifconfig.me

# Atau gunakan hostname
hostname -I
```
Contoh: `192.168.1.100` atau `example.com`

### Step 3: Setup GitHub Webhook
1. Buka: https://github.com/muhrobby/booking-futsal/settings/hooks
2. Klik **"Add webhook"**
3. Isi form:
   ```
   Payload URL: http://YOUR_IP_HERE:5000/webhook
   Content type: application/json
   Secret: [paste webhook secret dari Step 1]
   Events: Just the push event ✓
   Active: ✓ (centang)
   ```
4. Klik **"Add webhook"**

### Step 4: Start Webhook Server
```bash
# Background mode (recommended untuk production)
nohup python3 /home/robby/stacks/prod/booking-futsal/webhook_server.py > /home/robby/stacks/prod/booking-futsal/webhook_server.log 2>&1 &

# Atau untuk testing (foreground)
python3 /home/robby/stacks/prod/booking-futsal/webhook_server.py
```

## ✅ Verifikasi Setup

### Cek Webhook Server Berjalan
```bash
curl http://localhost:5000/health
# Expected: {"status": "ok"}
```

### Manual Test Deployment
```bash
# Test auto-update script
cd /home/robby/stacks/prod/booking-futsal
bash auto-update.sh

# Monitor logs real-time
tail -f auto-update.log
```

### Test Webhook dari GitHub
1. Buka https://github.com/muhrobby/booking-futsal/settings/hooks
2. Klik webhook yang sudah dibuat
3. Scroll ke bawah, klik **"Test"**
4. Lihat di log:
   ```bash
   tail -f /home/robby/stacks/prod/booking-futsal/webhook.log
   ```

## 📊 Monitoring

### Real-time Logs
```bash
# Auto-update logs
tail -f /home/robby/stacks/prod/booking-futsal/auto-update.log

# Webhook logs
tail -f /home/robby/stacks/prod/booking-futsal/webhook.log

# Container logs
podman-compose -f /home/robby/stacks/prod/booking-futsal/podman-compose.yml logs -f
```

### Check Webhook Server Status
```bash
ps aux | grep webhook_server | grep -v grep

# Kill if needed
pkill -f webhook_server
```

## 🔄 How It Works

1. **Developer push code ke GitHub**
   ```bash
   git push origin main
   ```

2. **GitHub kirim webhook ke server Anda** (port 5000)

3. **Webhook server verifikasi signature** & trigger auto-update script

4. **auto-update.sh melakukan:**
   - `git fetch` → check ada update
   - `git pull` → download latest code
   - `podman-compose down` → stop container lama
   - `podman-compose up -d --build` → build image baru & start

5. **Update langsung LIVE!** 🚀

## 🐛 Troubleshooting

### Webhook server tidak jalan
```bash
# Check if port 5000 is free
lsof -i :5000

# Start manually untuk debug
python3 /home/robby/stacks/prod/booking-futsal/webhook_server.py
```

### Git permission denied
```bash
# Setup git credentials
git config --global credential.helper store

# Atau gunakan SSH key
ssh-keygen -t ed25519 -C "your-email@example.com"
# Tambahkan public key ke GitHub SSH settings
```

### Podman permission issues
```bash
# Enable rootless podman jika needed
loginctl enable-linger $(whoami)

# Atau run as specific user
sudo -u robby bash auto-update.sh
```

### Deployment gagal
```bash
# Check logs detail
tail -50 /home/robby/stacks/prod/booking-futsal/auto-update.log

# Manual test
cd /home/robby/stacks/prod/booking-futsal
git fetch origin main
git pull origin main
podman-compose up -d --build
```

## 📁 File Penting
```
/home/robby/stacks/prod/booking-futsal/
├── webhook_server.py          # Webhook listener
├── auto-update.sh             # Deploy script
├── .webhook_secret            # GitHub webhook secret
├── auto-update.log            # Deployment logs
├── webhook.log                # Webhook logs
└── podman-compose.yml         # Container config
```

## 🎯 Quick Commands

```bash
# Start webhook server background
nohup python3 /home/robby/stacks/prod/booking-futsal/webhook_server.py > /home/robby/stacks/prod/booking-futsal/webhook_server.log 2>&1 &

# Check webhook status
curl http://localhost:5000/health

# Manual trigger update
bash /home/robby/stacks/prod/booking-futsal/auto-update.sh

# View deployment logs
tail -f /home/robby/stacks/prod/booking-futsal/auto-update.log

# Stop webhook server
pkill -f webhook_server

# View Podman containers
podman ps -a

# View container logs
podman logs -f <container-name>
```

## 💡 Tips

- Webhook secret sudah di-generate dan di-save di `.webhook_secret`
- Jangan share webhook secret dengan orang lain
- Untuk production, gunakan systemd service (see `booking-webhook.service`)
- Untuk multiple branches, edit webhook untuk filter branch tertentu

## ❓ FAQ

**Q: Port 5000 sudah dipakai, bisa ganti?**
A: Edit `webhook_server.py` line terakhir, ubah `PORT = 5000` ke port lain

**Q: Bisa auto-deploy multiple branches?**
A: Edit `webhook_server.py`, ubah `if data.get('ref') == 'refs/heads/main':` sesuai branch lain

**Q: Deployment terlalu lama?**
A: Check `podman images` dan `podman ps`, mungkin ada image/container lama yang perlu di-clean

---

**Need help?** Check logs atau baca `AUTO_UPDATE_SETUP.md` untuk detail lebih lanjut.
