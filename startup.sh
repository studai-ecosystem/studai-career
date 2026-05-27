#!/bin/bash
# StudAI Career - Azure App Service Startup Script
# NOTE: Do NOT use set -e here. A failed artisan command should NOT crash the container.

echo "========================================"
echo "StudAI Career - App Service Startup"
echo "$(date '+%Y-%m-%d %H:%M:%S')"
echo "========================================"

# ---- 1. Download Azure MySQL SSL Certificates ----
mkdir -p /home/site/ssl
SSL_DIR=/home/site/ssl
CA_BUNDLE="$SSL_DIR/azure-mysql-ca-bundle.pem"

# Download both DigiCert root CAs (Azure MySQL uses one or both depending on region)
if [ ! -f "$CA_BUNDLE" ]; then
  echo "Downloading MySQL SSL certificates..."
  curl -sfL https://dl.cacerts.digicert.com/DigiCertGlobalRootCA.crt.pem \
    -o "$SSL_DIR/DigiCertGlobalRootCA.crt.pem" \
    || echo "WARNING: Failed to download DigiCertGlobalRootCA"
  curl -sfL https://dl.cacerts.digicert.com/DigiCertGlobalRootG2.crt.pem \
    -o "$SSL_DIR/DigiCertGlobalRootG2.crt.pem" \
    || echo "WARNING: Failed to download DigiCertGlobalRootG2"
  # Create combined CA bundle
  cat "$SSL_DIR/DigiCertGlobalRootCA.crt.pem" "$SSL_DIR/DigiCertGlobalRootG2.crt.pem" > "$CA_BUNDLE" 2>/dev/null || true
fi

# Verify cert bundle exists and is non-empty
if [ -s "$CA_BUNDLE" ]; then
  echo "MySQL SSL CA bundle ready ($(wc -c < "$CA_BUNDLE") bytes)"
  export MYSQL_ATTR_SSL_CA="$CA_BUNDLE"
else
  echo "WARNING: MySQL SSL CA bundle missing or empty"
fi

# ---- 2. Set Document Root and Symlinks ----
cd /home/site/wwwroot

# Create storage directories if they don't exist
mkdir -p storage/framework/{sessions,views,cache/data}
mkdir -p storage/logs/{json,ai,agent,payments,security}
mkdir -p bootstrap/cache

# Set permissions
chmod -R 775 storage bootstrap/cache

# Create storage symlink
php artisan storage:link --force 2>/dev/null || true

# ---- 3. Apply Custom Nginx Configuration ----
# Copy config to /home/site/default for future container starts
# AND apply to running nginx with reload
if [ -f /home/site/wwwroot/nginx-azure.conf ]; then
  cp /home/site/wwwroot/nginx-azure.conf /home/site/default
  cp /home/site/wwwroot/nginx-azure.conf /etc/nginx/sites-available/default 2>/dev/null || true
  cp /home/site/wwwroot/nginx-azure.conf /etc/nginx/sites-enabled/default 2>/dev/null || true
  echo "Testing nginx config..."
  if nginx -t 2>&1; then
    echo "Nginx config valid, reloading..."
    nginx -s reload 2>&1 || service nginx reload 2>&1 || killall -HUP nginx 2>&1 || echo "WARNING: Could not reload nginx"
    echo "Nginx reloaded with custom config"
  else
    echo "WARNING: Nginx config test failed, keeping default config"
  fi
fi

# ---- 4. Laravel Optimization ----
if [ "$APP_ENV" = "production" ]; then
  echo "Clearing stale caches..."
  php artisan optimize:clear || true
  rm -f bootstrap/cache/config.php bootstrap/cache/routes*.php bootstrap/cache/events.php

  # Set APP_URL to HTTPS if not already set by Azure App Settings
  export APP_URL="${APP_URL:-https://studai-app-prod.azurewebsites.net}"

  echo "Running production optimizations (with 60s timeout each)..."
  timeout 60 php artisan config:cache || echo "WARNING: config:cache failed/timed-out"
  timeout 60 php artisan route:cache  || echo "WARNING: route:cache failed/timed-out"
  timeout 90 php artisan view:cache   || echo "WARNING: view:cache failed/timed-out"
  timeout 60 php artisan event:cache  || echo "WARNING: event:cache failed/timed-out"

  echo "Running migrations..."
  timeout 300 php artisan migrate --force --no-interaction || echo "WARNING: migrations failed/timed-out"

  echo "Syncing Meilisearch indices..."
  timeout 30 php artisan scout:sync-index-settings 2>/dev/null || true

  echo "Seeding default test accounts..."
  timeout 60 php /home/site/wwwroot/reset-password.php || echo "WARNING: account seeder failed (non-critical)"

  echo "Seeding subscription plans and resume templates..."
  timeout 30 php artisan db:seed --class=SubscriptionPlanSeeder --force 2>/dev/null || echo "WARNING: SubscriptionPlanSeeder failed (non-critical)"
  timeout 30 php artisan db:seed --class=ResumeTemplateSeeder --force 2>/dev/null || echo "WARNING: ResumeTemplateSeeder failed (non-critical)"
else
  echo "Development mode - clearing caches..."
  php artisan optimize:clear 2>/dev/null || true

  echo "Running migrations..."
  timeout 120 php artisan migrate --force --no-interaction || echo "WARNING: migrations failed/timed-out"
fi

# ---- 5. Filament & Icon Cache (with timeouts) ----
timeout 60 php artisan filament:cache-components 2>/dev/null || true
timeout 60 php artisan icons:cache 2>/dev/null || true

# ---- 6. PHP-FPM Opcache Pre-warming ----
# Send HTTP requests to warm up PHP-FPM opcache so first user requests are fast
# This avoids the 5-8 second cold start on first requests after deployment
if [ "$APP_ENV" = "production" ]; then
  echo "Pre-warming PHP-FPM opcache (avoids cold-start timeouts for users)..."
  # Wait for PHP-FPM/nginx to be fully ready
  sleep 5
  # Make multiple requests to warm different PHP-FPM workers
  for i in 1 2 3; do
    timeout 30 curl -sf http://127.0.0.1:8080/ > /dev/null 2>&1 && echo "Warmup request $i done" || echo "Warmup request $i skipped (non-critical)"
    sleep 1
  done
  echo "Opcache pre-warming complete"
fi

echo "========================================"
echo "Startup complete!"
echo "========================================"
