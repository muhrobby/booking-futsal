# 🚀 Auto-Deploy Setup untuk Booking Futsal

**Status**: ✅ Siap Digunakan!

---

## 📖 Baca Ini Dulu!

Untuk setup lengkap dan detail: **[BUKA QUICKSTART.md](QUICKSTART.md)**

---

## ⚡ Quick Start (3 Perintah)

### 1. Start webhook server
```bash
/home/robby/stacks/prod/booking-futsal/webhook-manager.sh start
```

### 2. Setup GitHub webhook
https://github.com/muhrobby/booking-futsal/settings/hooks
- Payload URL: `http://YOUR_IP:5000/webhook`
- Secret: `f892c5aae3adaffaae1758d9b18612e1847c4bedb4c3c542b58c7dfb85a4548e`

### 3. Test it!
```bash
curl http://localhost:5000/health
```

---

## 🎯 Bagaimana Cara Kerjanya?

```
1. Developer push code ke GitHub
   git push origin main

2. GitHub mengirim webhook ke server Anda (port 5000)

3. Webhook server menerima dan verify signature

4. Auto-update script berjalan:
   ✅ git fetch & pull latest code
   ✅ podman-compose down (stop container lama)
   ✅ podman-compose up -d --build (start container baru)

5. UPDATE LIVE! 🎉
```

---

## 🛠️ Available Commands

### Webhook Manager
```bash
# Start server
/home/robby/stacks/prod/booking-futsal/webhook-manager.sh start

# Stop server
/home/robby/stacks/prod/booking-futsal/webhook-manager.sh stop

# Restart server
/home/robby/stacks/prod/booking-futsal/webhook-manager.sh restart

# Check status
/home/robby/stacks/prod/booking-futsal/webhook-manager.sh status

# View logs
/home/robby/stacks/prod/booking-futsal/webhook-manager.sh logs
```

### Manual Deployment
```bash
# Manually trigger deployment
bash /home/robby/stacks/prod/booking-futsal/auto-update.sh

# View deployment logs
tail -f /home/robby/stacks/prod/booking-futsal/auto-update.log
```

### Monitoring
```bash
# Webhook events
tail -f /home/robby/stacks/prod/booking-futsal/webhook.log

# Deployment progress
tail -f /home/robby/stacks/prod/booking-futsal/auto-update.log

# Container status
podman-compose -f /home/robby/stacks/prod/booking-futsal/podman-compose.yml logs -f
```

---

## 📋 Files Created

| File | Purpose |
|------|---------|
| `webhook_server.py` | Python webhook listener (port 5000) |
| `auto-update.sh` | Git pull + Podman rebuild script |
| `webhook-manager.sh` | Service manager (start/stop/status) |
| `.webhook_secret` | GitHub webhook verification token |
| `QUICKSTART.md` | **📖 Detailed setup guide** |
| `AUTO_UPDATE_SETUP.md` | Advanced configuration |
| `SETUP_SUMMARY.md` | Configuration summary |

---

## ✅ Verification

Pastikan semua ini berjalan:

```bash
# 1. Webhook secret exists
cat /home/robby/stacks/prod/booking-futsal/.webhook_secret

# 2. Scripts executable
ls -l /home/robby/stacks/prod/booking-futsal/*.sh

# 3. Git configured
cd /home/robby/stacks/prod/booking-futsal && git remote -v

# 4. Podman working
podman ps

# 5. Webhook server responds
curl http://localhost:5000/health
```

---

## 🐛 Troubleshooting

### Port 5000 already in use
```bash
# Find and kill
lsof -i :5000
kill -9 <PID>
```

### Webhook server won't start
```bash
# Check manually
python3 /home/robby/stacks/prod/booking-futsal/webhook_server.py

# Check logs
tail -50 /home/robby/stacks/prod/booking-futsal/webhook_server.log
```

### Deployment fails
```bash
# View full logs
tail -50 /home/robby/stacks/prod/booking-futsal/auto-update.log

# Manual test
cd /home/robby/stacks/prod/booking-futsal
git fetch origin main
git pull origin main
podman-compose up -d --build
```

---

## 🔐 Security

- ✅ Webhook secret auto-generated and saved
- ✅ GitHub verifies all webhook signatures
- ✅ Only `main` branch triggers deployment
- ✅ All webhooks are logged

---

## 📚 Next Steps

1. **[👉 Read QUICKSTART.md for detailed instructions](QUICKSTART.md)**
2. Get your IP: `curl ifconfig.me`
3. Start webhook server: `webhook-manager.sh start`
4. Setup GitHub webhook (copy your webhook secret above)
5. Make a commit and watch auto-deploy happen!

---

## 💡 Pro Tips

- **Keep webhook running**: Add to crontab or systemd
- **Multiple branches**: Edit webhook_server.py for other branches
- **Notifications**: Add Slack/Discord webhook in auto-update.sh
- **Rollback**: Keep git history, can revert if needed

---

**Questions?** Check the documentation files or see troubleshooting section above.

**Everything working?** Congratulations! 🎉 You now have automatic deployments!
