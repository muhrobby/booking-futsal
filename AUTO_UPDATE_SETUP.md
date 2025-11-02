# Auto-Update Setup Guide untuk Booking Futsal

## Deskripsi
Setup ini memungkinkan deployment otomatis setiap kali ada push ke GitHub repository `muhrobby/booking-futsal`.

## File yang Dibuat
1. **auto-update.sh** - Script untuk pull dari GitHub dan rebuild Docker
2. **webhook_server.py** - Server Flask yang mendengarkan GitHub webhook
3. **booking-webhook.service** - Systemd service file

## Langkah Setup

### 1. Install Dependencies (jika belum ada)
```bash
pip3 install flask
```

### 2. Generate Webhook Secret
```bash
# Generate random secret
WEBHOOK_SECRET=$(openssl rand -hex 32)
echo $WEBHOOK_SECRET

# Simpan ke file
echo "$WEBHOOK_SECRET" > /home/robby/stacks/prod/booking-futsal/.webhook_secret
chmod 600 /home/robby/stacks/prod/booking-futsal/.webhook_secret
```

### 3. Setup Systemd Service (Optional tapi Recommended)
```bash
# Copy service file ke systemd
sudo cp /home/robby/stacks/prod/booking-futsal/booking-webhook.service /etc/systemd/system/

# Reload systemd daemon
sudo systemctl daemon-reload

# Enable service untuk auto-start on boot
sudo systemctl enable booking-webhook.service

# Start service
sudo systemctl start booking-webhook.service

# Check status
sudo systemctl status booking-webhook.service
```

### 4. Setup GitHub Webhook
1. Buka repository: https://github.com/muhrobby/booking-futsal
2. Pergi ke **Settings** > **Webhooks** > **Add webhook**
3. Isi form:
   - **Payload URL**: `http://your-server-ip:5000/webhook`
     (Ganti `your-server-ip` dengan IP/domain server Anda)
   - **Content type**: `application/json`
   - **Secret**: Paste webhook secret dari langkah 2
   - **Events**: Pilih "Just the push event"
   - **Active**: Check box ini

### 5. Manual Testing
```bash
# Test webhook server
curl http://localhost:5000/health

# Manual trigger auto-update
bash /home/robby/stacks/prod/booking-futsal/auto-update.sh

# Check logs
tail -f /home/robby/stacks/prod/booking-futsal/auto-update.log
tail -f /home/robby/stacks/prod/booking-futsal/webhook.log
```

## Cara Kerja Workflow

```
1. Developer push code ke GitHub repository
        ↓
2. GitHub mengirim webhook POST ke server Anda (port 5000)
        ↓
3. Webhook server verify signature dan trigger script
        ↓
4. auto-update.sh:
   - Git pull latest changes
   - Docker compose down
   - Docker compose up --build
        ↓
5. Update langsung live! 🚀
```

## Troubleshooting

### Webhook server tidak jalan
```bash
# Check service status
sudo systemctl status booking-webhook.service

# View logs
sudo journalctl -u booking-webhook.service -f

# Manual run untuk debug
python3 /home/robby/stacks/prod/booking-futsal/webhook_server.py
```

### Auto-update gagal
```bash
# Check auto-update logs
tail -f /home/robby/stacks/prod/booking-futsal/auto-update.log

# Manual test
cd /home/robby/stacks/prod/booking-futsal
git fetch origin main
git pull origin main
docker-compose up -d --build
```

### Git permission issues
```bash
# Pastikan git credentials tersimpan
git config --global credential.helper store

# Atau generate SSH key
ssh-keygen -t ed25519 -C "your-email@example.com"
# Copy public key ke GitHub SSH settings
```

## File Penting
- Logs: `/home/robby/stacks/prod/booking-futsal/auto-update.log`
- Webhook logs: `/home/robby/stacks/prod/booking-futsal/webhook.log`
- Webhook secret: `/home/robby/stacks/prod/booking-futsal/.webhook_secret`
- Docker compose: `/home/robby/stacks/prod/booking-futsal/docker-compose.yml`

## Monitoring

Untuk real-time monitoring updates:
```bash
# Monitor auto-update log
watch -n 1 'tail -20 /home/robby/stacks/prod/booking-futsal/auto-update.log'

# Monitor webhook log
watch -n 1 'tail -20 /home/robby/stacks/prod/booking-futsal/webhook.log'

# Monitor Docker containers
docker-compose -f /home/robby/stacks/prod/booking-futsal/docker-compose.yml logs -f
```
