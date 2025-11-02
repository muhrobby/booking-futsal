# 📋 Setup Summary - Booking Futsal Auto-Deploy

**Status**: ✅ **READY TO USE**

## Apa yang sudah disiapkan?

### 1. Files yang sudah dibuat:
```
✅ auto-update.sh           → Script untuk pull & rebuild dengan Podman
✅ webhook_server.py        → Python webhook listener (port 5000)
✅ webhook-manager.sh       → Manager untuk start/stop webhook server
✅ .webhook_secret          → Secret token untuk GitHub webhook verification
✅ QUICKSTART.md            → Setup guide cepat (BACA INI DULU!)
```

### 2. Bagaimana cara kerjanya?
```
Developer push kode
       ↓
GitHub kirim webhook ke server:5000/webhook
       ↓
Webhook server verify signature
       ↓
Trigger auto-update.sh
       ↓
Script pull latest code
       ↓
Rebuild dengan podman-compose
       ↓
Container restart dengan code terbaru
       ↓
UPDATE LIVE! 🚀
```

## ⚡ Quick Start (5 Menit)

### Langkah 1: Get Your Server IP
```bash
curl ifconfig.me
# Output: YOUR_IP_ADDRESS
```

### Langkah 2: Start Webhook Server
```bash
# Background mode (recommended)
nohup python3 /home/robby/stacks/prod/booking-futsal/webhook_server.py > /home/robby/stacks/prod/booking-futsal/webhook_server.log 2>&1 &

# Atau gunakan webhook-manager
/home/robby/stacks/prod/booking-futsal/webhook-manager.sh start
```

### Langkah 3: Setup GitHub Webhook
1. Buka: https://github.com/muhrobby/booking-futsal/settings/hooks
2. Click **"Add webhook"**
3. Isi form:
   - **Payload URL**: `http://YOUR_IP_ADDRESS:5000/webhook`
   - **Content type**: `application/json`
   - **Secret**: (lihat di bawah)
   - **Events**: Just the push event
4. Click **"Add webhook"**

### Webhook Secret
```
f892c5aae3adaffaae1758d9b18612e1847c4bedb4c3c542b58c7dfb85a4548e
```

### Langkah 4: Test It!
```bash
# Cek webhook server
curl http://localhost:5000/health

# Manual test deployment
bash /home/robby/stacks/prod/booking-futsal/auto-update.sh

# View logs
tail -f /home/robby/stacks/prod/booking-futsal/auto-update.log
```

## 🎯 Commands yang Perlu Diingat

### Webhook Server Management
```bash
# Start
/home/robby/stacks/prod/booking-futsal/webhook-manager.sh start

# Stop
/home/robby/stacks/prod/booking-futsal/webhook-manager.sh stop

# Status
/home/robby/stacks/prod/booking-futsal/webhook-manager.sh status

# View logs
/home/robby/stacks/prod/booking-futsal/webhook-manager.sh logs
```

### Manual Deployment
```bash
# Test auto-update
bash /home/robby/stacks/prod/booking-futsal/auto-update.sh

# View deployment logs
tail -f /home/robby/stacks/prod/booking-futsal/auto-update.log
```

### Monitoring
```bash
# Real-time logs
tail -f /home/robby/stacks/prod/booking-futsal/webhook.log
tail -f /home/robby/stacks/prod/booking-futsal/auto-update.log

# Container status
podman ps
podman-compose -f /home/robby/stacks/prod/booking-futsal/docker-compose.yml logs -f
```

## 📊 Logs Location
```
/home/robby/stacks/prod/booking-futsal/
├── auto-update.log       → Deployment logs
├── webhook.log           → Webhook events
└── webhook_server.log    → Server output
```

## 🔐 Security Notes
- Webhook secret sudah di-generate dan di-save
- GitHub webhook akan verify signature sebelum trigger
- Only push events to `main` branch yang akan trigger deployment
- PID file di `.webhook_server.pid` untuk tracking

## 🐛 Troubleshooting

### Server tidak respond
```bash
# Check if running
curl http://localhost:5000/health

# Check logs
tail -50 /home/robby/stacks/prod/booking-futsal/webhook.log

# Restart
/home/robby/stacks/prod/booking-futsal/webhook-manager.sh restart
```

### Deployment gagal
```bash
# Check deployment logs
tail -50 /home/robby/stacks/prod/booking-futsal/auto-update.log

# Manual test
cd /home/robby/stacks/prod/booking-futsal
git fetch origin main
git pull origin main
podman-compose up -d --build
```

### Port 5000 sudah terpakai
```bash
# Find process using port
lsof -i :5000

# Kill if needed
kill -9 <PID>

# Or change port in webhook_server.py and restart
```

## 📚 Documentation Files
- **QUICKSTART.md** ← START HERE! (Detailed setup guide)
- **AUTO_UPDATE_SETUP.md** ← More advanced options
- This file (SETUP_SUMMARY.md) ← You are here

## ✅ Verification Checklist

- [ ] Webhook server berjalan: `curl http://localhost:5000/health`
- [ ] GitHub webhook setup dengan correct IP dan secret
- [ ] Manual test: `bash auto-update.sh` berjalan tanpa error
- [ ] Logs terupdate: `tail -f auto-update.log`
- [ ] Push ke GitHub dan lihat auto-update terjadi

## 🚀 Next Steps

1. **Read QUICKSTART.md** - Follow setup instructions
2. **Get your server IP** - `curl ifconfig.me`
3. **Start webhook server** - `webhook-manager.sh start`
4. **Setup GitHub webhook** - https://github.com/muhrobby/booking-futsal/settings/hooks
5. **Test everything** - `webhook-manager.sh status` dan manual test
6. **Make a commit** - Push ke GitHub dan watch auto-update happen!

---

**Question?** Check QUICKSTART.md for more details!

**Everything working?** Congrats! 🎉 You now have automatic deployments!
