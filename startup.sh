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
  echo "Running production optimizations..."
  php artisan config:cache || echo "WARNING: config:cache failed"
  php artisan route:cache || echo "WARNING: route:cache failed"
  php artisan view:cache || echo "WARNING: view:cache failed"
  php artisan event:cache || echo "WARNING: event:cache failed"

  # ---- DB Connection Diagnostic ----
  echo "Testing database connection..."
  echo "  DB_HOST=${DB_HOST:-not set}"
  echo "  DB_PORT=${DB_PORT:-3306}"
  echo "  DB_DATABASE=${DB_DATABASE:-not set}"
  echo "  DB_USERNAME=${DB_USERNAME:-not set}"
  echo "  MYSQL_ATTR_SSL_CA=${MYSQL_ATTR_SSL_CA:-not set}"
  php -r "
    try {
      \$opts = [PDO::MYSQL_ATTR_SSL_CA => getenv('MYSQL_ATTR_SSL_CA') ?: '/home/site/ssl/azure-mysql-ca-bundle.pem'];
      \$dsn = 'mysql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: '3306') . ';dbname=' . getenv('DB_DATABASE');
      \$pdo = new PDO(\$dsn, getenv('DB_USERNAME'), getenv('DB_PASSWORD'), \$opts);
      echo 'DB connection OK (SSL)' . PHP_EOL;
    } catch (Exception \$e) {
      echo 'DB connection FAILED: ' . \$e->getMessage() . PHP_EOL;
      // Try without SSL
      try {
        \$pdo2 = new PDO(\$dsn, getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
        echo 'DB connection OK (no SSL) - SSL cert may be wrong' . PHP_EOL;
      } catch (Exception \$e2) {
        echo 'DB connection FAILED (no SSL too): ' . \$e2->getMessage() . PHP_EOL;
      }
    }
  " 2>&1 || echo "WARNING: DB diagnostic failed"

  echo "Running migrations..."
  php artisan migrate --force --no-interaction || echo "WARNING: migrations failed"

  echo "Syncing Meilisearch indices..."
  php artisan scout:sync-index-settings 2>/dev/null || true
else
  echo "Development mode - clearing caches..."
  php artisan optimize:clear 2>/dev/null || true

  echo "Running migrations..."
  php artisan migrate --force --no-interaction || echo "WARNING: migrations failed"
fi

# ---- 5. Filament & Icon Cache ----
php artisan filament:cache-components 2>/dev/null || true
php artisan icons:cache 2>/dev/null || true

echo "========================================"
echo "Startup complete!"
echo "========================================"
