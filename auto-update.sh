#!/bin/bash

# Auto-update and deployment script for booking-futsal (Podman version)
# This script pulls latest changes from GitHub and redeploys using podman-compose

PROJECT_DIR="/home/robby/stacks/prod/booking-futsal"
LOG_FILE="$PROJECT_DIR/auto-update.log"
COMPOSE_FILE="$PROJECT_DIR/podman-compose.yml"

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

log "=== Starting auto-update and deployment ==="

cd "$PROJECT_DIR" || exit 1

# Fetch latest changes from GitHub
log "Fetching latest changes from GitHub..."
git fetch origin main

# Check if there are new commits
CURRENT=$(git rev-parse HEAD)
REMOTE=$(git rev-parse origin/main)

if [ "$CURRENT" = "$REMOTE" ]; then
    log "Already up to date. No deployment needed."
    exit 0
fi

log "New commits found. Pulling changes..."
git pull origin main

if [ $? -ne 0 ]; then
    log "ERROR: Failed to pull changes"
    exit 1
fi

log "Changes pulled successfully. Rebuilding Podman containers..."

# Check if compose file exists
if [ ! -f "$COMPOSE_FILE" ]; then
    log "ERROR: Compose file not found at $COMPOSE_FILE"
    exit 1
fi

log "Stopping containers..."
podman-compose -f "$COMPOSE_FILE" down

log "Building new image and starting containers..."
podman-compose -f "$COMPOSE_FILE" up -d --build

if [ $? -eq 0 ]; then
    log "Deployment completed successfully! 🚀"
else
    log "ERROR: Deployment failed"
    exit 1
fi

log "=== Auto-update and deployment finished ==="
