#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SQLITE_DB="$ROOT_DIR/database/database.sqlite"

cd "$ROOT_DIR"

if [ "$(id -u)" -eq 0 ]; then
  export COMPOSER_ALLOW_SUPERUSER=1
fi

composer install --no-interaction --prefer-dist

if [ ! -f .env ]; then
  cp .env.example .env
fi

php artisan key:generate --force
mkdir -p "$ROOT_DIR/database"
touch "$SQLITE_DB"

DB_CONNECTION=sqlite DB_DATABASE="$SQLITE_DB" php artisan migrate --force
DB_CONNECTION=sqlite DB_DATABASE="$SQLITE_DB" php artisan test
npm run types:check
npm run lint:check
npm run build

echo "Local validation completed successfully."
