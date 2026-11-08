#!/bin/sh
set -eu

if [ -z "${APP_KEY:-}" ]; then
    echo "APP_KEY is required. Copy .env.example to .env and set APP_KEY." >&2
    exit 1
fi

if [ -z "${JWT_SECRET:-}" ]; then
    echo "JWT_SECRET is required. Set it in the root .env file." >&2
    exit 1
fi

php artisan migrate --force
php artisan db:seed --class=ProductSeeder --force

exec "$@"
