#!/bin/bash

################################################################################
# CI/CD Setup Verification & Status Check
# Run this to verify everything is configured correctly
################################################################################

set -e

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

REPO_PATH="/home/muhrobby/Data/laravel/booking-futsal"
CHECKS_PASSED=0
CHECKS_FAILED=0

log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[✓]${NC} $1"
    ((CHECKS_PASSED++))
}

log_error() {
    echo -e "${RED}[✗]${NC} $1"
    ((CHECKS_FAILED++))
}

log_warning() {
    echo -e "${YELLOW}[!]${NC} $1"
}

################################################################################
# Check CI/CD Files
################################################################################

echo ""
echo -e "${BLUE}=========================================="
echo "CI/CD Setup Verification"
echo "==========================================${NC}"

log_info "Checking repository structure..."

# Check GitHub workflows directory
if [ -d "$REPO_PATH/.github/workflows" ]; then
    log_success "GitHub workflows directory exists"
else
    log_error "GitHub workflows directory not found"
fi

# Check deploy workflow
if [ -f "$REPO_PATH/.github/workflows/deploy.yml" ]; then
    log_success "Deploy workflow found (.github/workflows/deploy.yml)"
else
    log_error "Deploy workflow not found"
fi

# Check deployment scripts
if [ -f "$REPO_PATH/deploy.sh" ]; then
    if [ -x "$REPO_PATH/deploy.sh" ]; then
        log_success "Deploy script exists and is executable"
    else
        log_warning "Deploy script exists but not executable (run: chmod +x deploy.sh)"
    fi
else
    log_error "Deploy script not found"
fi

if [ -f "$REPO_PATH/setup-vps.sh" ]; then
    if [ -x "$REPO_PATH/setup-vps.sh" ]; then
        log_success "VPS setup script exists and is executable"
    else
        log_warning "VPS setup script exists but not executable (run: chmod +x setup-vps.sh)"
    fi
else
    log_error "VPS setup script not found"
fi

if [ -f "$REPO_PATH/health-check.sh" ]; then
    if [ -x "$REPO_PATH/health-check.sh" ]; then
        log_success "Health check script exists and is executable"
    else
        log_warning "Health check script exists but not executable (run: chmod +x health-check.sh)"
    fi
else
    log_error "Health check script not found"
fi

# Check documentation
if [ -f "$REPO_PATH/CICD_DEPLOYMENT.md" ]; then
    log_success "Deployment guide found (CICD_DEPLOYMENT.md)"
else
    log_warning "Deployment guide not found"
fi

if [ -f "$REPO_PATH/DEPLOYMENT_QUICK_START.md" ]; then
    log_success "Quick start guide found (DEPLOYMENT_QUICK_START.md)"
else
    log_warning "Quick start guide not found"
fi

# Check configuration examples
if [ -f "$REPO_PATH/.env.production.example" ]; then
    log_success "Production env template found (.env.production.example)"
else
    log_warning "Production env template not found"
fi

if [ -f "$REPO_PATH/nginx.conf.example" ]; then
    log_success "Nginx config example found (nginx.conf.example)"
else
    log_warning "Nginx config example not found"
fi

# Check Laravel health controller
if [ -f "$REPO_PATH/app/Http/Controllers/HealthController.php" ]; then
    log_success "Health controller created (app/Http/Controllers/HealthController.php)"
else
    log_warning "Health controller not found"
fi

################################################################################
# Check Routes
################################################################################

echo ""
log_info "Checking routes configuration..."

if grep -q "'/health'" "$REPO_PATH/routes/web.php"; then
    log_success "Health check routes configured in routes/web.php"
else
    log_warning "Health check routes not found in routes/web.php"
fi

if grep -q "HealthController" "$REPO_PATH/routes/web.php"; then
    log_success "HealthController imported in routes/web.php"
else
    log_warning "HealthController not imported"
fi

################################################################################
# Summary
################################################################################

echo ""
echo -e "${BLUE}=========================================="
echo "Summary"
echo "==========================================${NC}"

echo -e "Checks Passed: ${GREEN}$CHECKS_PASSED${NC}"
echo -e "Checks Failed: ${RED}$CHECKS_FAILED${NC}"

if [ $CHECKS_FAILED -eq 0 ]; then
    echo ""
    log_success "All checks passed! ✅"
    echo ""
    echo -e "${GREEN}Next steps:${NC}"
    echo "1. Push to GitHub:"
    echo "   git add ."
    echo "   git commit -m 'Add CI/CD pipeline'"
    echo "   git push origin main"
    echo ""
    echo "2. Setup VPS (first time only):"
    echo "   ssh root@your_vps_ip"
    echo "   curl -O https://raw.githubusercontent.com/yourusername/booking-futsal/main/setup-vps.sh"
    echo "   sudo bash setup-vps.sh"
    echo ""
    echo "3. Configure GitHub Secrets:"
    echo "   Go to: Settings > Secrets and variables > Actions"
    echo "   Add: DEPLOY_KEY, VPS_HOST, VPS_USER, VPS_PORT"
    echo ""
    echo "4. Deploy:"
    echo "   git push origin main"
    echo "   Monitor: https://github.com/yourusername/booking-futsal/actions"
    echo ""
    exit 0
else
    echo ""
    log_error "Some checks failed!"
    echo ""
    echo -e "${YELLOW}Please fix the issues above and try again${NC}"
    exit 1
fi
