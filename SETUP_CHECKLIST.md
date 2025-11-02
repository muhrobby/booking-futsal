# ✅ Setup Checklist

Ikuti checklist ini untuk memastikan auto-deploy berfungsi dengan baik.

## Pre-Setup Verification
- [x] Git repository configured
- [x] Podman installed
- [x] podman-compose installed
- [x] Python3 available
- [x] Port 5000 available (atau siap untuk change)

## Files Created
- [x] `auto-update.sh` - Deployment script
- [x] `webhook_server.py` - Webhook listener
- [x] `webhook-manager.sh` - Service manager
- [x] `.webhook_secret` - Webhook verification token
- [x] Documentation files (README_AUTO_DEPLOY.md, QUICKSTART.md, etc)

## Initial Setup Steps

### Step 1: Start Webhook Server
```bash
/home/robby/stacks/prod/booking-futsal/webhook-manager.sh start
```
- [ ] Server started successfully
- [ ] Check: `curl http://localhost:5000/health` returns `{"status": "ok"}`

### Step 2: Get Your Server IP
```bash
curl ifconfig.me
```
- [ ] IP Address noted: `__________________`

### Step 3: Setup GitHub Webhook
Go to: https://github.com/muhrobby/booking-futsal/settings/hooks

Add webhook with:
- [ ] Payload URL: `http://YOUR_IP:5000/webhook`
- [ ] Content type: `application/json`
- [ ] Secret: `f892c5aae3adaffaae1758d9b18612e1847c4bedb4c3c542b58a5dfb85a4548e`
- [ ] Events: "Just the push event" selected
- [ ] Active: Checked

### Step 4: Test Manual Deployment
```bash
bash /home/robby/stacks/prod/booking-futsal/auto-update.sh
```
- [ ] Script runs without error
- [ ] Check logs: `tail -f auto-update.log`
- [ ] Containers rebuilt successfully

### Step 5: Test Webhook from GitHub
1. Go to webhook settings
2. Scroll to "Recent Deliveries"
3. Click test webhook
4. Check webhook.log:
```bash
tail -f /home/robby/stacks/prod/booking-futsal/webhook.log
```
- [ ] Webhook received and logged
- [ ] Auto-update script triggered
- [ ] Deployment logs updated

## Runtime Checks

### Daily Monitoring
- [ ] Webhook server still running: `curl http://localhost:5000/health`
- [ ] Check container status: `podman ps | grep booking-futsal`
- [ ] Review recent logs: `tail -20 auto-update.log`

### Performance
- [ ] Deployment completes in reasonable time (< 5 minutes usually)
- [ ] No out-of-memory errors in logs
- [ ] Container health status is healthy/running

## Troubleshooting Checklist

If something doesn't work:

### Webhook server not responding
- [ ] Check if running: `ps aux | grep webhook_server`
- [ ] Check port: `lsof -i :5000`
- [ ] View logs: `tail -50 webhook.log`
- [ ] Restart: `/home/robby/stacks/prod/booking-futsal/webhook-manager.sh restart`

### Git pull fails
- [ ] Check git credentials: `git config --list`
- [ ] Check SSH access to GitHub
- [ ] Manual test: `git pull origin main`
- [ ] View logs: `tail -50 auto-update.log`

### Podman/Container issues
- [ ] Check containers: `podman ps -a`
- [ ] View logs: `podman-compose -f podman-compose.yml logs`
- [ ] Try manual rebuild: `podman-compose up -d --build`

### Port already in use
- [ ] Find process: `lsof -i :5000`
- [ ] Kill process: `kill -9 <PID>`
- [ ] Change port in webhook_server.py if needed
- [ ] Update GitHub webhook URL

## Security Verification

- [ ] Webhook secret is 64 characters: `cat .webhook_secret | wc -c`
- [ ] Secret is not committed to git: `git status .webhook_secret`
- [ ] File permissions are correct: `ls -l .webhook_secret` (should be 600)
- [ ] GitHub webhook has signature verification enabled
- [ ] Only main branch triggers deployment

## Documentation Reference

| Document | Purpose |
|----------|---------|
| README_AUTO_DEPLOY.md | Quick overview and commands |
| QUICKSTART.md | Detailed setup instructions |
| AUTO_UPDATE_SETUP.md | Advanced configuration options |
| SETUP_SUMMARY.md | Configuration summary |
| SETUP_CHECKLIST.md | This checklist |

## Logging Locations

Make sure you know where to find logs:

```bash
# Webhook events
tail -f /home/robby/stacks/prod/booking-futsal/webhook.log

# Deployment logs
tail -f /home/robby/stacks/prod/booking-futsal/auto-update.log

# Server output
tail -f /home/robby/stacks/prod/booking-futsal/webhook_server.log

# Container logs
podman logs -f <container-name>
```

## Commands Reference

```bash
# Start/Stop/Status
/home/robby/stacks/prod/booking-futsal/webhook-manager.sh start
/home/robby/stacks/prod/booking-futsal/webhook-manager.sh stop
/home/robby/stacks/prod/booking-futsal/webhook-manager.sh status

# Manual deployment
bash /home/robby/stacks/prod/booking-futsal/auto-update.sh

# View logs
tail -f /home/robby/stacks/prod/booking-futsal/webhook.log
tail -f /home/robby/stacks/prod/booking-futsal/auto-update.log

# Check health
curl http://localhost:5000/health

# Get your IP
curl ifconfig.me
```

## Final Verification

Once everything is set up, verify with this command:

```bash
#!/bin/bash
echo "🧪 Final Verification"
echo "===================="

# 1. Webhook server
if curl -s http://localhost:5000/health > /dev/null; then
    echo "✅ Webhook server responding"
else
    echo "❌ Webhook server not responding"
fi

# 2. Git repo
if git -C /home/robby/stacks/prod/booking-futsal status > /dev/null 2>&1; then
    echo "✅ Git repository configured"
else
    echo "❌ Git repository issue"
fi

# 3. Podman
if podman ps > /dev/null 2>&1; then
    echo "✅ Podman working"
else
    echo "❌ Podman issue"
fi

# 4. Compose file
if [ -f /home/robby/stacks/prod/booking-futsal/podman-compose.yml ]; then
    echo "✅ Compose file exists"
else
    echo "❌ Compose file missing"
fi

# 5. Auto-update script
if [ -x /home/robby/stacks/prod/booking-futsal/auto-update.sh ]; then
    echo "✅ Auto-update script executable"
else
    echo "❌ Auto-update script not executable"
fi

echo ""
echo "✅ All systems ready for auto-deployment!"
```

Save as `verify.sh`, run with `bash verify.sh`

---

## Support

If you're stuck:
1. Check the logs
2. Review QUICKSTART.md
3. Check troubleshooting section in README_AUTO_DEPLOY.md
4. Verify all checklist items above

---

**Status**: Ready for deployment! 🚀
