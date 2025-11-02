# 🚀 Deployment Simplified untuk User Robby

**User**: robby  
**Path**: /home/robby/stacks/prod/booking-futsal  
**Method**: Git pull + Podman rebuild  
**Time**: ~10 minutes setup

---

## ⚡ 4 Steps Setup

### Step 1: Generate SSH Key
```bash
ssh-keygen -t ed25519 -C "booking-futsal" -f ~/.ssh/booking_futsal
cat ~/.ssh/booking_futsal
```

### Step 2: Add to GitHub Secrets
https://github.com/muhrobby/booking-futsal/settings/secrets/actions

```
VPS_HOST       = Your VPS IP
VPS_USER       = robby
VPS_PORT       = 22
VPS_PATH       = /home/robby/stacks/prod/booking-futsal
DEPLOY_KEY     = (paste private key dari step 1)
```

### Step 3: Test SSH
```bash
ssh robby@YOUR_VPS_IP "echo 'Connected!'"
```

### Step 4: Push ke GitHub
```bash
cd /home/robby/stacks/prod/booking-futsal
git push origin main
# Watch: https://github.com/muhrobby/booking-futsal/actions
```

---

## 🎯 What Happens When You Push

```
git push → GitHub Actions → Run tests → If pass → SSH deploy → podman rebuild → LIVE!
```

---

## 📖 Documentation

**Read this**: `DEPLOY_AS_ROBBY.md` (detailed guide)

---

## ✅ Quick Checklist

- [ ] SSH key generated
- [ ] SSH key added to GitHub secrets  
- [ ] SSH connection tested
- [ ] First push done
- [ ] GitHub Actions passed
- [ ] Application deployed

---

## 🎉 Done!

Your deployment is now automated. Just push and watch! 🚀
