#!/bin/bash

################################################################################
# Application Health Check & Monitoring Script
# Run this periodically to monitor application status
################################################################################

set -e

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Configuration
PROJECT_PATH="/home/deploy/projects/futsal"
CURRENT_RELEASE="$PROJECT_PATH/current"
LOG_FILE="/var/log/futsal-health-check.log"
ALERT_EMAIL="admin@yourdomain.com"
HEALTH_CHECK_URL="https://yourdomain.com/health"
DB_HOST="127.0.0.1"
DB_USER="futsal"
DB_NAME="futsal"

################################################################################
# Functions
################################################################################

log_info() {
    local message="[$(date '+%Y-%m-%d %H:%M:%S')] [INFO] $1"
    echo -e "${BLUE}$message${NC}"
    echo "$message" >> "$LOG_FILE"
}

log_success() {
    local message="[$(date '+%Y-%m-%d %H:%M:%S')] [SUCCESS] $1"
    echo -e "${GREEN}$message${NC}"
    echo "$message" >> "$LOG_FILE"
}

log_warning() {
    local message="[$(date '+%Y-%m-%d %H:%M:%S')] [WARNING] $1"
    echo -e "${YELLOW}$message${NC}"
    echo "$message" >> "$LOG_FILE"
}

log_error() {
    local message="[$(date '+%Y-%m-%d %H:%M:%S')] [ERROR] $1"
    echo -e "${RED}$message${NC}"
    echo "$message" >> "$LOG_FILE"
}

send_alert() {
    local subject="$1"
    local message="$2"
    
    echo "$message" | mail -s "$subject" "$ALERT_EMAIL" 2>/dev/null || true
}

################################################################################
# Health Checks
################################################################################

check_disk_space() {
    log_info "Checking disk space..."
    
    local available=$(df /home | tail -1 | awk '{print $4}')
    local usage=$(df /home | tail -1 | awk '{print $5}' | sed 's/%//')
    
    if [ "$usage" -gt 90 ]; then
        log_error "Disk space critically low: ${usage}% used"
        send_alert "Critical: Low Disk Space" "Disk usage: ${usage}%\nAvailable: ${available}KB"
        return 1
    elif [ "$usage" -gt 80 ]; then
        log_warning "Disk space high: ${usage}% used"
    else
        log_success "Disk space OK: ${usage}% used"
    fi
}

check_application_running() {
    log_info "Checking if application is running..."
    
    if [ ! -L "$CURRENT_RELEASE" ]; then
        log_error "Current release symlink not found"
        return 1
    fi
    
    local release_path=$(readlink "$CURRENT_RELEASE")
    
    if [ ! -d "$release_path" ]; then
        log_error "Release directory not found: $release_path"
        return 1
    fi
    
    log_success "Application path OK: $release_path"
}

check_web_server() {
    log_info "Checking Nginx..."
    
    if sudo systemctl is-active --quiet nginx; then
        log_success "Nginx is running"
    else
        log_error "Nginx is not running"
        send_alert "Critical: Nginx Down" "Nginx service is not running on $(hostname)"
        return 1
    fi
    
    # Check if ports are listening
    if netstat -tlnp 2>/dev/null | grep -q ":80 " && netstat -tlnp 2>/dev/null | grep -q ":443 "; then
        log_success "Nginx ports (80, 443) are listening"
    else
        log_error "Nginx ports not listening properly"
        return 1
    fi
}

check_php_fpm() {
    log_info "Checking PHP-FPM..."
    
    if sudo systemctl is-active --quiet php8.2-fpm; then
        log_success "PHP-FPM is running"
    else
        log_error "PHP-FPM is not running"
        send_alert "Critical: PHP-FPM Down" "PHP-FPM service is not running on $(hostname)"
        return 1
    fi
}

check_database() {
    log_info "Checking database connection..."
    
    # Try to connect to PostgreSQL
    if PGPASSWORD="$(grep DB_PASSWORD $PROJECT_PATH/shared/.env | cut -d '=' -f2)" \
       psql -h "$DB_HOST" -U "$DB_USER" -d "$DB_NAME" -c "SELECT 1" > /dev/null 2>&1; then
        log_success "Database connection OK"
    else
        log_error "Database connection failed"
        send_alert "Critical: Database Down" "Cannot connect to database on $DB_HOST"
        return 1
    fi
}

check_http_response() {
    log_info "Checking HTTP response..."
    
    local response_code=$(curl -s -o /dev/null -w "%{http_code}" \
        --connect-timeout 5 \
        --max-time 10 \
        "$HEALTH_CHECK_URL" 2>/dev/null || echo "000")
    
    if [ "$response_code" == "200" ]; then
        log_success "HTTP health check passed (200 OK)"
    else
        log_error "HTTP health check failed (Response: $response_code)"
        send_alert "Warning: HTTP Health Check Failed" "Response code: $response_code\nURL: $HEALTH_CHECK_URL"
        return 1
    fi
}

check_storage_permissions() {
    log_info "Checking storage directory permissions..."
    
    local storage_path="$CURRENT_RELEASE/storage"
    
    if [ ! -d "$storage_path" ]; then
        log_error "Storage directory not found"
        return 1
    fi
    
    # Check write permissions
    if touch "$storage_path/.write_test" 2>/dev/null; then
        rm -f "$storage_path/.write_test"
        log_success "Storage directory is writable"
    else
        log_error "Storage directory is not writable"
        send_alert "Warning: Storage Not Writable" "Check permissions for $storage_path"
        return 1
    fi
}

check_memory_usage() {
    log_info "Checking memory usage..."
    
    local memory_usage=$(free | grep Mem | awk '{printf("%.2f", $3/$2 * 100.0)}')
    
    if (( $(echo "$memory_usage > 90" | bc -l) )); then
        log_error "Memory usage critically high: ${memory_usage}%"
        send_alert "Critical: High Memory Usage" "Memory usage: ${memory_usage}%"
        return 1
    elif (( $(echo "$memory_usage > 80" | bc -l) )); then
        log_warning "Memory usage high: ${memory_usage}%"
    else
        log_success "Memory usage OK: ${memory_usage}%"
    fi
}

check_cpu_load() {
    log_info "Checking CPU load..."
    
    local load_avg=$(uptime | awk -F'load average:' '{print $2}' | awk '{print $1}')
    local cpu_count=$(nproc)
    
    # Compare load average with CPU count
    if (( $(echo "$load_avg > $cpu_count * 2" | bc -l) )); then
        log_warning "CPU load high: $load_avg (CPU count: $cpu_count)"
    else
        log_success "CPU load OK: $load_avg (CPU count: $cpu_count)"
    fi
}

check_queue_workers() {
    log_info "Checking queue workers..."
    
    if ps aux | grep -q "[p]hp.*artisan queue:work"; then
        local worker_count=$(ps aux | grep "[p]hp.*artisan queue:work" | wc -l)
        log_success "Queue workers running: $worker_count processes"
    else
        log_warning "No queue workers running"
        send_alert "Warning: Queue Workers Down" "No queue workers detected on $(hostname)"
        return 1
    fi
}

check_logs() {
    log_info "Checking application logs for errors..."
    
    local laravel_log="$CURRENT_RELEASE/storage/logs/laravel.log"
    
    if [ -f "$laravel_log" ]; then
        # Count ERROR lines in last 1 hour
        local error_count=$(grep -c "ERROR\|CRITICAL" "$laravel_log" 2>/dev/null | tail -1000 || echo "0")
        
        if [ "$error_count" -gt 10 ]; then
            log_warning "Found $error_count error lines in logs"
            send_alert "Warning: Many Errors in Logs" "Found $error_count error lines in application logs"
        else
            log_success "Application logs OK"
        fi
    fi
}

check_latest_release() {
    log_info "Checking latest deployment..."
    
    # Get latest release timestamp
    if [ -L "$CURRENT_RELEASE" ]; then
        local current=$(basename "$(readlink "$CURRENT_RELEASE")")
        local last_modified=$(stat -c %y "$CURRENT_RELEASE" | cut -d' ' -f1-2)
        
        log_success "Current release: $current (Updated: $last_modified)"
    else
        log_error "Cannot determine current release"
        return 1
    fi
}

################################################################################
# Main Health Check
################################################################################

main() {
    log_info "=========================================="
    log_info "Starting Application Health Check"
    log_info "Host: $(hostname)"
    log_info "=========================================="
    
    local failed_checks=0
    
    # Run all checks
    check_disk_space || ((failed_checks++))
    check_application_running || ((failed_checks++))
    check_web_server || ((failed_checks++))
    check_php_fpm || ((failed_checks++))
    check_database || ((failed_checks++))
    check_http_response || ((failed_checks++))
    check_storage_permissions || ((failed_checks++))
    check_memory_usage || ((failed_checks++))
    check_cpu_load || ((failed_checks++))
    check_queue_workers || ((failed_checks++))
    check_logs || ((failed_checks++))
    check_latest_release || ((failed_checks++))
    
    # Summary
    log_info "=========================================="
    
    if [ $failed_checks -eq 0 ]; then
        log_success "All health checks passed! ✅"
        echo "0"
        exit 0
    else
        log_error "Health checks failed: $failed_checks checks"
        echo "$failed_checks"
        exit 1
    fi
}

# Run main function
main "$@"
