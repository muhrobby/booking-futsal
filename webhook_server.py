#!/usr/bin/env python3

import json
import subprocess
import hmac
import hashlib
import logging
import http.server
import socketserver
from threading import Thread

# Configure logging
logging.basicConfig(
    filename='/home/robby/stacks/prod/booking-futsal/webhook.log',
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)

PROJECT_DIR = '/home/robby/stacks/prod/booking-futsal'
SCRIPT_PATH = f'{PROJECT_DIR}/auto-update.sh'

# Read webhook secret from file
def get_webhook_secret():
    try:
        with open(f'{PROJECT_DIR}/.webhook_secret', 'r') as f:
            return f.read().strip()
    except:
        return 'your-secret-token'

WEBHOOK_SECRET = get_webhook_secret()

class WebhookHandler(http.server.BaseHTTPRequestHandler):
    
    def do_POST(self):
        content_length = int(self.headers.get('Content-Length', 0))
        payload_body = self.rfile.read(content_length)
        signature = self.headers.get('X-Hub-Signature-256', '')
        
        if self.path == '/webhook':
            # Verify GitHub signature
            if not self.verify_signature(payload_body, signature):
                logging.warning('Invalid webhook signature received')
                self.send_response(401)
                self.send_header('Content-type', 'application/json')
                self.end_headers()
                self.wfile.write(json.dumps({'status': 'invalid signature'}).encode())
                return
            
            try:
                data = json.loads(payload_body)
                
                # Only process push events to main branch
                if data.get('ref') == 'refs/heads/main':
                    pusher = data.get('pusher', {}).get('name', 'unknown')
                    logging.info(f'Received push event from {pusher}')
                    
                    # Run auto-update script in background
                    Thread(target=subprocess.run, args=(['bash', SCRIPT_PATH],)).start()
                    logging.info('Started auto-update script')
                    
                    self.send_response(200)
                    self.send_header('Content-type', 'application/json')
                    self.end_headers()
                    self.wfile.write(json.dumps({'status': 'ok', 'message': 'Update process started'}).encode())
                else:
                    logging.info(f'Ignoring push to {data.get("ref")}')
                    self.send_response(200)
                    self.send_header('Content-type', 'application/json')
                    self.end_headers()
                    self.wfile.write(json.dumps({'status': 'ignored', 'reason': 'not main branch'}).encode())
                    
            except Exception as e:
                logging.error(f'Error processing webhook: {str(e)}')
                self.send_response(500)
                self.send_header('Content-type', 'application/json')
                self.end_headers()
                self.wfile.write(json.dumps({'status': 'error', 'message': str(e)}).encode())
        
        elif self.path == '/health':
            self.send_response(200)
            self.send_header('Content-type', 'application/json')
            self.end_headers()
            self.wfile.write(json.dumps({'status': 'ok'}).encode())
        
        else:
            self.send_response(404)
            self.send_header('Content-type', 'application/json')
            self.end_headers()
            self.wfile.write(json.dumps({'status': 'not found'}).encode())
    
    def do_GET(self):
        if self.path == '/health':
            self.send_response(200)
            self.send_header('Content-type', 'application/json')
            self.end_headers()
            self.wfile.write(json.dumps({'status': 'ok'}).encode())
        else:
            self.send_response(404)
            self.send_header('Content-type', 'application/json')
            self.end_headers()
            self.wfile.write(json.dumps({'status': 'not found'}).encode())
    
    def verify_signature(self, payload_body, signature):
        if not signature:
            return False
        
        expected_signature = 'sha256=' + hmac.new(
            WEBHOOK_SECRET.encode(),
            payload_body,
            hashlib.sha256
        ).hexdigest()
        
        return hmac.compare_digest(signature, expected_signature)
    
    def log_message(self, format, *args):
        # Custom logging instead of default stderr
        logging.info(format % args)

if __name__ == '__main__':
    PORT = 5000
    handler = WebhookHandler
    
    with socketserver.TCPServer(("0.0.0.0", PORT), handler) as httpd:
        logging.info(f'Webhook server starting on port {PORT}...')
        print(f'🚀 Webhook server listening on port {PORT}')
        print(f'📝 Logs: {PROJECT_DIR}/webhook.log')
        try:
            httpd.serve_forever()
        except KeyboardInterrupt:
            logging.info('Webhook server shutting down...')
            print('\n✓ Webhook server stopped')
