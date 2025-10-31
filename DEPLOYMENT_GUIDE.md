# Booking Futsal - Deployment Guide

## Prerequisites
- Podman (Docker alternative)
- Git
- Domain dengan DNS pointing ke server
- SSL Certificate (via Traefik + Cloudflare)

## System Architecture
```
Browser (HTTPS)
    ↓
Traefik (Reverse Proxy + SSL)
    ↓
Nginx (Web Server) - port 80
    ↓
PHP-FPM (Application) - port 9000
    ↓
MySQL (Database) - port 3306
```

## Step-by-Step Deployment

### 1. Clone Repository
```bash
git clone https://github.com/muhrobby/booking-futsal.git
cd booking-futsal
```

### 2. Configure Environment
```bash
# Copy env.example to .env
cp .env.example .env  # atau gunakan .env existing

# Edit .env untuk production
nano .env
# Pastikan:
# APP_ENV=production
# APP_DEBUG=false
# APP_URL=https://kelompok1-de.humahub.my.id
# DB_PASSWORD=Qwerty123.
# DB_ROOT_PASSWORD=Qwerty123.
```

### 3. Build Docker Image
```bash
podman build -t booking-futsal_app:latest -f Dockerfile .
```

### 4. Ensure Traefik Network Exists
```bash
podman network create traefik-network
# Atau verify jika sudah ada:
podman network ls | grep traefik
```

### 5. Start Services with Podman Compose
```bash
podman-compose up -d
```

### 6. Verify All Services Running
```bash
podman ps
# Output harus menampilkan:
# - futsal-neo-s-db (MySQL)
# - futsal-neo-s-app (PHP-FPM)
# - futsal-neo-s-nginx (Nginx)
```

### 7. Run Database Migrations
```bash
podman exec futsal-neo-s-app php artisan migrate
```

### 8. Seed Database
```bash
podman exec futsal-neo-s-app php artisan db:seed
```

### 9. Publish Livewire Assets
```bash
podman exec futsal-neo-s-app php artisan livewire:publish --assets
```

### 10. Clear All Caches
```bash
podman exec futsal-neo-s-app php artisan cache:clear
podman exec futsal-neo-s-app php artisan config:clear
podman exec futsal-neo-s-app php artisan view:clear
podman exec futsal-neo-s-app php artisan route:cache
```

### 11. Fix File Permissions
```bash
podman exec futsal-neo-s-app chown -R www-data:www-data /var/www/storage
podman exec futsal-neo-s-app chmod -R 775 /var/www/storage
podman exec futsal-neo-s-app chown -R www-data:www-data /var/www/bootstrap/cache
podman exec futsal-neo-s-app chmod -R 775 /var/www/bootstrap/cache
```

### 12. Rebuild Nginx to Connect Livewire
```bash
podman restart futsal-neo-s-nginx
```

### 13. Verify Deployment
```bash
# Test homepage
curl -k https://kelompok1-de.humahub.my.id/

# Test login page
curl -k https://kelompok1-de.humahub.my.id/login

# Test CSS assets loaded
curl -k https://kelompok1-de.humahub.my.id/build/assets/app-*.css

# Test Livewire JS
curl -k https://kelompok1-de.humahub.my.id/vendor/livewire/livewire.min.js
```

## Important Configuration Files

### 1. Dockerfile
**Location:** `Dockerfile`
**Purpose:** Multi-stage build untuk PHP-FPM container
**Key Points:**
- Stage 1: Composer dependencies (vendor folder)
- Stage 2: Node.js build (npm assets)
- Stage 3: PHP-FPM runtime (final image)

### 2. podman-compose.yml
**Location:** `podman-compose.yml`
**Services:**
- `app`: PHP-FPM container
- `db`: MySQL 8.0 database
- `nginx`: Nginx web server
**Networks:** futsal-network (internal), traefik-network (external)
**Volumes:** Persistent storage untuk database, app storage, bootstrap cache

### 3. Nginx Configuration
**Location:** `docker/nginx/conf.d/default.conf`
**Key Changes:**
- Added `/vendor` route untuk static Livewire assets
- Proper routing untuk /livewire/update endpoint
- Try files untuk static assets dan fallback ke PHP

### 4. Environment Configuration
**Location:** `.env`
**Production Settings:**
```
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:Sg2KZgy+mVVA+BNKe+VyARKQCGB/ElinKdeDea5RjnQ=
APP_URL=https://kelompok1-de.humahub.my.id
DB_CONNECTION=mysql
DB_HOST=db
DB_PASSWORD=Qwerty123.
DB_ROOT_PASSWORD=Qwerty123.
```

## Database Information

### Users (60 total)
```
Admin Accounts (3):
- admin1@example.com / password
- admin2@example.com / password
- admin3@example.com / password

Regular Users (57):
- user1@example.com / password
- user2@example.com / password
- ... (total 57 users)
```

### Tables
- users (60 rows)
- fields (2 rows: Lapangan A, Lapangan B)
- time_slots (14 rows: 08:00-22:00)
- bookings (490 rows)
- sessions (dynamic)
- cache & cache_locks (dynamic)
- jobs & job_batches (queue)
- migrations & password_reset_tokens

## Troubleshooting

### 1. 500 Error pada Dashboard
**Problem:** JULIANDAY function tidak support di MySQL
**Solution:** Gunakan TIME_TO_SEC() instead (sudah di-fix di app)

### 2. Login menampilkan 405 Method Not Allowed
**Problem:** Nginx routing error untuk /livewire endpoint
**Solution:** Pastikan nginx config sudah benar di `docker/nginx/conf.d/default.conf`

### 3. CSS tidak loaded / styling kosong
**Problem:** Build assets tidak ada atau volume mount issue
**Solution:**
```bash
npm run build
podman cp futsal-neo-s-app:/var/www/public/vendor ./public/
```

### 4. Session/CSRF Token Error (419)
**Problem:** Domain mismatch atau APP_URL salah
**Solution:** Pastikan APP_URL=https://kelompok1-de.humahub.my.id (lowercase)

### 5. Livewire JS tidak load (404)
**Problem:** /vendor/livewire/livewire.min.js tidak ditemukan
**Solution:**
```bash
podman exec futsal-neo-s-app php artisan livewire:publish --assets
podman cp futsal-neo-s-app:/var/www/public/vendor ./public/
```

## Useful Commands

### View Logs
```bash
# PHP-FPM logs
podman logs futsal-neo-s-app

# Nginx logs
podman logs futsal-neo-s-nginx

# MySQL logs
podman logs futsal-neo-s-db

# Laravel logs
podman exec futsal-neo-s-app tail -f storage/logs/laravel.log
```

### Container Management
```bash
# Stop services
podman-compose down

# Start services
podman-compose up -d

# Restart specific service
podman restart futsal-neo-s-app

# Execute command in container
podman exec futsal-neo-s-app php artisan [command]
```

### Database
```bash
# Connect to MySQL
podman exec -it futsal-neo-s-db mysql -u futsal_user -p futsal_booking

# Backup database
podman exec futsal-neo-s-db mysqldump -u futsal_user -p futsal_booking > backup.sql

# Restore database
podman exec -i futsal-neo-s-db mysql -u futsal_user -p futsal_booking < backup.sql
```

## Production Checklist

- [ ] Git repository cloned
- [ ] .env configured for production
- [ ] Dockerfile verified and built
- [ ] podman-compose.yml verified
- [ ] Nginx configuration correct
- [ ] Traefik network exists
- [ ] Services started: app, db, nginx
- [ ] Migrations run
- [ ] Database seeded
- [ ] Livewire assets published
- [ ] Caches cleared
- [ ] File permissions correct
- [ ] Nginx restarted
- [ ] All verification tests pass
- [ ] SSL certificate working
- [ ] Website accessible via domain

## Support

Jika ada error, check:
1. Container logs dengan `podman logs [service]`
2. Database connection dengan `podman exec app php artisan db:show`
3. Routes list dengan `podman exec app php artisan route:list`
4. Nginx config di `docker/nginx/conf.d/default.conf`
