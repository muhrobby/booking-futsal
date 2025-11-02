# 🔐 GitHub Secrets Setup untuk Deployment

**Error**: `Permission denied (publickey,password)`  
**Cause**: DEPLOY_KEY secret tidak ada atau tidak valid

---

## ✅ Step-by-Step Setup

### Step 1: Generate SSH Key Pair (Di Laptop/Lokal)

```bash
# Generate key (JANGAN di server, di lokal!)
ssh-keygen -t ed25519 -C "booking-futsal-deploy" -f ~/.ssh/booking_futsal_deploy -N ""

# Output akan create:
# ~/.ssh/booking_futsal_deploy (private key)
# ~/.ssh/booking_futsal_deploy.pub (public key)
```

### Step 2: Add Public Key ke VPS

```bash
# SSH ke VPS sebagai user robby
ssh robby@YOUR_VPS_IP

# Create .ssh directory
mkdir -p ~/.ssh

# Add public key (copy-paste content dari ~/.ssh/booking_futsal_deploy.pub)
nano ~/.ssh/authorized_keys
# Paste key, save, exit

# Set permissions
chmod 700 ~/.ssh
chmod 600 ~/.ssh/authorized_keys

# Test SSH (exit dan test)
exit
ssh robby@YOUR_VPS_IP "echo OK"
```

### Step 3: Add 5 Secrets ke GitHub

Go to: https://github.com/muhrobby/booking-futsal/settings/secrets/actions

Click "New repository secret" untuk masing-masing:

**Secret 1: VPS_HOST**
```
Name:  VPS_HOST
Value: YOUR_VPS_IP (contoh: 192.168.1.100)
```

**Secret 2: VPS_USER**
```
Name:  VPS_USER
Value: robby
```

**Secret 3: VPS_PORT**
```
Name:  VPS_PORT
Value: 22
```

**Secret 4: VPS_PATH**
```
Name:  VPS_PATH
Value: /home/robby/stacks/prod/booking-futsal
```

**Secret 5: DEPLOY_KEY (PALING PENTING!)**
```
Name:  DEPLOY_KEY
Value: (COPY-PASTE SELURUH PRIVATE KEY dari ~/.ssh/booking_futsal_deploy)
```

⚠️ **PRIVATE KEY harus lengkap** - mulai dari `-----BEGIN` sampai `-----END`

---

## 📋 Verify Setup

### Test SSH Locally:

```bash
# Test 1: Basic SSH
ssh -i ~/.ssh/booking_futsal_deploy robby@YOUR_VPS_IP "echo 'Connected!'"

# Test 2: Git access
ssh -i ~/.ssh/booking_futsal_deploy robby@YOUR_VPS_IP "cd /home/robby/stacks/prod/booking-futsal && git status"

# Test 3: Podman
ssh -i ~/.ssh/booking_futsal_deploy robby@YOUR_VPS_IP "podman ps"
```

### Check GitHub Secrets:

```
https://github.com/muhrobby/booking-futsal/settings/secrets/actions

Pastikan semua ini ada (with **):
✅ VPS_HOST
✅ VPS_USER
✅ VPS_PORT
✅ VPS_PATH
✅ DEPLOY_KEY
```

---

## 🐛 Troubleshooting

### "Permission denied"
```
Penyebab: DEPLOY_KEY tidak valid atau public key belum di authorized_keys

Fix:
1. Cek public key di VPS:
   cat ~/.ssh/authorized_keys | grep "booking-futsal"
   
2. Cek permissions:
   ls -la ~/.ssh/
   (harus 700 untuk folder, 600 untuk file)

3. Regenerate jika perlu:
   ssh-keygen -t ed25519 -C "booking-futsal-deploy" -f ~/.ssh/booking_futsal_deploy
```

### "Host key verification failed"
```
Penyebab: known_hosts belum update

Fix:
1. Workflow sudah ada ssh-keyscan
2. Pastikan VPS_HOST benar
3. Check workflow logs untuk detail
```

### "Could not read key"
```
Penyebab: DEPLOY_KEY tidak proper format

Fix:
1. Display private key:
   cat ~/.ssh/booking_futsal_deploy
   
2. Copy-paste SELURUH content (including -----BEGIN/END)

3. Update DEPLOY_KEY secret di GitHub

4. Verify ada newline di akhir
```

---

## ✅ Checklist

- [ ] SSH key generated (local)
- [ ] Public key added to VPS
- [ ] Permissions correct
- [ ] SSH test works
- [ ] All 5 secrets added to GitHub
- [ ] DEPLOY_KEY valid

---

## 🚀 After Setup

1. Commit & push code
2. Watch: https://github.com/muhrobby/booking-futsal/actions
3. Should see workflow success!

---

**Status**: Ready for deployment! 🚀
