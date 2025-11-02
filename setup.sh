#!/bin/bash

# Setup script untuk auto-update booking-futsal (Podman version)
PROJECT_DIR="/home/robby/stacks/prod/booking-futsal"

echo "================================================"
echo "Setup Auto-Update untuk Booking Futsal (Podman)"
echo "================================================"
echo ""

# Check if webhook secret exists
if [ ! -f "$PROJECT_DIR/.webhook_secret" ]; then
    echo "❌ Webhook secret tidak ditemukan"
    exit 1
fi

WEBHOOK_SECRET=$(cat "$PROJECT_DIR/.webhook_secret")
echo "✓ Webhook secret: $WEBHOOK_SECRET"
echo ""

# Check Python3
if ! command -v python3 &> /dev/null; then
    echo "❌ Python3 tidak ditemukan"
    exit 1
fi
echo "✓ Python3 found: $(python3 --version)"
echo ""

# Check Git
if ! command -v git &> /dev/null; then
    echo "❌ Git tidak ditemukan"
    exit 1
fi
echo "✓ Git found: $(git --version)"
echo ""

# Check Podman
if ! command -v podman &> /dev/null; then
    echo "❌ Podman tidak ditemukan"
    exit 1
fi
echo "✓ Podman found: $(podman --version)"
echo ""

# Check podman-compose
if ! command -v podman-compose &> /dev/null; then
    echo "❌ Podman Compose tidak ditemukan"
    exit 1
fi
echo "✓ Podman Compose found: $(podman-compose --version | head -1)"
echo ""

echo "================================================"
echo "✅ Setup Prerequisites Berhasil!"
echo "================================================"
echo ""
echo "LANGKAH SELANJUTNYA:"
echo ""
echo "1. Dapatkan IP/Domain server Anda:"
echo "   curl ifconfig.me"
echo ""
echo "2. Setup GitHub Webhook:"
echo "   - Buka: https://github.com/muhrobby/booking-futsal/settings/hooks"
echo "   - Click 'Add webhook'"
echo "   - Payload URL: http://YOUR_IP:5000/webhook"
echo "   - Content type: application/json"
echo "   - Secret: $WEBHOOK_SECRET"
echo "   - Events: Just the push event"
echo ""
echo "3. Start webhook server (pilih salah satu):"
echo ""
echo "   OPTION A - Background (recommended):"
echo "   nohup python3 $PROJECT_DIR/webhook_server.py > $PROJECT_DIR/webhook_server.log 2>&1 &"
echo ""
echo "   OPTION B - Foreground (untuk testing):"
echo "   python3 $PROJECT_DIR/webhook_server.py"
echo ""
echo "4. Verify webhook server berjalan:"
echo "   curl http://localhost:5000/health"
echo ""
echo "5. Test auto-update (manual):"
echo "   bash $PROJECT_DIR/auto-update.sh"
echo ""
