#!/bin/bash

################################################################################
# VPS Setup Script for Zero Downtime Deployment
# Run this once on your VPS to setup the deployment environment
################################################################################

set -e

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Configuration
PROJECT_NAME="futsal"
DEPLOY_USER="deploy"
PROJECT_PATH="/home/$DEPLOY_USER/projects/$PROJECT_NAME"

log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

################################################################################
# Check if running as root
################################################################################

if [ "$EUID" -ne 0 ]; then 
    log_error "This script must be run as root"
    exit 1
fi

################################################################################
# Update system
################################################################################

log_info "Updating system packages..."
apt-get update
apt-get upgrade -y
log_success "System updated"

################################################################################
# Install required packages
################################################################################

log_info "Installing required packages..."

apt-get install -y \
    curl \
    wget \
    git \
    zip \
    unzip \
    build-essential \
    supervisor

log_success "Required packages installed"

################################################################################
# Install PHP and extensions
################################################################################

log_info "Installing PHP 8.2..."

apt-get install -y \
    php8.2-cli \
    php8.2-fpm \
    php8.2-common \
    php8.2-mysql \
    php8.2-pgsql \
    php8.2-sqlite3 \
    php8.2-curl \
    php8.2-gd \
    php8.2-xml \
    php8.2-mbstring \
    php8.2-zip \
    php8.2-bcmath \
    php8.2-redis

log_success "PHP 8.2 installed"

################################################################################
# Install Composer
################################################################################

log_info "Installing Composer..."

curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

log_success "Composer installed"

################################################################################
# Install Node.js
################################################################################

log_info "Installing Node.js..."

curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt-get install -y nodejs

log_success "Node.js installed"

################################################################################
# Install Nginx
################################################################################

log_info "Installing Nginx..."

apt-get install -y nginx

systemctl enable nginx
systemctl start nginx

log_success "Nginx installed"

################################################################################
# Install PostgreSQL (optional - adjust for your database)
################################################################################

log_info "Installing PostgreSQL..."

apt-get install -y postgresql postgresql-contrib

systemctl enable postgresql
systemctl start postgresql

log_success "PostgreSQL installed"

################################################################################
# Create deploy user
################################################################################

log_info "Creating deploy user..."

if ! id "$DEPLOY_USER" &>/dev/null; then
    useradd -m -s /bin/bash "$DEPLOY_USER"
    log_success "Deploy user created"
else
    log_info "Deploy user already exists"
fi

################################################################################
# Setup project directories
################################################################################

log_info "Setting up project directories..."

mkdir -p "$PROJECT_PATH"/{releases,shared,shared/{storage,bootstrap/cache}}

# Create .env in shared
cat > "$PROJECT_PATH/shared/.env" << 'EOF'
APP_NAME="Futsal Neo S"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=futsal
DB_USERNAME=futsal
DB_PASSWORD=changeme

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=cookie

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINTS=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1
EOF

chown -R "$DEPLOY_USER:$DEPLOY_USER" "$PROJECT_PATH"
chmod -R 755 "$PROJECT_PATH"
chmod -R 775 "$PROJECT_PATH/shared/storage"
chmod -R 775 "$PROJECT_PATH/shared/bootstrap/cache"

log_success "Project directories created"

################################################################################
# Configure PHP-FPM
################################################################################

log_info "Configuring PHP-FPM..."

# Create PHP-FPM pool for Laravel
cat > /etc/php/8.2/fpm/pool.d/futsal.conf << EOF
[$PROJECT_NAME]
user = $DEPLOY_USER
group = www-data

listen = /run/php/php8.2-$PROJECT_NAME.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

pm = dynamic
pm.max_children = 20
pm.start_servers = 5
pm.min_spare_servers = 2
pm.max_spare_servers = 10

chdir = $PROJECT_PATH/current/public
catch_workers_output = yes
EOF

systemctl reload php8.2-fpm

log_success "PHP-FPM configured"

################################################################################
# Configure Nginx
################################################################################

log_info "Configuring Nginx..."

cat > /etc/nginx/sites-available/$PROJECT_NAME << 'EOF'
upstream futsal_backend {
    server unix:/run/php/php8.2-futsal.sock;
}

server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;

    root /home/deploy/projects/futsal/current/public;
    index index.php;

    # Redirect HTTP to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;

    root /home/deploy/projects/futsal/current/public;
    index index.php;

    # SSL certificates (generate with Let's Encrypt)
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;

    # SSL configuration
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml text/javascript 
               application/json application/javascript application/xml+rss 
               application/rss+xml font/truetype font/opentype 
               application/vnd.ms-fontobject image/svg+xml;

    # Client upload size
    client_max_body_size 100M;

    # Logging
    access_log /var/log/nginx/futsal_access.log;
    error_log /var/log/nginx/futsal_error.log;

    # Deny access to sensitive files
    location ~ /\.env {
        deny all;
        access_log off;
        log_not_found off;
    }

    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }

    # Laravel routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP handling
    location ~ \.php$ {
        fastcgi_pass futsal_backend;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_buffering off;
        fastcgi_request_buffering off;
    }

    # Cache static files
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
EOF

# Enable site
ln -sf /etc/nginx/sites-available/$PROJECT_NAME /etc/nginx/sites-enabled/

# Test nginx config
nginx -t

log_success "Nginx configured"

################################################################################
# Generate SSL certificates with Let's Encrypt
################################################################################

log_info "Installing Certbot for SSL..."

apt-get install -y certbot python3-certbot-nginx

log_warning "Please run: certbot certonly --nginx -d yourdomain.com"
log_warning "Then update the SSL paths in /etc/nginx/sites-available/futsal"

################################################################################
# Setup SSH key for GitHub
################################################################################

log_info "Setting up SSH key for deployment..."

su - $DEPLOY_USER << 'EOF'
ssh-keygen -t rsa -b 4096 -f ~/.ssh/id_rsa -N ""
echo ""
echo "Add this public key to your GitHub repository deploy keys:"
cat ~/.ssh/id_rsa.pub
EOF

log_warning "Copy the public key above to GitHub Settings > Deploy Keys"

################################################################################
# Setup Supervisor for queue workers
################################################################################

log_info "Setting up Supervisor..."

cat > /etc/supervisor/conf.d/futsal.conf << EOF
[program:futsal-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/$DEPLOY_USER/projects/$PROJECT_NAME/current/artisan queue:work redis --sleep=3 --tries=3 --timeout=90
autostart=true
autorestart=true
user=$DEPLOY_USER
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/supervisor/futsal-worker.log
stopwaitsecs=3600
EOF

supervisorctl reread
supervisorctl update

log_success "Supervisor configured"

################################################################################
# Setup database
################################################################################

log_info "Setting up database..."

sudo -u postgres psql << EOF
CREATE ROLE futsal WITH LOGIN PASSWORD 'changeme';
CREATE DATABASE futsal OWNER futsal;
ALTER ROLE futsal CREATEDB;
EOF

log_warning "Update DB_PASSWORD in .env file with: changeme"

################################################################################
# Setup cron jobs
################################################################################

log_info "Setting up cron jobs..."

cat > /tmp/crontab_futsal << EOF
* * * * * cd /home/$DEPLOY_USER/projects/$PROJECT_NAME/current && php artisan schedule:run >> /dev/null 2>&1
0 2 * * * cd /home/$DEPLOY_USER/projects/$PROJECT_NAME/current && php artisan backup:run --only-files
0 3 * * 0 cd /home/$DEPLOY_USER/projects/$PROJECT_NAME/current && php artisan model:prune
EOF

su - $DEPLOY_USER -c "crontab /tmp/crontab_futsal"
rm /tmp/crontab_futsal

log_success "Cron jobs configured"

################################################################################
# Final steps
################################################################################

log_success "=========================================="
log_success "VPS Setup Complete!"
log_success "=========================================="
echo ""
echo -e "${YELLOW}Next steps:${NC}"
echo "1. Update DNS records to point to this VPS"
echo "2. Setup SSL certificate:"
echo "   certbot certonly --nginx -d yourdomain.com"
echo "3. Update these files with your domain:"
echo "   - /etc/nginx/sites-available/$PROJECT_NAME"
echo "4. Update .env file:"
echo "   sudo nano $PROJECT_PATH/shared/.env"
echo "5. Configure GitHub Secrets:"
echo "   - DEPLOY_KEY (SSH private key from ~/.ssh/id_rsa)"
echo "   - VPS_HOST (your VPS IP or domain)"
echo "   - VPS_USER (deploy)"
echo "   - VPS_PORT (22, or your custom SSH port)"
echo "6. Push code to GitHub to trigger deployment"
echo ""
log_success "Setup finished!"
