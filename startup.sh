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

  # ---- Force-load AI credentials from .env ----
  # Azure App Service Application Settings can override .env with empty values.
  # We read directly from .env to guarantee config:cache bakes in the real keys.
  echo "Loading AI credentials from .env into environment..."
  if [ -f ".env" ]; then
    _load_env_var() {
      local key="$1"
      local val
      val=$(grep -m1 "^${key}=" .env | cut -d'=' -f2-)
      [ -n "$val" ] && export "$key"="$val" && echo "  Loaded ${key}"
    }
    _load_env_var AZURE_OPENAI_API_KEY
    _load_env_var AZURE_OPENAI_ENDPOINT
    _load_env_var AZURE_OPENAI_DEPLOYMENT_ID
    _load_env_var AZURE_OPENAI_API_VERSION
    _load_env_var AZURE_OPENAI_MODEL
    _load_env_var AZURE_ANTHROPIC_API_KEY
    _load_env_var AZURE_ANTHROPIC_ENDPOINT
    _load_env_var AZURE_ANTHROPIC_MODEL
    _load_env_var OPENAI_API_KEY
  fi

  echo "Running production optimizations..."
  timeout 60 php artisan config:cache || echo "WARNING: config:cache failed/timed-out"
  timeout 60 php artisan route:cache  || echo "WARNING: route:cache failed/timed-out"
  timeout 90 php artisan view:cache   || echo "WARNING: view:cache failed/timed-out"
  timeout 60 php artisan event:cache  || echo "WARNING: event:cache failed/timed-out"

  echo "Running migrations..."
  # NOTE: Timeout set to 120s to stay within Azure App Service 230s startup window
  timeout 120 php artisan migrate --force --no-interaction || echo "WARNING: migrations failed/timed-out"

  echo "Syncing Meilisearch indices..."
  timeout 30 php artisan scout:sync-index-settings 2>/dev/null || true

  # Seed test accounts once (idempotent - skips if already seeded)
  timeout 30 php artisan studai:seed-test-accounts 2>/dev/null || echo "WARNING: account seeder failed (non-critical)"

else
  echo "Development mode - clearing caches..."
  php artisan optimize:clear 2>/dev/null || true

  echo "Running migrations..."
  timeout 120 php artisan migrate --force --no-interaction || echo "WARNING: migrations failed/timed-out"
fi

# ---- 5. Filament & Icon Cache (with timeouts) ----
timeout 30 php artisan filament:cache-components 2>/dev/null || true
timeout 30 php artisan icons:cache 2>/dev/null || true

echo "========================================"
echo "Startup complete!"
echo "========================================"
