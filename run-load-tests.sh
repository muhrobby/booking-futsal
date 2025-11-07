#!/bin/bash

# Load Testing Script for Booking Futsal
# This script runs comprehensive load tests and generates reports

echo "=========================================="
echo "Booking Futsal - Load Testing Suite"
echo "=========================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Check if server is running
echo -e "${YELLOW}Checking if Laravel server is running...${NC}"
if ! curl -s http://localhost:8000 > /dev/null; then
    echo -e "${RED}Error: Laravel server not running on http://localhost:8000${NC}"
    echo "Start the server with: php artisan serve"
    exit 1
fi
echo -e "${GREEN}✓ Server is running${NC}"
echo ""

# Test 1: Simple homepage load
echo -e "${YELLOW}Test 1: Homepage Load (Apache Bench)${NC}"
echo "Testing 500 requests with 50 concurrent users..."
ab -n 500 -c 50 http://localhost:8000/ > /tmp/ab-homepage.txt 2>&1
grep -E "Requests per second|Time per request|Failed" /tmp/ab-homepage.txt
echo ""

# Test 2: Artillery baseline
echo -e "${YELLOW}Test 2: Artillery - Quick Baseline${NC}"
echo "Running 5-minute load test..."
artillery quick --count 100 --num 1000 http://localhost:8000/

echo ""
echo -e "${GREEN}=========================================="
echo "Load Testing Complete!"
echo "==========================================${NC}"
echo ""
echo "For advanced testing, run:"
echo "  artillery run load-test.yml"
echo ""
echo "Generate HTML report:"
echo "  artillery report latest.json --output report.html"
echo ""
