✅ Summary - Podman Deployment Ready!

Saya telah memperbaiki dan membuat deployment setup yang sesuai untuk
Podman!

📁 Files Created/Updated

1. Dockerfile (Updated) ✅

    ✅ Multi-stage build:

    - Stage 1: Composer dependencies
    - Stage 2: Frontend build (Vite)
    - Stage 3: PHP 8.2-FPM runtime

    ✅ Optimized for Laravel 12
    ✅ OPcache configured
    ✅ Proper permissions (www-data)
    ✅ Health check included

2. podman-compose.yml (Created) ✅

    ✅ Native Podman format (bukan docker-compose)
    ✅ Resource limits dengan cpus & mem_limit:

    - app: 1.0 CPU / 512MB
    - db: 0.5 CPU / 512MB
    - nginx: 0.5 CPU / 256MB

    ✅ Traefik labels untuk SSL auto
    ✅ Environment variables dari .env
    ✅ Volumes untuk storage & cache
    ✅ Networks: futsal-network + traefik-network

3. Nginx Config (Already created)

    ✅ nginx.conf - Main config
    ✅ conf.d/default.conf - Site config
    ✅ PHP-FPM upstream
    ✅ Static asset caching

---

---

🔑 Key Differences - Podman vs Docker

┌─────────────────┬─────────────────────┬───────────────────────────────┐
│ Feature │ Docker Compose │ Podman Compose │
├─────────────────┼─────────────────────┼───────────────────────────────┤
│ File Name │ docker-compose.yml │ podman-compose.yml │
├─────────────────┼─────────────────────┼───────────────────────────────┤
│ Resource Limits │ deploy.resources │ cpus & mem_limit (top level) │
├─────────────────┼─────────────────────┼───────────────────────────────┤
│ Image Registry │ docker.io (default) │ docker.io/library/ (explicit) │
├─────────────────┼─────────────────────┼───────────────────────────────┤
│ Rootless │ No │ Yes (default) │
├─────────────────┼─────────────────────┼───────────────────────────────┤
│ Daemon │ Required │ Daemonless │
└─────────────────┴─────────────────────┴───────────────────────────────┘

---

---

🚀 Quick Start Deployment

     # 1. Install Podman
     sudo dnf install -y podman podman-compose podman-plugins

     # 2. Setup rootless
     systemctl --user enable --now podman.socket
     sudo loginctl enable-linger $USER

     # 3. Create network
     podman network create traefik-network

     # 4. Clone project
     cd ~/apps
     git clone <repo> futsal-neo-s
     cd futsal-neo-s

     # 5. Configure
     cp .env.example .env
     nano .env  # Update APP_URL, passwords

     # 6. Update podman-compose.yml
     nano podman-compose.yml  # Update domain

     # 7. Build & Deploy
     podman-compose build --no-cache
     podman-compose up -d

     # 8. Laravel setup
     podman exec -it futsal-neo-s-app bash
     php artisan key:generate
     php artisan migrate --force
     php artisan db:seed --force
     php artisan config:cache
     exit

     # 9. Verify
     podman ps
     podman stats
     curl -I https://futsal.yourdomain.com

---

---

🔧 Resource Limits Format

     # Podman-compose format (CORRECT)
     services:
       app:
         cpus: 1.0              # Top level
         mem_limit: 512m        # Top level
         mem_reservation: 256m  # Top level

     # Docker-compose format (WRONG for Podman)
     services:
       app:
         deploy:              # ❌ Don't use with Podman
           resources:
             limits:
               cpus: '1.0'

---

---

📊 Resource Allocation

VPS 2GB RAM:

     app:     cpus: 1.0,  mem: 512MB
     db:      cpus: 0.5,  mem: 512MB
     nginx:   cpus: 0.5,  mem: 256MB
     Total:   2.0 CPUs,  1.25GB RAM

VPS 4GB RAM (Recommended):

     app:     cpus: 2.0,  mem: 1GB
     db:      cpus: 1.0,  mem: 1GB
     nginx:   cpus: 1.0,  mem: 512MB
     Total:   4.0 CPUs,  2.5GB RAM

---

---

🎯 What to Update

podman-compose.yml:

     - Line 70: Domain → futsal.yourdomain.com
     - Resource limits (cpus, mem_limit)

.env:

     - APP_URL → your domain
     - DB_PASSWORD → secure password
     - DB_ROOT_PASSWORD → secure password

---

---

✅ Ready Files

     ✅ Dockerfile (PHP 8.2-FPM)
     ✅ podman-compose.yml (Podman format)
     ✅ docker/nginx/nginx.conf
     ✅ docker/nginx/conf.d/default.conf
     ✅ docker-compose.yml.backup (old version)

---

---

Deployment siap dengan Podman! 🎉

Podman-compose menggunakan format yang sedikit berbeda dari docker-compose,
terutama untuk resource limits yang langsung di top-level service, bukan di
dalam deploy.resources. File sudah disesuaikan!
