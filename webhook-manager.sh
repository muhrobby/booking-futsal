#!/bin/bash

# Webhook service manager for Booking Futsal
PROJECT_DIR="/home/robby/stacks/prod/booking-futsal"
WEBHOOK_PY="$PROJECT_DIR/webhook_server.py"
WEBHOOK_LOG="$PROJECT_DIR/webhook_server.log"
PID_FILE="$PROJECT_DIR/.webhook_server.pid"

usage() {
    echo "Usage: $0 {start|stop|restart|status|logs}"
    echo ""
    echo "  start   - Start webhook server in background"
    echo "  stop    - Stop webhook server"
    echo "  restart - Restart webhook server"
    echo "  status  - Show webhook server status"
    echo "  logs    - Show webhook logs (tail -f)"
    exit 1
}

start() {
    if [ -f "$PID_FILE" ]; then
        PID=$(cat "$PID_FILE")
        if ps -p "$PID" > /dev/null 2>&1; then
            echo "❌ Webhook server already running (PID: $PID)"
            return 1
        fi
    fi
    
    echo "🚀 Starting webhook server..."
    nohup python3 "$WEBHOOK_PY" > "$WEBHOOK_LOG" 2>&1 &
    NEW_PID=$!
    echo $NEW_PID > "$PID_FILE"
    sleep 2
    
    if curl -s http://localhost:5000/health > /dev/null 2>&1; then
        echo "✅ Webhook server started successfully (PID: $NEW_PID)"
        return 0
    else
        echo "❌ Webhook server failed to start"
        rm -f "$PID_FILE"
        return 1
    fi
}

stop() {
    if [ -f "$PID_FILE" ]; then
        PID=$(cat "$PID_FILE")
        if ps -p "$PID" > /dev/null 2>&1; then
            echo "🛑 Stopping webhook server (PID: $PID)..."
            kill $PID
            sleep 2
            if ps -p "$PID" > /dev/null 2>&1; then
                echo "⚠️  Force killing..."
                kill -9 $PID
            fi
            rm -f "$PID_FILE"
            echo "✅ Webhook server stopped"
            return 0
        else
            echo "❌ Webhook server not running"
            rm -f "$PID_FILE"
            return 1
        fi
    else
        echo "❌ No PID file found"
        # Try to kill any running webhook_server processes
        if pkill -f webhook_server; then
            echo "✅ Killed webhook server processes"
        fi
        return 1
    fi
}

restart() {
    stop
    sleep 1
    start
}

status() {
    if [ -f "$PID_FILE" ]; then
        PID=$(cat "$PID_FILE")
        if ps -p "$PID" > /dev/null 2>&1; then
            echo "✅ Webhook server is running (PID: $PID)"
            
            if curl -s http://localhost:5000/health > /dev/null 2>&1; then
                echo "✅ Webhook server is responding on port 5000"
            else
                echo "⚠️  PID exists but server not responding"
            fi
            
            return 0
        else
            echo "❌ Webhook server not running (stale PID: $PID)"
            rm -f "$PID_FILE"
            return 1
        fi
    else
        if curl -s http://localhost:5000/health > /dev/null 2>&1; then
            echo "⚠️  Webhook server running but no PID file"
            echo "   Finding actual PID..."
            ps aux | grep webhook_server | grep -v grep
            return 0
        else
            echo "❌ Webhook server not running"
            return 1
        fi
    fi
}

logs() {
    if [ ! -f "$WEBHOOK_LOG" ]; then
        echo "❌ Log file not found: $WEBHOOK_LOG"
        return 1
    fi
    
    echo "📝 Following webhook logs (Press Ctrl+C to stop)..."
    echo ""
    tail -f "$WEBHOOK_LOG"
}

# Main
case "${1:-status}" in
    start)
        start
        ;;
    stop)
        stop
        ;;
    restart)
        restart
        ;;
    status)
        status
        ;;
    logs)
        logs
        ;;
    *)
        usage
        ;;
esac
