#!/bin/bash

################################################################################
# Zero Downtime Deployment Script for Laravel
# This script uses blue-green deployment strategy
################################################################################

set -e

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PROJECT_NAME="futsal"
PROJECT_PATH="/home/deploy/projects"
CURRENT_RELEASE="$PROJECT_PATH/current"
RELEASES_PATH="$PROJECT_PATH/releases"
SHARED_PATH="$PROJECT_PATH/shared"
REPO_URL="$1"  # Git repository URL (passed from workflow)
RELEASE_NAME=$(date +%Y%m%d_%H%M%S)
NEW_RELEASE="$RELEASES_PATH/$RELEASE_NAME"

# Nginx configuration
NGINX_CONFIG="/etc/nginx/sites-available/$PROJECT_NAME"
BLUE_PORT=8001
GREEN_PORT=8002
HEALTH_CHECK_URL="http://localhost"

################################################################################
# Functions
################################################################################

log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# Check if directory exists and create if not
ensure_dir() {
    if [ ! -d "$1" ]; then
        mkdir -p "$1"
        log_info "Created directory: $1"
    fi
}

# Clone/Update repository
clone_repository() {
    log_info "Cloning repository..."
    
    if [ ! -d "$NEW_RELEASE" ]; then
        mkdir -p "$NEW_RELEASE"
    fi
    
    # Clone the latest code
    git clone --depth 1 --branch main "$REPO_URL" "$NEW_RELEASE" 2>/dev/null || {
        # If depth clone fails, try without depth
        git clone --branch main "$REPO_URL" "$NEW_RELEASE"
    }
    
    log_success "Repository cloned successfully"
}

# Install dependencies
install_dependencies() {
    log_info "Installing dependencies..."
    
    cd "$NEW_RELEASE"
    
    # Install PHP dependencies
    composer install --no-dev --prefer-dist --optimize-autoloader --quiet
    log_success "Composer dependencies installed"
    
    # Install Node dependencies
    npm ci --prefer-offline --no-audit --quiet
    log_success "Node dependencies installed"
}

# Build frontend assets
build_assets() {
    log_info "Building frontend assets..."
    
    cd "$NEW_RELEASE"
    npm run build
    
    log_success "Frontend assets built successfully"
}

# Copy shared files and directories
copy_shared() {
    log_info "Copying shared files..."
    
    # Create shared directories if they don't exist
    ensure_dir "$SHARED_PATH/storage"
    ensure_dir "$SHARED_PATH/bootstrap/cache"
    ensure_dir "$SHARED_PATH/.env"
    
    # Copy .env if it exists
    if [ -f "$SHARED_PATH/.env" ]; then
        cp "$SHARED_PATH/.env" "$NEW_RELEASE/.env"
        log_success "Environment file copied"
    else
        log_warning "Shared .env not found. Please create it manually."
    fi
    
    # Link storage
    if [ -L "$NEW_RELEASE/storage" ]; then
        rm "$NEW_RELEASE/storage"
    fi
    ln -s "$SHARED_PATH/storage" "$NEW_RELEASE/storage"
    
    # Link bootstrap/cache
    mkdir -p "$NEW_RELEASE/bootstrap"
    if [ -L "$NEW_RELEASE/bootstrap/cache" ]; then
        rm "$NEW_RELEASE/bootstrap/cache"
    fi
    ln -s "$SHARED_PATH/bootstrap/cache" "$NEW_RELEASE/bootstrap/cache"
    
    log_success "Shared files linked successfully"
}

# Run database migrations
run_migrations() {
    log_info "Running database migrations..."
    
    cd "$NEW_RELEASE"
    
    php artisan migrate --force
    
    log_success "Migrations completed"
}

# Cache optimization
optimize_cache() {
    log_info "Optimizing application cache..."
    
    cd "$NEW_RELEASE"
    
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    log_success "Cache optimized"
}

# Health check
health_check() {
    log_info "Performing health checks..."
    
    # Wait for application to start
    sleep 2
    
    local max_attempts=30
    local attempt=1
    
    while [ $attempt -le $max_attempts ]; do
        if curl -s -f "$HEALTH_CHECK_URL" > /dev/null 2>&1; then
            log_success "Health check passed"
            return 0
        fi
        
        log_info "Health check attempt $attempt/$max_attempts..."
        sleep 2
        ((attempt++))
    done
    
    log_error "Health check failed after $max_attempts attempts"
    return 1
}

# Switch between blue and green
switch_deployment() {
    log_info "Switching deployment..."
    
    # Backup current .env to shared
    if [ -f "$CURRENT_RELEASE/.env" ]; then
        cp "$CURRENT_RELEASE/.env" "$SHARED_PATH/.env"
    fi
    
    # Remove old current symlink
    if [ -L "$CURRENT_RELEASE" ]; then
        rm "$CURRENT_RELEASE"
    fi
    
    # Point to new release
    ln -s "$NEW_RELEASE" "$CURRENT_RELEASE"
    
    log_success "Deployment switched successfully"
}

# Reload PHP-FPM
reload_php_fpm() {
    log_info "Reloading PHP-FPM..."
    
    sudo systemctl reload php8.2-fpm
    
    log_success "PHP-FPM reloaded"
}

# Reload Nginx
reload_nginx() {
    log_info "Reloading Nginx..."
    
    sudo nginx -t
    sudo systemctl reload nginx
    
    log_success "Nginx reloaded"
}

# Cleanup old releases (keep last 5)
cleanup_old_releases() {
    log_info "Cleaning up old releases..."
    
    cd "$RELEASES_PATH"
    
    # Count releases
    local release_count=$(ls -1 | wc -l)
    
    if [ $release_count -gt 5 ]; then
        # Remove oldest releases
        ls -1t | tail -n +6 | xargs -I {} rm -rf "$RELEASES_PATH/{}"
        log_success "Removed old releases"
    else
        log_info "No old releases to remove"
    fi
}

# Rollback to previous release
rollback() {
    log_error "Deployment failed, rolling back..."
    
    # Get previous release
    local previous_release=$(ls -1t "$RELEASES_PATH" | head -n 2 | tail -n 1)
    
    if [ -z "$previous_release" ]; then
        log_error "No previous release found for rollback"
        return 1
    fi
    
    # Switch back to previous
    rm "$CURRENT_RELEASE"
    ln -s "$RELEASES_PATH/$previous_release" "$CURRENT_RELEASE"
    
    reload_php_fpm
    reload_nginx
    
    log_success "Rolled back to: $previous_release"
    
    # Remove failed release
    rm -rf "$NEW_RELEASE"
    
    return 0
}

################################################################################
# Main Deployment Process
################################################################################

main() {
    log_info "=========================================="
    log_info "Starting deployment process"
    log_info "Release: $RELEASE_NAME"
    log_info "=========================================="
    
    # Check if directories exist
    ensure_dir "$PROJECT_PATH"
    ensure_dir "$RELEASES_PATH"
    ensure_dir "$SHARED_PATH"
    
    # Set error handling
    trap rollback ERR
    
    # Execute deployment steps
    clone_repository
    install_dependencies
    build_assets
    copy_shared
    run_migrations
    optimize_cache
    switch_deployment
    reload_php_fpm
    reload_nginx
    health_check
    
    # Cleanup
    cleanup_old_releases
    
    log_success "=========================================="
    log_success "Deployment completed successfully!"
    log_success "Website updated to: $RELEASE_NAME"
    log_success "Zero downtime deployment achieved ✅"
    log_success "=========================================="
}

# Run main function
main "$@"
